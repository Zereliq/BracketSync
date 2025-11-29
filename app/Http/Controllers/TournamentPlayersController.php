<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamUser;
use App\Models\Tournament;
use App\Models\TournamentPlayer;
use Illuminate\Http\Request;

class TournamentPlayersController extends Controller
{
    private const DISALLOWED_STAFF_ROLES = ['Referee', 'Streamer', 'Commentator'];

    public function showPublic(Tournament $tournament)
    {
        $data = $this->loadPlayersData($tournament);

        return view('tournaments.show', array_merge($data, [
            'tournament' => $tournament,
            'currentTab' => 'players',
        ]));
    }

    public function showDashboard(Tournament $tournament)
    {
        $data = $this->loadPlayersData($tournament);

        return view('tournaments.show', array_merge($data, [
            'tournament' => $tournament,
            'currentTab' => 'players',
            'isTeamTournament' => $tournament->isTeamTournament(),
            'isDashboard' => true,
        ]));
    }

    public function signupPublic(Request $request, Tournament $tournament)
    {
        if (! auth()->check()) {
            return redirect()->route('auth.osu.redirect')
                ->with('error', 'Please log in with osu! to sign up.');
        }

        $user = auth()->user();

        $validation = $this->validateSignup($tournament, $user);
        if ($validation !== true) {
            return back()->with('error', $validation);
        }

        $isTeamTournament = $tournament->isTeamTournament();
        $lookingForTeam = $request->boolean('looking_for_team', false);

        // For solo tournaments (1v1), just register the player
        if (! $isTeamTournament) {
            TournamentPlayer::create([
                'tournament_id' => $tournament->id,
                'user_id' => $user->id,
                'looking_for_team' => false,
            ]);

            // If tournament is invitational, mark the invitation as accepted
            if ($tournament->signup_method === 'invitationals') {
                \App\Models\TournamentInvitation::where('tournament_id', $tournament->id)
                    ->where('user_id', $user->id)
                    ->where('status', 'pending')
                    ->update(['status' => 'accepted']);
            }

            return back()->with('success', 'Successfully registered for the tournament!');
        }

        // For team tournaments, register the player (teams created separately in Teams tab)
        TournamentPlayer::create([
            'tournament_id' => $tournament->id,
            'user_id' => $user->id,
            'looking_for_team' => $lookingForTeam,
        ]);

        // If tournament is invitational, mark the invitation as accepted
        if ($tournament->signup_method === 'invitationals') {
            \App\Models\TournamentInvitation::where('tournament_id', $tournament->id)
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->update(['status' => 'accepted']);
        }

        $message = $lookingForTeam
            ? 'Successfully registered! You\'re marked as looking for a team.'
            : 'Successfully registered! You can join or create a team in the Teams tab.';

        return back()->with('success', $message);
    }

    public function withdrawPublic(Tournament $tournament)
    {
        if (! auth()->check()) {
            return back()->with('error', 'Please log in to withdraw.');
        }

        $user = auth()->user();

        if (! $tournament->signupsOpen()) {
            return back()->with('error', 'Signups are closed, cannot withdraw now.');
        }

        // Check if player is registered
        $registration = TournamentPlayer::where('tournament_id', $tournament->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $registration) {
            return back()->with('error', 'You are not registered for this tournament.');
        }

        // If they're on a team, remove them from the team
        $teamUser = TeamUser::whereHas('team', function ($query) use ($tournament) {
            $query->where('tournament_id', $tournament->id);
        })->where('user_id', $user->id)->first();

        if ($teamUser) {
            $team = $teamUser->team;
            $teamUser->delete();

            // Delete team if empty
            if ($team->members()->count() === 0) {
                $team->delete();
            }
        }

        // Remove registration
        $registration->delete();

        return back()->with('success', 'Successfully withdrawn from the tournament.');
    }

    public function removePlayer(Tournament $tournament, TournamentPlayer $player)
    {
        if (! auth()->check()) {
            return back()->with('error', 'Unauthorized.');
        }

        // Check if user has permission to edit players
        if (! auth()->user()->can('editPlayers', $tournament)) {
            return back()->with('error', 'You do not have permission to remove players.');
        }

        // Verify the player belongs to this tournament
        if ($player->tournament_id !== $tournament->id) {
            abort(404);
        }

        $playerName = $player->user->name ?? 'Player';

        // If they're on a team, remove them from the team
        $teamUser = TeamUser::whereHas('team', function ($query) use ($tournament) {
            $query->where('tournament_id', $tournament->id);
        })->where('user_id', $player->user_id)->first();

        if ($teamUser) {
            $team = $teamUser->team;
            $teamUser->delete();

            // Delete team if empty
            if ($team->members()->count() === 0) {
                $team->delete();
            }
        }

        // Remove registration
        $player->delete();

        return back()->with('success', "Successfully removed {$playerName} from the tournament.");
    }

    private function loadPlayersData(Tournament $tournament): array
    {
        $isTeamTournament = $tournament->isTeamTournament();

        if ($isTeamTournament) {
            $tournament->load([
                'creator',
                'tournamentRoleLinks.user',
                'tournamentRoleLinks.role',
                'teams.members',
            ]);
        } else {
            $tournament->load([
                'creator',
                'tournamentRoleLinks.user',
                'tournamentRoleLinks.role',
                'registeredPlayers.user',
            ]);
        }

        $user = auth()->user();
        $signedUp = false;
        $canSignup = false;
        $signupError = null;
        $userIsStaff = false;
        $userCanPlayAsStaff = true;

        $pendingInvitation = null;
        $pendingInvitations = collect();

        if ($user) {
            // Check if user is registered
            $signedUp = TournamentPlayer::where('tournament_id', $tournament->id)
                ->where('user_id', $user->id)
                ->exists();

            // Check for pending invitation
            $pendingInvitation = \App\Models\TournamentInvitation::where('tournament_id', $tournament->id)
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->with('inviter')
                ->first();

            $staffRole = $tournament->tournamentRoleLinks()
                ->where('user_id', $user->id)
                ->with('role')
                ->first();

            if ($staffRole) {
                $userIsStaff = true;
                $roleName = $staffRole->role->name ?? '';
                if (in_array($roleName, self::DISALLOWED_STAFF_ROLES)) {
                    $userCanPlayAsStaff = false;
                    $signupError = "Staff members with the role '{$roleName}' cannot participate as players.";
                }
            }

            if ($userCanPlayAsStaff) {
                $validation = $this->validateSignup($tournament, $user);
                if ($validation === true) {
                    $canSignup = true;
                } else {
                    $signupError = $validation;
                }
            }

            // Load pending invitations for staff
            if ($user->can('editPlayers', $tournament)) {
                $pendingInvitations = \App\Models\TournamentInvitation::where('tournament_id', $tournament->id)
                    ->where('status', 'pending')
                    ->with(['user', 'inviter'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        } else {
            $signupError = 'Please log in with osu! to sign up.';
        }

        return [
            'teams' => $tournament->teams ?? collect(),
            'registeredPlayers' => $tournament->registeredPlayers ?? collect(),
            'signedUp' => $signedUp,
            'canSignup' => $canSignup,
            'signupError' => $signupError,
            'isTeamTournament' => $isTeamTournament,
            'userIsStaff' => $userIsStaff,
            'userCanPlayAsStaff' => $userCanPlayAsStaff,
            'pendingInvitation' => $pendingInvitation,
            'pendingInvitations' => $pendingInvitations,
        ];
    }

    private function validateSignup(Tournament $tournament, $user): string|bool
    {
        if (! $tournament->signupsOpen()) {
            return 'Signups are not open right now.';
        }

        if (! in_array($tournament->status, ['draft', 'announced'])) {
            return 'Signups are closed for this tournament.';
        }

        // Check if tournament is invitational
        if ($tournament->signup_method === 'invitationals') {
            $hasInvitation = \App\Models\TournamentInvitation::where('tournament_id', $tournament->id)
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->exists();

            if (! $hasInvitation) {
                return 'This is an invitational tournament. You need an invitation to participate.';
            }
        }

        $staffRole = $tournament->tournamentRoleLinks()
            ->where('user_id', $user->id)
            ->with('role')
            ->first();

        if ($staffRole) {
            $roleName = $staffRole->role->name ?? '';
            if (in_array($roleName, self::DISALLOWED_STAFF_ROLES)) {
                return "Staff members with the role '{$roleName}' cannot participate as players.";
            }
        }

        if ($tournament->country_restriction_type === 'whitelist') {
            if (! in_array($user->country_code, $tournament->country_list ?? [])) {
                return 'Your country is not allowed to participate in this tournament.';
            }
        }

        if ($tournament->country_restriction_type === 'blacklist') {
            if (in_array($user->country_code, $tournament->country_list ?? [])) {
                return 'Your country is not allowed to participate in this tournament.';
            }
        }

        $alreadySignedUp = TournamentPlayer::where('tournament_id', $tournament->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadySignedUp) {
            return 'You are already registered for this tournament.';
        }

        return true;
    }
}
