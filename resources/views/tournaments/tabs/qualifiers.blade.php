@php
    use App\Models\QualifiersSlot;
    use App\Models\QualifiersReservation;

    $isDashboard = request()->routeIs('dashboard.*');
    $canEdit = $isDashboard && auth()->check() && auth()->user()->can('update', $tournament);
    $isReferee = auth()->check() && $tournament->isReferee(auth()->user());

    $slots = $tournament->qualifiersSlots()
        ->with(['referee', 'reservations.user', 'reservations.team'])
        ->orderBy('start_time')
        ->get();

    $suggestions = $tournament->qualifiersReservations()
        ->with(['user', 'team', 'reservedBy'])
        ->whereNull('qualifiers_slot_id')
        ->whereNotNull('suggested_time')
        ->whereIn('status', ['reserved'])
        ->orderBy('suggested_time')
        ->get();

    $userReservation = null;
    if (auth()->check()) {
        if ($tournament->isTeamTournament()) {
            $userTeam = $tournament->teams()
                ->whereHas('users', function($q) {
                    $q->where('users.id', auth()->id())
                      ->where('teams_users.is_captain', true);
                })
                ->first();
            if ($userTeam) {
                $userReservation = $tournament->qualifiersReservations()
                    ->where('team_id', $userTeam->id)
                    ->whereIn('status', ['reserved', 'checked_in'])
                    ->first();
            }
        } else {
            $userReservation = $tournament->qualifiersReservations()
                ->where('user_id', auth()->id())
                ->whereIn('status', ['reserved', 'checked_in'])
                ->first();
        }
    }

    $unreadSuggestions = collect();
    if ($isReferee) {
        $unreadSuggestions = auth()->user()->unreadNotifications
            ->where('type', 'App\Notifications\QualifierSuggestionNotification')
            ->where('data.tournament_id', $tournament->id)
            ->pluck('data.reservation_id');
    }
@endphp

<div class="space-y-6" x-data="{
    showSettingsModal: false,
    showSlotModal: false,
    showEditSlotModal: false,
    showSuggestModal: false,
    showAcceptModal: false,
    editingSlot: null,
    acceptingSuggestion: null,
    slotToDelete: null
}">

    @if(!$tournament->has_qualifiers)
        {{-- No Qualifiers Message --}}
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl font-bold text-slate-400 mb-2">No Qualifiers</h3>
                <p class="text-slate-500">This tournament does not have a qualifier stage.</p>
                @if($canEdit)
                    <button @click="showSettingsModal = true" type="button" class="mt-4 px-6 py-2.5 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors">
                        Enable Qualifiers
                    </button>
                @endif
            </div>
        </div>
    @else
        {{-- Staff Controls (Dashboard Only) --}}
        @if($canEdit)
            <div class="flex justify-end gap-3">
                <button @click="showSettingsModal = true" type="button" class="px-6 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Qualifier Settings</span>
                </button>
                <button @click="showSlotModal = true" type="button" class="px-6 py-2.5 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Create Slot</span>
                </button>
            </div>

            {{-- Settings Overview Card --}}
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <h3 class="text-lg font-bold text-white mb-4">Current Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-slate-800/50 rounded-lg p-4">
                        <p class="text-sm text-slate-400 mb-1">Qualifier Mode</p>
                        <p class="text-white font-semibold">{{ ucfirst(str_replace('_', ' ', $tournament->qualifier_mode)) }}</p>
                    </div>
                    <div class="bg-slate-800/50 rounded-lg p-4">
                        <p class="text-sm text-slate-400 mb-1">Badged Tournament</p>
                        <p class="text-white font-semibold">{{ $tournament->is_badged ? 'Yes' : 'No' }}</p>
                    </div>
                    <div class="bg-slate-800/50 rounded-lg p-4">
                        <p class="text-sm text-slate-400 mb-1">Slot Length</p>
                        <p class="text-white font-semibold">{{ $tournament->qualifiers_slot_length_minutes }} minutes</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Qualifier Slots List --}}
        @if(in_array($tournament->qualifier_mode, ['slots_only', 'slots_and_suggest']))
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-white">Available Qualifier Slots</h2>
                </div>

                @if($slots->isEmpty())
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="text-xl font-bold text-slate-400 mb-2">No Slots Created</h3>
                        <p class="text-slate-500">{{ $canEdit ? 'Create qualifier slots for players to reserve.' : 'No qualifier slots are available yet.' }}</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($slots as $slot)
                            <div class="bg-slate-800/50 border border-slate-700/50 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-4">
                                            <div>
                                                <p class="text-white font-semibold">{{ $slot->start_time->format('M j, Y @ g:i A') }}</p>
                                                <p class="text-sm text-slate-400">{{ $slot->start_time->diffForHumans() }}</p>
                                            </div>
                                            @if($slot->referee)
                                                <div class="flex items-center space-x-2 text-sm text-slate-300">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                    <span>Ref: {{ $slot->referee->username }}</span>
                                                </div>
                                            @endif
                                            <div class="flex items-center space-x-2">
                                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                                    @if($slot->status === 'open') bg-green-500/10 text-green-400 border border-green-500/30
                                                    @elseif($slot->status === 'reserved') bg-blue-500/10 text-blue-400 border border-blue-500/30
                                                    @elseif($slot->status === 'completed') bg-purple-500/10 text-purple-400 border border-purple-500/30
                                                    @else bg-slate-500/10 text-slate-400 border border-slate-500/30
                                                    @endif">
                                                    {{ ucfirst($slot->status) }}
                                                </span>
                                                <span class="text-sm text-slate-400">
                                                    {{ $slot->available_spots }}/{{ $slot->max_participants }} available
                                                </span>
                                            </div>
                                        </div>
                                        @if($slot->notes)
                                            <p class="mt-2 text-sm text-slate-400">{{ $slot->notes }}</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-2 ml-4">
                                        @if($canEdit)
                                            <button @click="editingSlot = {{ $slot->id }}; showEditSlotModal = true" type="button" class="p-2 text-slate-400 hover:text-white transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            <form action="{{ route('dashboard.tournaments.qualifiers.slots.destroy', [$tournament, $slot]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this slot?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-red-400 hover:text-red-300 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @elseif(!$canEdit && $slot->isOpen() && !$userReservation)
                                            <form action="{{ route('tournaments.qualifiers.reserve', [$tournament, $slot]) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors">
                                                    Reserve
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                {{-- Show reservations for this slot (staff view) --}}
                                @if($canEdit && $slot->reservations->isNotEmpty())
                                    <div class="mt-4 pt-4 border-t border-slate-700/50">
                                        <p class="text-sm font-medium text-slate-300 mb-2">Reservations:</p>
                                        <div class="space-y-1">
                                            @foreach($slot->reservations->whereIn('status', ['reserved', 'checked_in']) as $reservation)
                                                <div class="flex items-center justify-between text-sm">
                                                    <span class="text-slate-400">
                                                        @if($reservation->team)
                                                            {{ $reservation->team->teamname }}
                                                        @elseif($reservation->user)
                                                            {{ $reservation->user->username }}
                                                        @endif
                                                    </span>
                                                    <span class="text-slate-500">{{ ucfirst($reservation->status) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        {{-- Time Suggestions (Players) --}}
        @if(!$canEdit && in_array($tournament->qualifier_mode, ['suggest_only', 'slots_and_suggest']) && !$userReservation)
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <h2 class="text-xl font-bold text-white mb-4">Suggest a Time</h2>
                <p class="text-slate-400 mb-6">Can't find a suitable slot? Suggest your preferred time and staff will review your request.</p>
                <button @click="showSuggestModal = true" type="button" class="px-6 py-2.5 bg-fuchsia-500 hover:bg-fuchsia-600 text-white font-medium rounded-lg transition-colors">
                    Suggest Time
                </button>
            </div>
        @endif

        {{-- Pending Suggestions (Staff & Referees) --}}
        @if(($canEdit || $isReferee) && $suggestions->isNotEmpty())
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center justify-between">
                    <span>Pending Time Suggestions</span>
                    @if($isReferee && $unreadSuggestions->isNotEmpty())
                        <span class="px-3 py-1 bg-pink-500 text-white text-sm font-medium rounded-full">
                            {{ $unreadSuggestions->count() }} new
                        </span>
                    @endif
                </h2>
                <div class="space-y-3">
                    @foreach($suggestions as $suggestion)
                        <div class="bg-slate-800/50 border {{ $unreadSuggestions->contains($suggestion->id) ? 'border-pink-500/50 shadow-lg shadow-pink-500/10' : 'border-slate-700/50' }} rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-4">
                                        <div>
                                            @if($unreadSuggestions->contains($suggestion->id))
                                                <span class="inline-block px-2 py-1 bg-pink-500 text-white text-xs font-bold rounded mb-1">NEW</span>
                                            @endif
                                            <p class="text-white font-semibold">
                                                @if($suggestion->team)
                                                    {{ $suggestion->team->teamname }}
                                                @elseif($suggestion->user)
                                                    {{ $suggestion->user->username }}
                                                @endif
                                            </p>
                                            <p class="text-sm text-slate-400">Suggested: {{ $suggestion->suggested_time->format('M j, Y @ g:i A') }}</p>
                                        </div>
                                    </div>
                                    @if($suggestion->comment)
                                        <p class="mt-2 text-sm text-slate-400">Comment: {{ $suggestion->comment }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-2 ml-4">
                                    @if($isReferee && !$canEdit)
                                        {{-- Referees see "Accept & Ref" button --}}
                                        <form action="{{ route('dashboard.tournaments.qualifiers.suggestions.referee-accept', [$tournament, $suggestion]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors flex items-center space-x-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                <span>Accept & Ref</span>
                                            </button>
                                        </form>
                                    @elseif($canEdit)
                                        {{-- Staff see regular Accept button with modal --}}
                                        <button @click="acceptingSuggestion = {{ $suggestion->id }}; showAcceptModal = true" type="button" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors">
                                            Accept
                                        </button>
                                        <form action="{{ route('dashboard.tournaments.qualifiers.suggestions.deny', [$tournament, $suggestion]) }}" method="POST" onsubmit="return confirm('Are you sure you want to deny this suggestion?')">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors">
                                                Deny
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- User's Current Reservation --}}
        @if($userReservation)
            <div class="bg-gradient-to-br from-green-900/20 to-slate-900 border border-green-500/30 rounded-xl p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-green-400 mb-2">You're Registered!</h3>
                        @if($userReservation->slot)
                            <p class="text-slate-300">Your qualifier slot: <span class="font-semibold text-white">{{ $userReservation->slot->start_time->format('M j, Y @ g:i A') }}</span></p>
                            @if($userReservation->slot->referee)
                                <p class="text-sm text-slate-400 mt-1">Referee: {{ $userReservation->slot->referee->username }}</p>
                            @endif
                        @elseif($userReservation->suggested_time)
                            <p class="text-slate-300">Your suggested time: <span class="font-semibold text-white">{{ $userReservation->suggested_time->format('M j, Y @ g:i A') }}</span></p>
                            <p class="text-sm text-yellow-400 mt-1">Pending staff approval</p>
                        @endif
                    </div>
                    <form action="{{ route('tournaments.qualifiers.cancel', [$tournament, $userReservation]) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel your qualifier reservation?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors">
                            Cancel
                        </button>
                    </form>
                </div>
            </div>
        @endif
    @endif

    {{-- Settings Modal --}}
    @if($canEdit)
        <div x-show="showSettingsModal" x-cloak @click.self="showSettingsModal = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div @click.stop class="bg-slate-900 border border-slate-800 rounded-xl shadow-2xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
                <h3 class="text-xl font-bold text-white mb-6">Qualifier Settings</h3>

                <form action="{{ route('dashboard.tournaments.qualifiers.settings.update', $tournament) }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="flex items-center">
                        <input type="hidden" name="has_qualifiers" value="0">
                        <input type="checkbox" name="has_qualifiers" id="has_qualifiers" value="1" {{ $tournament->has_qualifiers ? 'checked' : '' }} class="w-4 h-4 text-pink-500 bg-slate-800 border-slate-700 rounded focus:ring-pink-500">
                        <label for="has_qualifiers" class="ml-2 text-sm font-medium text-slate-300">Enable Qualifiers</label>
                    </div>

                    <div class="flex items-center">
                        <input type="hidden" name="is_badged" value="0">
                        <input type="checkbox" name="is_badged" id="is_badged" value="1" {{ $tournament->is_badged ? 'checked' : '' }} class="w-4 h-4 text-pink-500 bg-slate-800 border-slate-700 rounded focus:ring-pink-500">
                        <label for="is_badged" class="ml-2 text-sm font-medium text-slate-300">Badged Tournament (requires referees)</label>
                    </div>

                    <div>
                        <label for="qualifier_mode" class="block text-sm font-medium text-slate-300 mb-2">Qualifier Mode</label>
                        <select name="qualifier_mode" id="qualifier_mode" required class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                            <option value="slots_only" {{ $tournament->qualifier_mode === 'slots_only' ? 'selected' : '' }}>Slots Only</option>
                            <option value="suggest_only" {{ $tournament->qualifier_mode === 'suggest_only' ? 'selected' : '' }}>Suggestions Only</option>
                            <option value="slots_and_suggest" {{ $tournament->qualifier_mode === 'slots_and_suggest' ? 'selected' : '' }}>Slots and Suggestions</option>
                        </select>
                    </div>

                    <div>
                        <label for="qualifiers_slot_length_minutes" class="block text-sm font-medium text-slate-300 mb-2">Default Slot Length (minutes)</label>
                        <input type="number" name="qualifiers_slot_length_minutes" id="qualifiers_slot_length_minutes" value="{{ $tournament->qualifiers_slot_length_minutes }}" min="5" max="120" required class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="qualifiers_signup_deadline" class="block text-sm font-medium text-slate-300 mb-2">Signup Deadline</label>
                        <input type="datetime-local" name="qualifiers_signup_deadline" id="qualifiers_signup_deadline" value="{{ $tournament->qualifiers_signup_deadline?->format('Y-m-d\TH:i') }}" class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="showSettingsModal = false" class="flex-1 px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Create Slot Modal --}}
        <div x-show="showSlotModal" x-cloak @click.self="showSlotModal = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div @click.stop class="bg-slate-900 border border-slate-800 rounded-xl shadow-2xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
                <h3 class="text-xl font-bold text-white mb-6">Create Qualifier Slot</h3>

                <form action="{{ route('dashboard.tournaments.qualifiers.slots.store', $tournament) }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="start_time" class="block text-sm font-medium text-slate-300 mb-2">Start Time*</label>
                        <input type="datetime-local" name="start_time" id="start_time" required class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="referee_user_id" class="block text-sm font-medium text-slate-300 mb-2">Referee {{ $tournament->qualifiers_required_referee ? '*' : '(Optional)' }}</label>
                        <input type="text" id="referee_search" placeholder="Search for referee..." class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent mb-2">
                        <input type="hidden" name="referee_user_id" id="referee_user_id">
                        <p class="text-sm text-slate-400">Start typing to search for a user</p>
                    </div>

                    <div>
                        <label for="max_participants" class="block text-sm font-medium text-slate-300 mb-2">Max Participants*</label>
                        <input type="number" name="max_participants" id="max_participants" value="{{ $tournament->isTeamTournament() ? $tournament->max_teamsize : 1 }}" min="1" max="100" required class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                    </div>

                    <div class="flex items-center">
                        <input type="hidden" name="is_public" value="0">
                        <input type="checkbox" name="is_public" id="is_public" value="1" checked class="w-4 h-4 text-pink-500 bg-slate-800 border-slate-700 rounded focus:ring-pink-500">
                        <label for="is_public" class="ml-2 text-sm font-medium text-slate-300">Public (visible to players)</label>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-slate-300 mb-2">Notes</label>
                        <textarea name="notes" id="notes" rows="3" class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"></textarea>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="showSlotModal = false" class="flex-1 px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors">
                            Create Slot
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Suggest Time Modal (Players) --}}
    @if(!$canEdit)
        <div x-show="showSuggestModal" x-cloak @click.self="showSuggestModal = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div @click.stop class="bg-slate-900 border border-slate-800 rounded-xl shadow-2xl max-w-lg w-full p-6">
                <h3 class="text-xl font-bold text-white mb-6">Suggest Qualifier Time</h3>

                <form action="{{ route('tournaments.qualifiers.suggest', $tournament) }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="suggested_time" class="block text-sm font-medium text-slate-300 mb-2">Preferred Time*</label>
                        <input type="datetime-local" name="suggested_time" id="suggested_time" required class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="comment" class="block text-sm font-medium text-slate-300 mb-2">Comment (Optional)</label>
                        <textarea name="comment" id="comment" rows="3" placeholder="Any additional information..." class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"></textarea>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="showSuggestModal = false" class="flex-1 px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-fuchsia-500 hover:bg-fuchsia-600 text-white font-medium rounded-lg transition-colors">
                            Submit Suggestion
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
