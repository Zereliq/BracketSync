<?php

namespace App\Http\Controllers;

use App\Models\QualifiersReservation;
use App\Models\QualifiersSlot;
use App\Models\Team;
use App\Models\Tournament;
use App\Notifications\QualifierSuggestionNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QualifiersController extends Controller
{
    public function reserve(Request $request, Tournament $tournament, QualifiersSlot $slot): RedirectResponse
    {
        if (! auth()->check()) {
            return back()->with('error', 'You must be logged in to reserve a qualifier slot.');
        }

        if ($slot->tournament_id !== $tournament->id) {
            abort(404);
        }

        if (! $slot->isOpen()) {
            return back()->with('error', 'This slot is not available for reservation.');
        }

        if ($tournament->qualifiers_signup_deadline && now()->isAfter($tournament->qualifiers_signup_deadline)) {
            return back()->with('error', 'Qualifier signup deadline has passed.');
        }

        $user = auth()->user();

        if ($tournament->isTeamTournament()) {
            $team = Team::query()
                ->where('tournament_id', $tournament->id)
                ->whereHas('users', function ($query) use ($user) {
                    $query->where('users.id', $user->id)
                        ->where('teams_users.is_captain', true);
                })
                ->first();

            if (! $team) {
                return back()->with('error', 'Only team captains can reserve qualifier slots.');
            }

            $existingReservation = QualifiersReservation::query()
                ->where('tournament_id', $tournament->id)
                ->where('team_id', $team->id)
                ->whereIn('status', ['reserved', 'checked_in'])
                ->exists();

            if ($existingReservation) {
                return back()->with('error', 'Your team already has a qualifier reservation.');
            }

            QualifiersReservation::query()->create([
                'qualifiers_slot_id' => $slot->id,
                'tournament_id' => $tournament->id,
                'team_id' => $team->id,
                'reserved_by_user_id' => $user->id,
                'status' => 'reserved',
            ]);

            return back()->with('success', 'Qualifier slot reserved for your team successfully.');
        } else {
            $isRegistered = $tournament->registeredPlayers()
                ->where('user_id', $user->id)
                ->exists();

            if (! $isRegistered) {
                return back()->with('error', 'You must be registered for this tournament to reserve a qualifier slot.');
            }

            $existingReservation = QualifiersReservation::query()
                ->where('tournament_id', $tournament->id)
                ->where('user_id', $user->id)
                ->whereIn('status', ['reserved', 'checked_in'])
                ->exists();

            if ($existingReservation) {
                return back()->with('error', 'You already have a qualifier reservation.');
            }

            QualifiersReservation::query()->create([
                'qualifiers_slot_id' => $slot->id,
                'tournament_id' => $tournament->id,
                'user_id' => $user->id,
                'reserved_by_user_id' => $user->id,
                'status' => 'reserved',
            ]);

            return back()->with('success', 'Qualifier slot reserved successfully.');
        }
    }

    public function suggest(Request $request, Tournament $tournament): RedirectResponse
    {
        if (! auth()->check()) {
            return back()->with('error', 'You must be logged in to suggest a qualifier time.');
        }

        if (! in_array($tournament->qualifier_mode, ['suggest_only', 'slots_and_suggest'])) {
            return back()->with('error', 'Time suggestions are not enabled for this tournament.');
        }

        if ($tournament->qualifiers_signup_deadline && now()->isAfter($tournament->qualifiers_signup_deadline)) {
            return back()->with('error', 'Qualifier signup deadline has passed.');
        }

        $validated = $request->validate([
            'suggested_time' => 'required|date|after:now',
            'comment' => 'nullable|string|max:500',
        ]);

        if ($tournament->qualifiers_signup_deadline && $validated['suggested_time'] > $tournament->qualifiers_signup_deadline) {
            return back()->with('error', 'Suggested time must be before the signup deadline.');
        }

        $user = auth()->user();

        if ($tournament->isTeamTournament()) {
            $team = Team::query()
                ->where('tournament_id', $tournament->id)
                ->whereHas('users', function ($query) use ($user) {
                    $query->where('users.id', $user->id)
                        ->where('teams_users.is_captain', true);
                })
                ->first();

            if (! $team) {
                return back()->with('error', 'Only team captains can suggest qualifier times.');
            }

            $existingReservation = QualifiersReservation::query()
                ->where('tournament_id', $tournament->id)
                ->where('team_id', $team->id)
                ->whereIn('status', ['reserved', 'checked_in'])
                ->exists();

            if ($existingReservation) {
                return back()->with('error', 'Your team already has a qualifier reservation.');
            }

            $reservation = QualifiersReservation::query()->create([
                'tournament_id' => $tournament->id,
                'team_id' => $team->id,
                'reserved_by_user_id' => $user->id,
                'status' => 'reserved',
                'suggested_time' => $validated['suggested_time'],
                'comment' => $validated['comment'] ?? null,
            ]);

            $referees = $tournament->getReferees();
            foreach ($referees as $referee) {
                $referee->notify(new QualifierSuggestionNotification($tournament, $reservation));
            }

            return back()->with('success', 'Qualifier time suggested for your team. Referees have been notified.');
        } else {
            $isRegistered = $tournament->registeredPlayers()
                ->where('user_id', $user->id)
                ->exists();

            if (! $isRegistered) {
                return back()->with('error', 'You must be registered for this tournament to suggest a qualifier time.');
            }

            $existingReservation = QualifiersReservation::query()
                ->where('tournament_id', $tournament->id)
                ->where('user_id', $user->id)
                ->whereIn('status', ['reserved', 'checked_in'])
                ->exists();

            if ($existingReservation) {
                return back()->with('error', 'You already have a qualifier reservation.');
            }

            $reservation = QualifiersReservation::query()->create([
                'tournament_id' => $tournament->id,
                'user_id' => $user->id,
                'reserved_by_user_id' => $user->id,
                'status' => 'reserved',
                'suggested_time' => $validated['suggested_time'],
                'comment' => $validated['comment'] ?? null,
            ]);

            $referees = $tournament->getReferees();
            foreach ($referees as $referee) {
                $referee->notify(new QualifierSuggestionNotification($tournament, $reservation));
            }

            return back()->with('success', 'Qualifier time suggested. Referees have been notified.');
        }
    }

    public function cancel(Tournament $tournament, QualifiersReservation $reservation): RedirectResponse
    {
        if (! auth()->check()) {
            return back()->with('error', 'You must be logged in to cancel a reservation.');
        }

        if ($reservation->tournament_id !== $tournament->id) {
            abort(404);
        }

        $user = auth()->user();

        if ($reservation->reserved_by_user_id !== $user->id) {
            return back()->with('error', 'You can only cancel your own reservations.');
        }

        if (! in_array($reservation->status, ['reserved', 'checked_in'])) {
            return back()->with('error', 'This reservation cannot be cancelled.');
        }

        $reservation->update([
            'status' => 'cancelled',
        ]);

        return back()->with('success', 'Qualifier reservation cancelled successfully.');
    }
}
