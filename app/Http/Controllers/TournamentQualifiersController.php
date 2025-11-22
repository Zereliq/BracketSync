<?php

namespace App\Http\Controllers;

use App\Models\QualifiersReservation;
use App\Models\QualifiersSlot;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TournamentQualifiersController extends Controller
{
    use AuthorizesRequests;

    public function updateSettings(Request $request, Tournament $tournament): RedirectResponse
    {
        $this->authorize('update', $tournament);

        $validated = $request->validate([
            'has_qualifiers' => 'boolean',
            'is_badged' => 'boolean',
            'qualifier_mode' => 'required|in:slots_only,suggest_only,slots_and_suggest',
            'qualifiers_slot_length_minutes' => 'required|integer|min:5|max:120',
            'qualifiers_signup_deadline' => 'nullable|date',
        ]);

        $qualifiersRequiredReferee = $validated['is_badged'] ?? false;

        $tournament->update([
            ...$validated,
            'qualifiers_required_referee' => $qualifiersRequiredReferee,
        ]);

        return back()->with('success', 'Qualifier settings updated successfully.');
    }

    public function storeSlot(Request $request, Tournament $tournament): RedirectResponse
    {
        $this->authorize('update', $tournament);

        $validated = $request->validate([
            'start_time' => 'required|date|after:now',
            'end_time' => 'nullable|date|after:start_time',
            'referee_user_id' => $tournament->qualifiers_required_referee ? 'required|exists:users,id' : 'nullable|exists:users,id',
            'max_participants' => 'required|integer|min:1|max:100',
            'is_public' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        if (! isset($validated['end_time'])) {
            $startTime = new \DateTime($validated['start_time']);
            $endTime = clone $startTime;
            $endTime->modify("+{$tournament->qualifiers_slot_length_minutes} minutes");
            $validated['end_time'] = $endTime->format('Y-m-d H:i:s');
        }

        QualifiersSlot::query()->create([
            'tournament_id' => $tournament->id,
            'created_by' => auth()->id(),
            ...$validated,
        ]);

        return back()->with('success', 'Qualifier slot created successfully.');
    }

    public function updateSlot(Request $request, Tournament $tournament, QualifiersSlot $slot): RedirectResponse
    {
        $this->authorize('update', $tournament);

        if ($slot->tournament_id !== $tournament->id) {
            abort(404);
        }

        $validated = $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'referee_user_id' => $tournament->qualifiers_required_referee ? 'required|exists:users,id' : 'nullable|exists:users,id',
            'max_participants' => 'required|integer|min:1|max:100',
            'is_public' => 'boolean',
            'status' => 'required|in:open,reserved,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        $slot->update($validated);

        return back()->with('success', 'Qualifier slot updated successfully.');
    }

    public function destroySlot(Tournament $tournament, QualifiersSlot $slot): RedirectResponse
    {
        $this->authorize('update', $tournament);

        if ($slot->tournament_id !== $tournament->id) {
            abort(404);
        }

        $slot->delete();

        return back()->with('success', 'Qualifier slot deleted successfully.');
    }

    public function acceptSuggestion(Request $request, Tournament $tournament, QualifiersReservation $reservation): RedirectResponse
    {
        $this->authorize('update', $tournament);

        if ($reservation->tournament_id !== $tournament->id || ! $reservation->isSuggestion()) {
            abort(404);
        }

        $request->validate([
            'referee_user_id' => $tournament->qualifiers_required_referee ? 'required|exists:users,id' : 'nullable|exists:users,id',
        ]);

        $slot = QualifiersSlot::query()->create([
            'tournament_id' => $tournament->id,
            'referee_user_id' => $request->input('referee_user_id'),
            'start_time' => $reservation->suggested_time,
            'end_time' => $reservation->suggested_time->copy()->addMinutes($tournament->qualifiers_slot_length_minutes),
            'max_participants' => $tournament->isTeamTournament() ? $tournament->max_teamsize : 1,
            'is_public' => true,
            'status' => 'reserved',
            'created_by' => auth()->id(),
        ]);

        $reservation->update([
            'qualifiers_slot_id' => $slot->id,
            'status' => 'reserved',
        ]);

        return back()->with('success', 'Suggestion accepted and slot created.');
    }

    public function denySuggestion(Request $request, Tournament $tournament, QualifiersReservation $reservation): RedirectResponse
    {
        $this->authorize('update', $tournament);

        if ($reservation->tournament_id !== $tournament->id || ! $reservation->isSuggestion()) {
            abort(404);
        }

        $reservation->update([
            'status' => 'cancelled',
        ]);

        return back()->with('success', 'Suggestion denied.');
    }

    public function refereeAcceptSuggestion(Tournament $tournament, QualifiersReservation $reservation): RedirectResponse
    {
        $user = auth()->user();

        if (! $tournament->isReferee($user)) {
            abort(403, 'Only referees can accept suggestions.');
        }

        if ($reservation->tournament_id !== $tournament->id || ! $reservation->isSuggestion()) {
            abort(404);
        }

        $slot = QualifiersSlot::query()->create([
            'tournament_id' => $tournament->id,
            'referee_user_id' => $user->id,
            'start_time' => $reservation->suggested_time,
            'end_time' => $reservation->suggested_time->copy()->addMinutes($tournament->qualifiers_slot_length_minutes),
            'max_participants' => $tournament->isTeamTournament() ? $tournament->max_teamsize : 1,
            'is_public' => true,
            'status' => 'reserved',
            'created_by' => $user->id,
        ]);

        $reservation->update([
            'qualifiers_slot_id' => $slot->id,
            'status' => 'reserved',
        ]);

        $user->unreadNotifications()
            ->where('type', 'App\Notifications\QualifierSuggestionNotification')
            ->where('data->reservation_id', $reservation->id)
            ->update(['read_at' => now()]);

        return back()->with('success', 'Suggestion accepted and you have been assigned as the referee.');
    }

    public function searchUsers(Request $request, Tournament $tournament)
    {
        $this->authorize('update', $tournament);

        $query = $request->input('q');

        if (! $query || strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::query()
            ->where('username', 'like', "%{$query}%")
            ->orWhere('osu_username', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'username', 'osu_username', 'avatar_url']);

        return response()->json($users);
    }
}
