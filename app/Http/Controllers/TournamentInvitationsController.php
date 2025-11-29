<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentInvitation;
use App\Models\User;
use Illuminate\Http\Request;

class TournamentInvitationsController extends Controller
{
    public function store(Request $request, Tournament $tournament)
    {
        if (! auth()->check()) {
            return back()->with('error', 'You must be logged in to invite players.');
        }

        // Check if user has permission to edit players
        if (! $tournament->userHasPermission(auth()->user(), 'players', 'edit')) {
            return back()->with('error', 'You do not have permission to invite players.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userId = $request->input('user_id');

        // Check if user is already registered
        $alreadyRegistered = $tournament->registeredPlayers()
            ->where('user_id', $userId)
            ->exists();

        if ($alreadyRegistered) {
            return back()->with('error', 'This player is already registered for the tournament.');
        }

        // Check if invitation already exists
        $existingInvitation = TournamentInvitation::where('tournament_id', $tournament->id)
            ->where('user_id', $userId)
            ->whereIn('status', ['pending', 'accepted'])
            ->first();

        if ($existingInvitation) {
            if ($existingInvitation->status === 'pending') {
                return back()->with('error', 'This player has already been invited.');
            } else {
                // Delete the old accepted invitation and create a new one
                $existingInvitation->delete();
            }
        }

        // Create invitation
        TournamentInvitation::create([
            'tournament_id' => $tournament->id,
            'user_id' => $userId,
            'invited_by' => auth()->id(),
            'status' => 'pending',
        ]);

        $user = User::find($userId);

        return back()->with('success', "Successfully invited {$user->name} to the tournament.");
    }

    public function accept(Tournament $tournament, TournamentInvitation $invitation)
    {
        if (! auth()->check()) {
            return back()->with('error', 'You must be logged in to accept invitations.');
        }

        if ($invitation->user_id !== auth()->id()) {
            return back()->with('error', 'This invitation is not for you.');
        }

        if ($invitation->status !== 'pending') {
            return back()->with('error', 'This invitation has already been responded to.');
        }

        // Mark invitation as accepted
        $invitation->update(['status' => 'accepted']);

        // Check if user is already registered
        $alreadyRegistered = $tournament->registeredPlayers()
            ->where('user_id', auth()->id())
            ->exists();

        if (! $alreadyRegistered) {
            // Automatically register the player
            \App\Models\TournamentPlayer::create([
                'tournament_id' => $tournament->id,
                'user_id' => auth()->id(),
                'looking_for_team' => false,
            ]);

            return redirect()
                ->route('tournaments.players', $tournament)
                ->with('success', 'Invitation accepted! You have been registered for the tournament.');
        }

        return redirect()
            ->route('tournaments.players', $tournament)
            ->with('success', 'Invitation accepted!');
    }

    public function decline(Tournament $tournament, TournamentInvitation $invitation)
    {
        if (! auth()->check()) {
            return back()->with('error', 'You must be logged in to decline invitations.');
        }

        if ($invitation->user_id !== auth()->id()) {
            return back()->with('error', 'This invitation is not for you.');
        }

        if ($invitation->status !== 'pending') {
            return back()->with('error', 'This invitation has already been responded to.');
        }

        $invitation->update(['status' => 'declined']);

        return back()->with('success', 'Invitation declined.');
    }
}
