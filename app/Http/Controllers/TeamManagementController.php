<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\TeamUser;
use App\Models\Tournament;
use App\Models\TournamentPlayer;
use Illuminate\Http\Request;

class TeamManagementController extends Controller
{
    public function store(Request $request, Tournament $tournament)
    {
        $request->validate([
            'teamname' => 'required|string|max:255',
        ]);

        $user = auth()->user();

        // Verify user is registered for this tournament
        $registered = TournamentPlayer::where('tournament_id', $tournament->id)
            ->where('user_id', $user->id)
            ->exists();

        if (! $registered) {
            return back()->with('error', 'You must be registered for the tournament to create a team.');
        }

        // Check if user is already on a team for this tournament
        $alreadyOnTeam = TeamUser::whereHas('team', function ($query) use ($tournament) {
            $query->where('tournament_id', $tournament->id);
        })->where('user_id', $user->id)->exists();

        if ($alreadyOnTeam) {
            return back()->with('error', 'You are already on a team for this tournament.');
        }

        // Create the team
        $team = Team::create([
            'tournament_id' => $tournament->id,
            'teamname' => $request->teamname,
        ]);

        // Add creator as captain
        TeamUser::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'is_captain' => true,
        ]);

        return back()->with('success', 'Team created successfully!');
    }

    public function invite(Request $request, Team $team)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = auth()->user();

        // Check if user is captain of this team
        $isCaptain = TeamUser::where('team_id', $team->id)
            ->where('user_id', $user->id)
            ->where('is_captain', true)
            ->exists();

        if (! $isCaptain) {
            return back()->with('error', 'Only team captains can invite players.');
        }

        $invitedUserId = $request->user_id;

        // Check if user is registered for this tournament
        $registered = TournamentPlayer::where('tournament_id', $team->tournament_id)
            ->where('user_id', $invitedUserId)
            ->exists();

        if (! $registered) {
            return back()->with('error', 'This player is not registered for the tournament.');
        }

        // Check if user is already on a team
        $alreadyOnTeam = TeamUser::whereHas('team', function ($query) use ($team) {
            $query->where('tournament_id', $team->tournament_id);
        })->where('user_id', $invitedUserId)->exists();

        if ($alreadyOnTeam) {
            return back()->with('error', 'This player is already on a team.');
        }

        // Check if invitation already exists
        $existingInvite = TeamInvitation::where('team_id', $team->id)
            ->where('user_id', $invitedUserId)
            ->where('status', 'pending')
            ->exists();

        if ($existingInvite) {
            return back()->with('error', 'This player has already been invited.');
        }

        // Create invitation
        TeamInvitation::create([
            'team_id' => $team->id,
            'user_id' => $invitedUserId,
            'invited_by' => $user->id,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Invitation sent successfully!');
    }

    public function acceptInvitation(TeamInvitation $invitation)
    {
        if ($invitation->user_id !== auth()->id()) {
            abort(403);
        }

        if (! $invitation->isPending()) {
            return back()->with('error', 'This invitation is no longer valid.');
        }

        // Check if user is already on a team for this tournament
        $alreadyOnTeam = TeamUser::whereHas('team', function ($query) use ($invitation) {
            $query->where('tournament_id', $invitation->team->tournament_id);
        })->where('user_id', auth()->id())->exists();

        if ($alreadyOnTeam) {
            return back()->with('error', 'You are already on a team for this tournament.');
        }

        $invitation->accept();

        return back()->with('success', 'You have joined the team!');
    }

    public function declineInvitation(TeamInvitation $invitation)
    {
        if ($invitation->user_id !== auth()->id()) {
            abort(403);
        }

        if (! $invitation->isPending()) {
            return back()->with('error', 'This invitation is no longer valid.');
        }

        $invitation->decline();

        return back()->with('success', 'Invitation declined.');
    }

    public function removeMember(Team $team, TeamUser $member)
    {
        $user = auth()->user();

        // Check if user is captain of this team
        $isCaptain = TeamUser::where('team_id', $team->id)
            ->where('user_id', $user->id)
            ->where('is_captain', true)
            ->exists();

        if (! $isCaptain && $member->user_id !== $user->id) {
            return back()->with('error', 'Only team captains can remove members.');
        }

        if ($member->is_captain) {
            return back()->with('error', 'Cannot remove the team captain.');
        }

        $member->delete();

        return back()->with('success', 'Member removed from team.');
    }

    public function destroy(Team $team)
    {
        $user = auth()->user();

        // Check if user is captain of this team
        $isCaptain = TeamUser::where('team_id', $team->id)
            ->where('user_id', $user->id)
            ->where('is_captain', true)
            ->exists();

        if (! $isCaptain) {
            return back()->with('error', 'Only team captains can delete the team.');
        }

        $team->delete();

        return back()->with('success', 'Team deleted successfully.');
    }
}
