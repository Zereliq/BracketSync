@php
    $isDashboard = $isDashboard ?? request()->routeIs('dashboard.*');
    $canEdit = auth()->check() && auth()->user()->can('editMatches', $tournament);
    $matches = $matches ?? collect();
    $rounds = $rounds ?? [];
    $selectedRound = $selectedRound ?? null;
    $myMatches = $myMatches ?? false;
    $routePrefix = $isDashboard ? 'dashboard.tournaments.' : 'tournaments.';

    // Status colors and labels
    $statusConfig = [
        'pending' => ['color' => 'slate', 'label' => 'Pending', 'icon' => 'clock'],
        'scheduled' => ['color' => 'blue', 'label' => 'Scheduled', 'icon' => 'calendar'],
        'in_progress' => ['color' => 'yellow', 'label' => 'In Progress', 'icon' => 'play'],
        'completed' => ['color' => 'green', 'label' => 'Completed', 'icon' => 'check'],
        'no_show' => ['color' => 'orange', 'label' => 'No-Show', 'icon' => 'alert-circle'],
        'cancelled' => ['color' => 'red', 'label' => 'Cancelled', 'icon' => 'x'],
    ];
@endphp

<div class="space-y-6">
    @if($canEdit)
        {{-- Quick Link to Settings --}}
        <div class="bg-gradient-to-r from-pink-500/10 to-purple-500/10 border border-pink-500/30 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-lg bg-pink-500/20 border border-pink-500/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold">Configure Match Settings</h3>
                        <p class="text-xs text-slate-400">Set Best Of format and Mappools for each round</p>
                    </div>
                </div>
                <a href="{{ route('dashboard.tournaments.settings', $tournament) }}" class="px-4 py-2 bg-pink-500 hover:bg-pink-600 text-white text-sm font-medium rounded-lg transition-colors flex items-center space-x-2">
                    <span>Go to Settings</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    @endif

    @if(!empty($rounds))
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            {{-- Round Filter Pills --}}
            <div class="flex flex-wrap items-center gap-3 mb-6">
                <span class="text-sm font-medium text-slate-400">Filter by Round:</span>
                <a href="{{ route($routePrefix . 'matches', $tournament) }}{{ $myMatches ? '?my_matches=true' : '' }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $selectedRound === null ? 'bg-pink-500 text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700' }}">
                    All Rounds
                </a>
                @foreach($rounds as $round)
                    <a href="{{ route($routePrefix . 'matches', $tournament) }}?round={{ $round['number'] }}{{ $myMatches ? '&my_matches=true' : '' }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $selectedRound == $round['number'] ? 'bg-pink-500 text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700' }}">
                        {{ $round['name'] }}
                        <span class="ml-1.5 text-xs opacity-75">({{ $round['count'] }})</span>
                    </a>
                @endforeach
            </div>

            {{-- My Matches Toggle --}}
            @auth
                <div class="flex items-center space-x-3 pt-4 border-t border-slate-800">
                    <label class="flex items-center space-x-2 cursor-pointer group">
                        <input type="checkbox"
                               class="w-5 h-5 text-pink-500 bg-slate-800 border-slate-700 rounded focus:ring-pink-500 focus:ring-offset-slate-900"
                               {{ $myMatches ? 'checked' : '' }}
                               onchange="toggleMyMatches(this.checked)">
                        <span class="text-sm font-medium text-slate-300 group-hover:text-white transition-colors">
                            Show only my matches
                        </span>
                    </label>
                    <span class="text-xs text-slate-500">(as player or referee)</span>
                </div>
                <script>
                    function toggleMyMatches(checked) {
                        const url = new URL(window.location.href);
                        if (checked) {
                            url.searchParams.set('my_matches', 'true');
                        } else {
                            url.searchParams.delete('my_matches');
                        }
                        window.location.href = url.toString();
                    }
                </script>
            @endauth
        </div>
    @endif

    @if($matches->isEmpty())
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
            </svg>
            <h3 class="text-xl font-bold text-slate-400 mb-2">
                @if($myMatches)
                    No Matches Found
                @else
                    No Matches Scheduled
                @endif
            </h3>
            <p class="text-slate-500">
                @if($myMatches)
                    You don't have any matches{{ $selectedRound !== null ? ' in this round' : '' }}.
                @else
                    No matches have been scheduled{{ $selectedRound !== null ? ' for this round' : ' for this tournament' }} yet.
                @endif
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-4">
            @foreach($matches as $match)
                @php
                    // Get the correct participant based on tournament type
                    $team1 = $tournament->isTeamTournament() ? $match->team1 : $match->player1;
                    $team2 = $tournament->isTeamTournament() ? $match->team2 : $match->player2;

                    // Skip matches without determined players
                    if (!$team1 && !$team2) {
                        continue;
                    }

                    $winner = $match->winner;
                    $status = $match->status ?? 'pending';
                    $statusInfo = $statusConfig[$status] ?? $statusConfig['pending'];

                    $isUserMatch = false;
                    if (auth()->check()) {
                        $user = auth()->user();
                        $isReferee = $match->referee_id === $user->id;
                        if ($tournament->isTeamTournament()) {
                            $userTeams = $user->teams()->where('tournament_id', $tournament->id)->pluck('id');
                            $isUserMatch = $isReferee || $userTeams->contains($match->team1_id) || $userTeams->contains($match->team2_id);
                        } else {
                            $isUserMatch = $isReferee || $match->team1_id === $user->id || $match->team2_id === $user->id;
                        }
                    }

                    // Calculate match duration if completed
                    $duration = null;
                    if ($match->match_start && $match->match_end) {
                        $duration = $match->match_start->diffInMinutes($match->match_end);
                    }

                    // Get map count (distinct map_numbers in scores)
                    $gameCount = $match->scores ? $match->scores->pluck('map_number')->unique()->count() : 0;

                    // Determine if team1 or team2 won
                    $team1Won = $winner && $winner->id === $team1?->id;
                    $team2Won = $winner && $winner->id === $team2?->id;
                @endphp

                <div class="bg-slate-900 border {{ $isUserMatch ? 'border-pink-500/50' : 'border-slate-800' }} rounded-xl overflow-hidden hover:border-slate-700 transition-all {{ $isUserMatch ? 'ring-2 ring-pink-500/20' : '' }}">
                    {{-- Match Header --}}
                    <div class="bg-gradient-to-r from-slate-800/50 to-slate-900/50 px-6 py-4 border-b border-slate-800">
                        <div class="flex items-center justify-between flex-wrap gap-3">
                            <div class="flex items-center space-x-3 flex-wrap gap-2">
                                {{-- Your Match Badge --}}
                                @if($isUserMatch)
                                    <div class="px-3 py-1 bg-pink-500/20 border border-pink-500/30 rounded-lg">
                                        <span class="text-xs font-semibold text-pink-400">YOUR MATCH</span>
                                    </div>
                                @endif

                                {{-- Round Badge --}}
                                <div class="px-3 py-1 bg-slate-800 rounded-lg">
                                    <span class="text-sm font-medium text-slate-300">Round {{ $match->round }}</span>
                                </div>

                                {{-- Seed Info --}}
                                @if($match->team1_seed || $match->team2_seed)
                                    <div class="px-3 py-1 bg-slate-800/50 rounded-lg">
                                        <span class="text-xs font-medium text-slate-400">
                                            @if($match->team1_seed && $match->team2_seed)
                                                Seed {{ $match->team1_seed }} vs {{ $match->team2_seed }}
                                            @elseif($match->team1_seed)
                                                Seed {{ $match->team1_seed }}
                                            @else
                                                Seed {{ $match->team2_seed }}
                                            @endif
                                        </span>
                                    </div>
                                @endif

                                {{-- Best Of Badge --}}
                                @if($match->best_of)
                                    <div class="px-3 py-1 bg-blue-500/20 border border-blue-500/30 rounded-lg">
                                        <span class="text-xs font-semibold text-blue-400">BO{{ $match->best_of }}</span>
                                    </div>
                                @endif

                                {{-- Status Badge --}}
                                <div class="px-3 py-1.5 bg-{{ $statusInfo['color'] }}-500/20 border border-{{ $statusInfo['color'] }}-500/30 rounded-lg flex items-center space-x-1.5">
                                    @if($statusInfo['icon'] === 'clock')
                                        <svg class="w-3.5 h-3.5 text-{{ $statusInfo['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @elseif($statusInfo['icon'] === 'calendar')
                                        <svg class="w-3.5 h-3.5 text-{{ $statusInfo['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    @elseif($statusInfo['icon'] === 'play')
                                        <svg class="w-3.5 h-3.5 text-{{ $statusInfo['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @elseif($statusInfo['icon'] === 'check')
                                        <svg class="w-3.5 h-3.5 text-{{ $statusInfo['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @elseif($statusInfo['icon'] === 'x')
                                        <svg class="w-3.5 h-3.5 text-{{ $statusInfo['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @endif
                                    <span class="text-xs font-semibold text-{{ $statusInfo['color'] }}-400 uppercase">{{ $statusInfo['label'] }}</span>
                                </div>
                            </div>

                            @if($canEdit)
                                <div class="flex items-center gap-2 flex-wrap">
                                    {{-- Fill Result Button (only if match is not completed or no_show) --}}
                                    @if($match->status !== 'completed' && $match->status !== 'no_show')
                                        <button type="button"
                                                onclick="openFillResult({{ $match->id }})"
                                                class="px-4 py-2 bg-pink-500 hover:bg-pink-600 text-white text-sm font-medium rounded-lg transition-colors flex items-center space-x-2"
                                                title="Fill match result from osu! match ID">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>Fill Result</span>
                                        </button>

                                        {{-- Manual Entry Button --}}
                                        <button type="button"
                                                onclick="openManualEntry({{ $match->id }})"
                                                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors flex items-center space-x-2"
                                                title="Manually enter map wins">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            <span>Manual Entry</span>
                                        </button>

                                        {{-- Mark as No-Show Button --}}
                                        <button type="button"
                                                onclick="openNoShowModal({{ $match->id }})"
                                                class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition-colors flex items-center space-x-2"
                                                title="Mark player/team as no-show">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>No-Show</span>
                                        </button>
                                    @else
                                        {{-- Edit Result Button (for completed/no_show matches) --}}
                                        <button type="button"
                                                onclick="openEditMatch({{ $match->id }})"
                                                class="px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white text-sm font-medium rounded-lg transition-colors flex items-center space-x-2"
                                                title="Edit match result">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            <span>Edit Result</span>
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Match Content --}}
                    <div class="p-6">
                        {{-- Teams Display --}}
                        @php
                            $hasMatchDetails = $match->scheduled_at || $duration;
                            $hasRefSection = $match->referee || ($match->rolls && $match->rolls->count() > 0);
                        @endphp
                        <div class="grid grid-cols-1 md:grid-cols-7 gap-4 items-center {{ ($hasMatchDetails || $hasRefSection) ? 'mb-6' : '' }}">
                            {{-- Team 1 --}}
                            <div class="md:col-span-3">
                                @if($team1)
                                    @php
                                        $team1Name = $tournament->isTeamTournament()
                                            ? ($team1->teamname ?? 'TBD')
                                            : ($team1->name ?? $team1->username ?? 'TBD');
                                    @endphp
                                    <div class="flex items-center space-x-3 p-4 bg-slate-800/50 rounded-lg {{ $team1Won ? 'ring-2 ring-green-500/50' : '' }} transition-all">
                                        @if($tournament->isTeamTournament())
                                            @if($team1->logo_url)
                                                <img src="{{ $team1->logo_url }}" alt="{{ $team1Name }}" class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                                            @else
                                                <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-pink-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                                                    <span class="text-white text-base font-bold">{{ substr($team1Name, 0, 2) }}</span>
                                                </div>
                                            @endif
                                        @else
                                            @if($team1->avatar_url)
                                                <img src="{{ $team1->avatar_url }}" alt="{{ $team1Name }}" class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                                            @else
                                                <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-pink-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                                                    <span class="text-white text-base font-bold">{{ substr($team1Name, 0, 2) }}</span>
                                                </div>
                                            @endif
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <p class="text-white font-semibold truncate text-lg">{{ $team1Name }}</p>
                                            @if($match->team1_seed)
                                                <p class="text-slate-500 text-xs">Seed #{{ $match->team1_seed }}</p>
                                            @endif
                                            @if($match->status === 'no_show' && $match->no_show_team_id === $team1->id)
                                                @if($match->no_show_type === 'disqualified')
                                                    <span class="inline-block mt-1 px-2 py-0.5 bg-red-500/20 border border-red-500/50 text-red-400 text-xs font-semibold rounded">DISQUALIFIED</span>
                                                @else
                                                    <span class="inline-block mt-1 px-2 py-0.5 bg-orange-500/20 border border-orange-500/50 text-orange-400 text-xs font-semibold rounded">NO-SHOW</span>
                                                @endif
                                            @endif
                                        </div>
                                        <div class="flex flex-col items-end">
                                            <div class="text-3xl font-bold {{ $team1Won ? 'text-green-400' : 'text-white' }}">
                                                {{ $match->scores->where('winning_team_id', $team1->id)->pluck('map_number')->unique()->count() }}
                                            </div>
                                            @if($team1Won)
                                                <span class="text-xs font-semibold text-green-400 uppercase">Winner</span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="p-4 bg-slate-800/30 rounded-lg text-center border border-dashed border-slate-700">
                                        <span class="text-slate-500 font-medium">TBD</span>
                                    </div>
                                @endif
                            </div>

                            {{-- VS Divider --}}
                            <div class="md:col-span-1 flex justify-center">
                                <div class="w-12 h-12 rounded-full bg-slate-800 border-2 border-slate-700 flex items-center justify-center">
                                    <span class="text-slate-400 font-bold text-sm">VS</span>
                                </div>
                            </div>

                            {{-- Team 2 --}}
                            <div class="md:col-span-3">
                                @if($team2)
                                    @php
                                        $team2Name = $tournament->isTeamTournament()
                                            ? ($team2->teamname ?? 'TBD')
                                            : ($team2->name ?? $team2->username ?? 'TBD');
                                    @endphp
                                    <div class="flex items-center space-x-3 p-4 bg-slate-800/50 rounded-lg {{ $team2Won ? 'ring-2 ring-green-500/50' : '' }} transition-all">
                                        <div class="flex flex-col items-start">
                                            <div class="text-3xl font-bold {{ $team2Won ? 'text-green-400' : 'text-white' }}">
                                                {{ $match->scores->where('winning_team_id', $team2->id)->pluck('map_number')->unique()->count() }}
                                            </div>
                                            @if($team2Won)
                                                <span class="text-xs font-semibold text-green-400 uppercase">Winner</span>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-white font-semibold truncate text-lg text-right">{{ $team2Name }}</p>
                                            @if($match->team2_seed)
                                                <p class="text-slate-500 text-xs text-right">Seed #{{ $match->team2_seed }}</p>
                                            @endif
                                            @if($match->status === 'no_show' && $match->no_show_team_id === $team2->id)
                                                @if($match->no_show_type === 'disqualified')
                                                    <span class="inline-block mt-1 px-2 py-0.5 bg-red-500/20 border border-red-500/50 text-red-400 text-xs font-semibold rounded">DISQUALIFIED</span>
                                                @else
                                                    <span class="inline-block mt-1 px-2 py-0.5 bg-orange-500/20 border border-orange-500/50 text-orange-400 text-xs font-semibold rounded">NO-SHOW</span>
                                                @endif
                                            @endif
                                        </div>
                                        @if($tournament->isTeamTournament())
                                            @if($team2->logo_url)
                                                <img src="{{ $team2->logo_url }}" alt="{{ $team2Name }}" class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                                            @else
                                                <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center flex-shrink-0">
                                                    <span class="text-white text-base font-bold">{{ substr($team2Name, 0, 2) }}</span>
                                                </div>
                                            @endif
                                        @else
                                            @if($team2->avatar_url)
                                                <img src="{{ $team2->avatar_url }}" alt="{{ $team2Name }}" class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                                            @else
                                                <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center flex-shrink-0">
                                                    <span class="text-white text-base font-bold">{{ substr($team2Name, 0, 2) }}</span>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                @else
                                    <div class="p-4 bg-slate-800/30 rounded-lg text-center border border-dashed border-slate-700">
                                        <span class="text-slate-500 font-medium">TBD</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Match Details Grid --}}
                        @if($hasMatchDetails)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 {{ $hasRefSection ? 'mb-4' : '' }}">
                            {{-- Scheduled Time --}}
                            @if($match->scheduled_at)
                                <div class="bg-slate-800/30 rounded-lg p-3 border border-slate-800">
                                    <div class="flex items-center space-x-2 text-slate-400 mb-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-xs font-medium">Scheduled</span>
                                    </div>
                                    <p class="text-white text-sm font-semibold">{{ $match->scheduled_at->format('M j, Y') }}</p>
                                    <p class="text-slate-400 text-xs">{{ $match->scheduled_at->format('g:i A') }}</p>
                                </div>
                            @endif

                            {{-- Duration --}}
                            @if($duration)
                                <div class="bg-slate-800/30 rounded-lg p-3 border border-slate-800">
                                    <div class="flex items-center space-x-2 text-slate-400 mb-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-xs font-medium">Duration</span>
                                    </div>
                                    <p class="text-white text-sm font-semibold">{{ floor($duration / 60) }}h {{ $duration % 60 }}m</p>
                                </div>
                            @endif
                            </div>
                        @endif

                        {{-- Referee & Additional Info --}}
                        @if($hasRefSection)
                            <div class="flex flex-wrap items-center gap-4 pt-4 border-t border-slate-800">
                            @if($match->referee)
                                <div class="flex items-center space-x-2 text-sm">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-500">Referee</p>
                                        <p class="text-white font-medium">{{ $match->referee->username ?? $match->referee->name }}</p>
                                    </div>
                                </div>
                            @endif

                            {{-- Rolls Display --}}
                            @if($match->rolls && $match->rolls->count() > 0)
                                <div class="flex items-center space-x-2 text-sm">
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path>
                                    </svg>
                                    <div>
                                        <p class="text-xs text-slate-500">Rolls</p>
                                        <p class="text-white font-medium">
                                            @foreach($match->rolls as $roll)
                                                <span class="text-slate-300">{{ $roll->team->name ?? 'Team' }}</span>: <span class="font-bold">{{ $roll->roll }}</span>{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                        </p>
                                    </div>
                                </div>
                            @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Fill Result Modal --}}
<div id="fillResultModal" class="hidden fixed inset-0 bg-black/50 z-50 overflow-y-auto">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="bg-slate-900 rounded-xl border border-slate-800 max-w-4xl w-full my-8">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <h2 class="text-xl font-bold text-white">Fill Match Result</h2>
            <button type="button" onclick="closeFillResult()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-6">
            {{-- Step 1: Enter osu! Match ID --}}
            <div id="matchIdStep" class="space-y-4">
                {{-- Expected Participants Info --}}
                <div id="expectedParticipantsInfo" class="bg-blue-500/20 border border-blue-500/30 rounded-lg p-4">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-blue-400 mb-2">Expected Participants</h4>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <p class="text-slate-400 mb-1" id="expectedTeam1Label">Team 1</p>
                                    <p class="text-white font-medium" id="expectedTeam1Name">-</p>
                                </div>
                                <div>
                                    <p class="text-slate-400 mb-1" id="expectedTeam2Label">Team 2</p>
                                    <p class="text-white font-medium" id="expectedTeam2Name">-</p>
                                </div>
                            </div>
                            <p class="text-xs text-blue-300 mt-2">Make sure the osu! match includes these participants</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="osu_match_id" class="block text-sm font-medium text-slate-300 mb-2">
                        osu! Match ID
                    </label>
                    <div class="flex gap-3">
                        <input type="text" id="osu_match_id"
                            class="flex-1 px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg text-white focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                            placeholder="e.g., 115584968">
                        <button type="button" onclick="fetchOsuMatch()"
                            class="px-6 py-2.5 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            <span>Fetch Data</span>
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-slate-500">Enter the osu! multiplayer match ID to automatically fetch all game results</p>
                </div>

                <div id="fetchError" class="hidden bg-red-500/20 border border-red-500/30 rounded-lg p-4">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="flex-1">
                            <h4 id="errorTitle" class="text-sm font-semibold text-red-400 mb-1"></h4>
                            <p id="errorDetails" class="text-sm text-red-300 mb-2"></p>
                            <ul id="errorIssues" class="space-y-1 text-sm text-red-300 list-none"></ul>
                        </div>
                    </div>
                </div>

                <div id="fetchLoading" class="hidden">
                    <div class="flex items-center justify-center py-8">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-pink-500"></div>
                    </div>
                </div>
            </div>

            {{-- Step 2: Review and Edit Results --}}
            <div id="resultsStep" class="hidden space-y-4">
                <div class="bg-slate-800/50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-white">Match Overview</h3>
                        <button type="button" onclick="resetModal()" class="text-sm text-slate-400 hover:text-white transition-colors">
                            Change Match ID
                        </button>
                    </div>

                    {{-- Score Summary --}}
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div class="bg-slate-900 rounded-lg p-4 text-center">
                            <p class="text-slate-400 text-sm mb-1" id="team1NameDisplay">Team 1</p>
                            <p class="text-3xl font-bold text-white" id="team1ScoreDisplay">0</p>
                        </div>
                        <div class="flex items-center justify-center">
                            <span class="text-slate-500 font-bold text-lg">vs</span>
                        </div>
                        <div class="bg-slate-900 rounded-lg p-4 text-center">
                            <p class="text-slate-400 text-sm mb-1" id="team2NameDisplay">Team 2</p>
                            <p class="text-3xl font-bold text-white" id="team2ScoreDisplay">0</p>
                        </div>
                    </div>

                    {{-- Winner Display --}}
                    <div id="winnerDisplay" class="bg-green-500/20 border border-green-500/30 rounded-lg p-3 text-center">
                        <p class="text-sm text-green-400 font-semibold">Winner: <span id="winnerName"></span></p>
                    </div>
                </div>

                {{-- Games List --}}
                <div class="space-y-3">
                    <h3 class="text-lg font-bold text-white">Games Played</h3>
                    <div id="gamesList" class="space-y-2">
                        {{-- Populated by JavaScript --}}
                    </div>
                </div>

                {{-- Submit Button --}}
                <form id="fillResultForm" class="pt-4 border-t border-slate-800">
                    @csrf
                    <input type="hidden" id="result_match_id" name="match_id">
                    <input type="hidden" id="result_winner_id" name="winner_id">
                    <input type="hidden" id="result_games_data" name="games">

                    <div class="flex items-center gap-3">
                        <button type="submit" class="flex-1 px-6 py-2.5 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors">
                            Save Match Result
                        </button>
                        <button type="button" onclick="closeFillResult()" class="px-6 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>
</div>

{{-- No-Show Modal --}}
<div id="noShowModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-xl max-w-lg w-full shadow-2xl">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-white">Mark as No-Show</h2>
                <button type="button" onclick="closeNoShowModal()" class="text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="noShowForm">
                @csrf
                <input type="hidden" id="noshow_match_id" name="match_id">

                <div class="space-y-4">
                    <p class="text-slate-300">Select which player/team did not show up or was disqualified. The opposing player/team will automatically receive the win.</p>

                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">Forfeit Type</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center justify-center space-x-2 p-3 bg-slate-800 rounded-lg cursor-pointer hover:bg-slate-700 transition-colors border-2 border-transparent has-[:checked]:border-orange-500">
                                <input type="radio" name="no_show_type" value="no_show" class="w-4 h-4 text-orange-500" required checked>
                                <span class="text-white font-medium">No-Show</span>
                            </label>
                            <label class="flex items-center justify-center space-x-2 p-3 bg-slate-800 rounded-lg cursor-pointer hover:bg-slate-700 transition-colors border-2 border-transparent has-[:checked]:border-red-500">
                                <input type="radio" name="no_show_type" value="disqualified" class="w-4 h-4 text-red-500" required>
                                <span class="text-white font-medium">Disqualified</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">Select Player/Team</label>
                        <div class="space-y-3">
                            <label class="flex items-center space-x-3 p-4 bg-slate-800 rounded-lg cursor-pointer hover:bg-slate-700 transition-colors">
                                <input type="radio" id="noshow_team1_id" name="no_show_team_id" value="" class="w-5 h-5 text-pink-500" required>
                                <span class="text-white font-medium" id="noshowTeam1Label"></span>
                            </label>

                            <label class="flex items-center space-x-3 p-4 bg-slate-800 rounded-lg cursor-pointer hover:bg-slate-700 transition-colors">
                                <input type="radio" id="noshow_team2_id" name="no_show_team_id" value="" class="w-5 h-5 text-pink-500" required>
                                <span class="text-white font-medium" id="noshowTeam2Label"></span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-4">
                        <button type="submit" class="flex-1 px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors">
                            Confirm Forfeit
                        </button>
                        <button type="button" onclick="closeNoShowModal()" class="px-6 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Manual Entry Modal --}}
<div id="manualEntryModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-slate-900 border border-slate-800 rounded-xl max-w-3xl w-full shadow-2xl my-8">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-white">Manual Score Entry</h2>
                <button type="button" onclick="closeManualEntry()" class="text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-blue-400 font-semibold mb-1">Match Participants</p>
                        <div class="flex items-center gap-4">
                            <span class="text-white font-medium" id="manualTeam1Name"></span>
                            <span class="text-slate-500">vs</span>
                            <span class="text-white font-medium" id="manualTeam2Name"></span>
                        </div>
                    </div>
                </div>
            </div>

            <form id="manualEntryForm">
                @csrf
                <input type="hidden" id="manual_match_id" name="match_id">

                {{-- Quick Score Entry --}}
                <div class="bg-green-500/10 border border-green-500/30 rounded-lg p-4 mb-6">
                    <h3 class="text-sm font-semibold text-green-400 mb-2">Quick Score Entry</h3>
                    <p class="text-xs text-slate-400 mb-3">Enter score (e.g., 6-1) and submit to save, or generate maps below for detailed entry.</p>
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1" id="quickScoreTeam1Label">Team 1</label>
                            <input type="number" id="quickScoreTeam1" min="0" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white text-center text-2xl font-bold" placeholder="0">
                        </div>
                        <div class="text-center">
                            <span class="text-slate-500 text-2xl font-bold">-</span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1" id="quickScoreTeam2Label">Team 2</label>
                            <input type="number" id="quickScoreTeam2" min="0" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white text-center text-2xl font-bold" placeholder="0">
                        </div>
                    </div>
                    <button type="button" onclick="generateGamesFromScore()" class="mt-3 w-full px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg transition-colors">
                        Generate Maps from Score
                    </button>
                </div>

                <div class="space-y-4 mb-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white">Individual Maps (Optional)</h3>
                        <button type="button" onclick="addManualGame()" class="px-4 py-2 bg-pink-500 hover:bg-pink-600 text-white text-sm font-medium rounded-lg transition-colors">
                            + Add Map
                        </button>
                    </div>

                    <div id="manualGamesContainer" class="space-y-3">
                        {{-- Populated by JavaScript --}}
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-slate-800">
                    <button type="submit" class="flex-1 px-6 py-2.5 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors">
                        Save Match Result
                    </button>
                    <button type="button" onclick="closeManualEntry()" class="px-6 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@php
    $matchesData = $matches->map(function($match) use ($tournament) {
        $team1 = $tournament->isTeamTournament() ? $match->team1 : $match->player1;
        $team2 = $tournament->isTeamTournament() ? $match->team2 : $match->player2;

        $team1Name = $tournament->isTeamTournament()
            ? ($team1->teamname ?? 'TBD')
            : ($team1->name ?? $team1->username ?? 'TBD');

        $team2Name = $tournament->isTeamTournament()
            ? ($team2->teamname ?? 'TBD')
            : ($team2->name ?? $team2->username ?? 'TBD');

        return [
            'id' => $match->id,
            'best_of' => $match->best_of,
            'mappool_id' => $match->mappool_id,
            'team1_id' => $match->team1_id,
            'team2_id' => $match->team2_id,
            'team1_name' => $team1Name,
            'team2_name' => $team2Name,
        ];
    })->values();
@endphp

<script>
    // Match data for JavaScript
    const matchesData = @json($matchesData);

    // Ensure matches is an array
    const matches = Array.isArray(matchesData) ? matchesData : Object.values(matchesData || {});

    console.log('Matches loaded:', matches);
    console.log('Is array:', Array.isArray(matches));

    let currentMatchId = null;
    let currentMatchData = null;
    let fetchedGames = [];
    let manualGameCounter = 0;

    // Make functions globally available
    window.openFillResult = function(matchId) {
        console.log('openFillResult called with matchId:', matchId);
        const match = matches.find(m => m.id === matchId);
        if (!match) {
            console.error('Match not found:', matchId);
            return;
        }

        currentMatchId = matchId;
        document.getElementById('result_match_id').value = matchId;

        const isTeamTournament = {{ $tournament->isTeamTournament() ? 'true' : 'false' }};

        if (isTeamTournament) {
            document.getElementById('expectedTeam1Label').textContent = 'Team 1';
            document.getElementById('expectedTeam2Label').textContent = 'Team 2';
        } else {
            document.getElementById('expectedTeam1Label').textContent = 'Player 1';
            document.getElementById('expectedTeam2Label').textContent = 'Player 2';
        }

        document.getElementById('expectedTeam1Name').textContent = match.team1_name || 'TBD';
        document.getElementById('expectedTeam2Name').textContent = match.team2_name || 'TBD';

        resetModal();
        document.getElementById('fillResultModal').classList.remove('hidden');
    };

    window.resetModal = function() {
        document.getElementById('matchIdStep').classList.remove('hidden');
        document.getElementById('resultsStep').classList.add('hidden');
        document.getElementById('fetchError').classList.add('hidden');
        document.getElementById('fetchLoading').classList.add('hidden');
        document.getElementById('osu_match_id').value = '';
        fetchedGames = [];
    };

    window.closeFillResult = function() {
        document.getElementById('fillResultModal').classList.add('hidden');
        resetModal();
    };

    window.fetchOsuMatch = async function() {
        const osuMatchId = document.getElementById('osu_match_id').value.trim();

        if (!osuMatchId) {
            showError('Validation Error', 'Please enter a match ID', []);
            return;
        }

        document.getElementById('fetchError').classList.add('hidden');
        document.getElementById('fetchLoading').classList.remove('hidden');

        try {
            const response = await fetch(`{{ route('dashboard.tournaments.matches.fetch-osu-match', $tournament) }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    match_id: currentMatchId,
                    osu_match_id: osuMatchId
                })
            });

            const data = await response.json();

            if (!response.ok) {
                const errorTitle = data.error || 'Error';
                const errorDetails = data.details || 'An unexpected error occurred';
                const errorIssues = data.issues || [];
                showError(errorTitle, errorDetails, errorIssues);
                return;
            }

            displayResults(data);
        } catch (error) {
            console.error('Error:', error);
            showError('Network Error', 'Failed to connect to the server. Please try again.', []);
        } finally {
            document.getElementById('fetchLoading').classList.add('hidden');
        }
    }

    window.showError = function(title, details, issues) {
        const errorDiv = document.getElementById('fetchError');
        const titleEl = document.getElementById('errorTitle');
        const detailsEl = document.getElementById('errorDetails');
        const issuesEl = document.getElementById('errorIssues');

        titleEl.textContent = title;
        detailsEl.textContent = details;

        issuesEl.innerHTML = '';
        if (issues && issues.length > 0) {
            issues.forEach(issue => {
                const li = document.createElement('li');
                li.className = 'flex items-start space-x-2';
                li.innerHTML = `
                    <span class="text-red-400 mt-0.5">•</span>
                    <span>${issue}</span>
                `;
                issuesEl.appendChild(li);
            });
        }

        errorDiv.classList.remove('hidden');
    }

    window.displayResults = function(data) {
        currentMatchData = data;
        fetchedGames = data.games || [];

        const isTeamTournament = {{ $tournament->isTeamTournament() ? 'true' : 'false' }};
        const team1Name = isTeamTournament
            ? (data.match.team1?.teamname || 'Team 1')
            : (data.match.team1?.name || data.match.team1?.username || 'Player 1');
        const team2Name = isTeamTournament
            ? (data.match.team2?.teamname || 'Team 2')
            : (data.match.team2?.name || data.match.team2?.username || 'Player 2');

        document.getElementById('team1NameDisplay').textContent = team1Name;
        document.getElementById('team2NameDisplay').textContent = team2Name;
        document.getElementById('team1ScoreDisplay').textContent = data.team1_score;
        document.getElementById('team2ScoreDisplay').textContent = data.team2_score;

        const winnerName = data.winner_id === data.match.team1?.id ? team1Name : team2Name;
        document.getElementById('winnerName').textContent = winnerName;
        document.getElementById('result_winner_id').value = data.winner_id || '';

        const gamesList = document.getElementById('gamesList');
        gamesList.innerHTML = '';

        fetchedGames.forEach((game, index) => {
            const gameDiv = document.createElement('div');
            gameDiv.className = 'bg-slate-800/50 rounded-lg p-4 border border-slate-700';

            const isWinner1 = game.winning_team_id === data.match.team1?.id;

            gameDiv.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                        <span class="text-slate-400 font-medium">Game ${game.order_in_match}</span>
                        <span class="text-white font-semibold">${game.map?.artist || 'Unknown'} - ${game.map?.title || 'Unknown'}</span>
                        <span class="text-slate-500 text-sm">[${game.map?.version || 'Unknown'}]</span>
                    </div>
                    <button type="button" onclick="removeGame(${index})" class="text-red-400 hover:text-red-300 text-sm">
                        Remove
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-slate-900 rounded p-3 ${isWinner1 ? 'ring-2 ring-green-500/50' : ''}">
                        <p class="text-slate-400 text-xs mb-1">${team1Name}</p>
                        <p class="text-white font-bold text-lg">${game.team1_score.toLocaleString()}</p>
                        ${isWinner1 ? '<p class="text-green-400 text-xs font-semibold mt-1">Winner</p>' : ''}
                    </div>
                    <div class="bg-slate-900 rounded p-3 ${!isWinner1 ? 'ring-2 ring-green-500/50' : ''}">
                        <p class="text-slate-400 text-xs mb-1">${team2Name}</p>
                        <p class="text-white font-bold text-lg">${game.team2_score.toLocaleString()}</p>
                        ${!isWinner1 ? '<p class="text-green-400 text-xs font-semibold mt-1">Winner</p>' : ''}
                    </div>
                </div>
            `;

            gamesList.appendChild(gameDiv);
        });

        document.getElementById('matchIdStep').classList.add('hidden');
        document.getElementById('resultsStep').classList.remove('hidden');
    }

    window.removeGame = function(index) {
        fetchedGames.splice(index, 1);

        const team1Id = currentMatchData.match.team1?.id;
        const team2Id = currentMatchData.match.team2?.id;

        const team1Wins = fetchedGames.filter(g => g.winning_team_id === team1Id).length;
        const team2Wins = fetchedGames.filter(g => g.winning_team_id === team2Id).length;

        currentMatchData.team1_score = team1Wins;
        currentMatchData.team2_score = team2Wins;

        const bestOf = currentMatchData.match.best_of;
        let winnerId = null;

        if (bestOf) {
            const winsNeeded = Math.ceil(bestOf / 2);
            if (team1Wins >= winsNeeded) {
                winnerId = team1Id;
            } else if (team2Wins >= winsNeeded) {
                winnerId = team2Id;
            }
        } else {
            winnerId = team1Wins > team2Wins ? team1Id : (team2Wins > team1Wins ? team2Id : null);
        }

        currentMatchData.winner_id = winnerId;

        displayResults(currentMatchData);
    }

    // Handle fill result form submission
    document.getElementById('fillResultForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);

        const gamesData = fetchedGames.map(game => ({
            mappool_map_id: game.mappool_map_id,
            winning_team_id: game.winning_team_id,
            scores: game.scores
        }));

        const payload = {
            match_id: formData.get('match_id'),
            winner_id: formData.get('winner_id'),
            games: gamesData
        };

        try {
            const response = await fetch(`{{ route('dashboard.tournaments.matches.fill-result', $tournament) }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload)
            });

            if (response.ok) {
                window.location.reload();
            } else {
                const data = await response.json();
                alert(data.message || 'Failed to submit result');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred');
        }
    });

    // Close modal on outside click
    document.getElementById('fillResultModal').addEventListener('click', (e) => {
        if (e.target.id === 'fillResultModal') {
            closeFillResult();
        }
    });

    // No-Show Modal Functions
    window.openNoShowModal = function(matchId) {
        const match = matches.find(m => m.id === matchId);
        if (!match) {
            console.error('Match not found:', matchId);
            return;
        }

        console.log('Opening no-show modal for match:', match);

        currentMatchId = matchId;
        document.getElementById('noshow_match_id').value = matchId;

        const isTeamTournament = {{ $tournament->isTeamTournament() ? 'true' : 'false' }};

        // Set labels
        document.getElementById('noshowTeam1Label').textContent = match.team1_name || 'TBD';
        document.getElementById('noshowTeam2Label').textContent = match.team2_name || 'TBD';

        // Set radio button values
        const team1Radio = document.getElementById('noshow_team1_id');
        const team2Radio = document.getElementById('noshow_team2_id');

        team1Radio.value = match.team1_id;
        team2Radio.value = match.team2_id;

        console.log('Set team1 radio value:', team1Radio.value, 'for', match.team1_name);
        console.log('Set team2 radio value:', team2Radio.value, 'for', match.team2_name);

        // Clear any previous selection
        team1Radio.checked = false;
        team2Radio.checked = false;

        document.getElementById('noShowModal').classList.remove('hidden');
    };

    window.closeNoShowModal = function() {
        document.getElementById('noShowModal').classList.add('hidden');
        document.getElementById('noShowForm').reset();
    }

    // Manual Entry Modal Functions
    window.openManualEntry = async function(matchId) {
        console.log('openManualEntry called with matchId:', matchId);
        const match = matches.find(m => m.id === matchId);
        if (!match) {
            console.error('Match not found for ID:', matchId);
            return;
        }

        currentMatchId = matchId;
        currentMatchData = match;
        console.log('Current match data set:', currentMatchData);
        document.getElementById('manual_match_id').value = matchId;

        const isTeamTournament = {{ $tournament->isTeamTournament() ? 'true' : 'false' }};
        document.getElementById('manualTeam1Name').textContent = match.team1_name || 'TBD';
        document.getElementById('manualTeam2Name').textContent = match.team2_name || 'TBD';

        // Set quick score labels
        document.getElementById('quickScoreTeam1Label').textContent = match.team1_name || 'TBD';
        document.getElementById('quickScoreTeam2Label').textContent = match.team2_name || 'TBD';

        // Fetch mappool maps if mappool exists
        currentMatchData.mappoolMaps = [];
        if (match.mappool_id) {
            try {
                const response = await fetch(`/api/mappools/${match.mappool_id}/maps`);
                if (response.ok) {
                    currentMatchData.mappoolMaps = await response.json();
                }
            } catch (error) {
                console.error('Failed to fetch mappool maps:', error);
            }
        }

        // Reset form
        document.getElementById('manualGamesContainer').innerHTML = '';
        document.getElementById('quickScoreTeam1').value = '';
        document.getElementById('quickScoreTeam2').value = '';
        manualGameCounter = 0;

        document.getElementById('manualEntryModal').classList.remove('hidden');
    };

    window.closeManualEntry = function() {
        document.getElementById('manualEntryModal').classList.add('hidden');
        document.getElementById('manualEntryForm').reset();
    };

    window.generateGamesFromScore = function() {
        if (!currentMatchData) {
            console.error('Cannot generate games: currentMatchData is not set');
            return;
        }

        const team1Score = parseInt(document.getElementById('quickScoreTeam1').value) || 0;
        const team2Score = parseInt(document.getElementById('quickScoreTeam2').value) || 0;

        if (team1Score === 0 && team2Score === 0) {
            alert('Please enter at least one score');
            return;
        }

        const totalMaps = team1Score + team2Score;
        if (totalMaps > 20) {
            alert('Total maps cannot exceed 20');
            return;
        }

        // Clear existing games
        document.getElementById('manualGamesContainer').innerHTML = '';
        manualGameCounter = 0;

        // Generate maps for team 1 wins
        for (let i = 0; i < team1Score; i++) {
            manualGameCounter++;
            const gameHtml = `
                <div class="manual-game bg-slate-800 rounded-lg p-4" data-game-index="${manualGameCounter}">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-white font-semibold">Map ${manualGameCounter}</h4>
                        <button type="button" onclick="removeManualGame(${manualGameCounter})" class="text-red-400 hover:text-red-300 text-sm">Remove</button>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Map Winner</label>
                            <select name="games[${manualGameCounter}][winning_team_id]" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white">
                                <option value="">Select winner...</option>
                                <option value="${currentMatchData.team1_id}" selected>${currentMatchData.team1_name}</option>
                                <option value="${currentMatchData.team2_id}">${currentMatchData.team2_name}</option>
                            </select>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('manualGamesContainer').insertAdjacentHTML('beforeend', gameHtml);
        }

        // Generate maps for team 2 wins
        for (let i = 0; i < team2Score; i++) {
            manualGameCounter++;
            const gameHtml = `
                <div class="manual-game bg-slate-800 rounded-lg p-4" data-game-index="${manualGameCounter}">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-white font-semibold">Map ${manualGameCounter}</h4>
                        <button type="button" onclick="removeManualGame(${manualGameCounter})" class="text-red-400 hover:text-red-300 text-sm">Remove</button>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Map Winner</label>
                            <select name="games[${manualGameCounter}][winning_team_id]" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white">
                                <option value="">Select winner...</option>
                                <option value="${currentMatchData.team1_id}">${currentMatchData.team1_name}</option>
                                <option value="${currentMatchData.team2_id}" selected>${currentMatchData.team2_name}</option>
                            </select>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('manualGamesContainer').insertAdjacentHTML('beforeend', gameHtml);
        }

        console.log(`Generated ${totalMaps} maps: ${team1Score} for ${currentMatchData.team1_name}, ${team2Score} for ${currentMatchData.team2_name}`);
    };

    window.addManualGame = function() {
        if (!currentMatchData) {
            console.error('Cannot add game: currentMatchData is not set');
            return;
        }

        manualGameCounter++;

        // Build mappool options if available
        let mappoolOptions = '<option value="">Not specified</option>';
        if (currentMatchData.mappoolMaps && currentMatchData.mappoolMaps.length > 0) {
            currentMatchData.mappoolMaps.forEach(mapData => {
                const map = mapData.map;
                mappoolOptions += `<option value="${mapData.id}">${mapData.mod || ''} - ${map.artist} - ${map.title} [${map.version}]</option>`;
            });
        }

        const isTeamTournament = {{ $tournament->isTeamTournament() ? 'true' : 'false' }};

        const gameHtml = `
            <div class="manual-game bg-slate-800 rounded-lg p-4" data-game-index="${manualGameCounter}">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-white font-semibold">Map ${manualGameCounter}</h4>
                    <button type="button" onclick="removeManualGame(${manualGameCounter})" class="text-red-400 hover:text-red-300 text-sm">Remove</button>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Beatmap (Optional)</label>
                        <select name="games[${manualGameCounter}][mappool_map_id]" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white text-sm">
                            ${mappoolOptions}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Map Winner</label>
                        <select name="games[${manualGameCounter}][winning_team_id]" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white">
                            <option value="">Select winner...</option>
                            <option value="${currentMatchData.team1_id}">${currentMatchData.team1_name}</option>
                            <option value="${currentMatchData.team2_id}">${currentMatchData.team2_name}</option>
                        </select>
                    </div>

                    <div class="bg-slate-900/50 p-3 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-sm font-medium text-slate-400">Player Scores (Optional)</label>
                            <button type="button" onclick="toggleScoresSection(${manualGameCounter})" class="text-xs text-pink-400 hover:text-pink-300">Show/Hide</button>
                        </div>
                        <div id="scoresSection${manualGameCounter}" class="hidden space-y-2">
                            <p class="text-xs text-slate-500 mb-2">Add individual player scores for this map</p>
                            <div id="scoresContainer${manualGameCounter}"></div>
                            <button type="button" onclick="addPlayerScore(${manualGameCounter})" class="w-full px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white text-xs rounded transition-colors">
                                + Add Player Score
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.getElementById('manualGamesContainer').insertAdjacentHTML('beforeend', gameHtml);
    };

    window.removeManualGame = function(index) {
        const gameElement = document.querySelector(`[data-game-index="${index}"]`);
        if (gameElement) {
            gameElement.remove();
        }
        // Renumber remaining maps
        const games = document.querySelectorAll('.manual-game');
        games.forEach((game, idx) => {
            const header = game.querySelector('h4');
            if (header) {
                header.textContent = `Map ${idx + 1}`;
            }
        });
    }

    window.toggleScoresSection = function(gameIndex) {
        const section = document.getElementById(`scoresSection${gameIndex}`);
        section.classList.toggle('hidden');
    }

    let playerScoreCounters = {};

    window.addPlayerScore = function(gameIndex) {
        if (!playerScoreCounters[gameIndex]) {
            playerScoreCounters[gameIndex] = 0;
        }
        playerScoreCounters[gameIndex]++;
        const scoreIndex = playerScoreCounters[gameIndex];

        const scoreHtml = `
            <div class="bg-slate-800 p-2 rounded space-y-2" data-score-index="${scoreIndex}">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-slate-400">Player ${scoreIndex}</span>
                    <button type="button" onclick="removePlayerScore(${gameIndex}, ${scoreIndex})" class="text-xs text-red-400 hover:text-red-300">Remove</button>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs text-slate-500 mb-1">User ID</label>
                        <input type="number" name="games[${gameIndex}][scores][${scoreIndex}][user_id]" class="w-full px-2 py-1 text-xs bg-slate-900 border border-slate-700 rounded text-white" placeholder="User ID">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-500 mb-1">Score</label>
                        <input type="number" name="games[${gameIndex}][scores][${scoreIndex}][score]" class="w-full px-2 py-1 text-xs bg-slate-900 border border-slate-700 rounded text-white" placeholder="0">
                    </div>
                </div>
            </div>
        `;
        document.getElementById(`scoresContainer${gameIndex}`).insertAdjacentHTML('beforeend', scoreHtml);
    }

    window.removePlayerScore = function(gameIndex, scoreIndex) {
        const scoreElement = document.querySelector(`#scoresContainer${gameIndex} [data-score-index="${scoreIndex}"]`);
        if (scoreElement) {
            scoreElement.remove();
        }
    }

    // No-Show Form Submit
    document.getElementById('noShowForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);

        // Ensure no_show_team_id is sent as integer
        if (data.no_show_team_id) {
            data.no_show_team_id = parseInt(data.no_show_team_id);
        }

        // Debug logging
        console.log('Submitting no-show data:', data);

        // Validate data before sending
        if (!data.match_id || !data.no_show_team_id) {
            alert('Please select which player/team did not show up');
            return;
        }

        try {
            const response = await fetch('{{ route('dashboard.tournaments.matches.mark-no-show', $tournament) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                const result = await response.json();
                console.log('No-show marked successfully:', result);
                window.location.reload();
            } else {
                const result = await response.json();
                console.error('Failed to mark no-show:', result);
                alert(result.message || 'Failed to mark as no-show');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while marking the no-show');
        }
    });

    // Manual Entry Form Submit
    document.getElementById('manualEntryForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(e.target);
        const data = {
            match_id: formData.get('match_id'),
            games: []
        };

        // Check if quick score is filled
        const quickScoreTeam1 = parseInt(document.getElementById('quickScoreTeam1').value) || 0;
        const quickScoreTeam2 = parseInt(document.getElementById('quickScoreTeam2').value) || 0;
        const hasQuickScore = quickScoreTeam1 > 0 || quickScoreTeam2 > 0;

        // Parse detailed maps data
        const games = document.querySelectorAll('.manual-game');
        const hasDetailedMaps = games.length > 0;

        // Scenario 1: Only quick score, no detailed maps
        if (hasQuickScore && !hasDetailedMaps) {
            // Generate maps from quick score
            for (let i = 0; i < quickScoreTeam1; i++) {
                data.games.push({
                    winning_team_id: currentMatchData.team1_id
                });
            }
            for (let i = 0; i < quickScoreTeam2; i++) {
                data.games.push({
                    winning_team_id: currentMatchData.team2_id
                });
            }
        }
        // Scenario 2: Detailed maps (ignore quick score)
        else if (hasDetailedMaps) {
            games.forEach((game, index) => {
                const gameIndex = game.dataset.gameIndex;
                const winningTeamId = parseInt(formData.get(`games[${gameIndex}][winning_team_id]`));

                if (!winningTeamId) {
                    alert(`Please select a winner for Map ${index + 1}`);
                    throw new Error('Missing winner');
                }

                const mappoolMapId = formData.get(`games[${gameIndex}][mappool_map_id]`);

                // Parse player scores if any
                const scoresData = [];
                const scoresContainer = document.getElementById(`scoresContainer${gameIndex}`);
                if (scoresContainer) {
                    const scoreElements = scoresContainer.querySelectorAll('[data-score-index]');
                    scoreElements.forEach(scoreEl => {
                        const scoreIdx = scoreEl.dataset.scoreIndex;
                        const userId = formData.get(`games[${gameIndex}][scores][${scoreIdx}][user_id]`);
                        const score = formData.get(`games[${gameIndex}][scores][${scoreIdx}][score]`);

                        if (userId) {
                            scoresData.push({
                                user_id: parseInt(userId),
                                score: score ? parseInt(score) : null
                            });
                        }
                    });
                }

                const gameData = {
                    winning_team_id: winningTeamId
                };

                // Only add mappool_map_id if it's set
                if (mappoolMapId && mappoolMapId !== '') {
                    gameData.mappool_map_id = parseInt(mappoolMapId);
                }

                // Only add scores if there are any
                if (scoresData.length > 0) {
                    gameData.scores = scoresData;
                }

                console.log(`Map ${index + 1}:`, gameData);
                data.games.push(gameData);
            });
        }
        // Scenario 3: Neither quick score nor detailed maps
        else {
            alert('Please either fill in the quick score or add detailed maps');
            return;
        }

        console.log('Submitting manual entry data:', data);

        try {
            const response = await fetch('{{ route('dashboard.tournaments.matches.fill-result-manual', $tournament) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                const result = await response.json();
                console.log('Manual entry saved successfully:', result);
                window.location.reload();
            } else {
                const result = await response.json();
                console.error('Failed to save manual entry:', result);
                alert(result.message || 'Failed to save manual entry');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while saving manual entry');
        }
    });

    // Close modals on outside click
    document.getElementById('noShowModal')?.addEventListener('click', (e) => {
        if (e.target.id === 'noShowModal') {
            closeNoShowModal();
        }
    });

    document.getElementById('manualEntryModal')?.addEventListener('click', (e) => {
        if (e.target.id === 'manualEntryModal') {
            closeManualEntry();
        }
    });

    // Edit Match Functions
    window.openEditMatch = async function(matchId) {
        console.log('openEditMatch called with matchId:', matchId);
        const match = matches.find(m => m.id === matchId);
        if (!match) {
            console.error('Match not found for ID:', matchId);
            return;
        }

        // Fetch existing match data
        try {
            const response = await fetch(`/dashboard/tournaments/{{ $tournament->id }}/matches/${matchId}/maps`);
            if (!response.ok) {
                throw new Error('Failed to fetch match data');
            }

            const data = await response.json();
            console.log('Existing match data:', data);

            // Open manual entry modal
            currentMatchId = matchId;
            currentMatchData = match;
            document.getElementById('manual_match_id').value = matchId;
            document.getElementById('manualTeam1Name').textContent = match.team1_name || 'TBD';
            document.getElementById('manualTeam2Name').textContent = match.team2_name || 'TBD';

            // Reset and populate form with existing games
            document.getElementById('manualGamesContainer').innerHTML = '';
            manualGameCounter = 0;

            if (data.games && data.games.length > 0) {
                data.games.forEach((game, index) => {
                    manualGameCounter++;
                    const gameHtml = `
                        <div class="manual-game bg-slate-800 rounded-lg p-4" data-game-index="${manualGameCounter}">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-white font-semibold">Map ${manualGameCounter}</h4>
                                <button type="button" onclick="removeManualGame(${manualGameCounter})" class="text-red-400 hover:text-red-300 text-sm">Remove</button>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-slate-400 mb-1">Map Winner</label>
                                    <select name="games[${manualGameCounter}][winning_team_id]" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white">
                                        <option value="">Select winner...</option>
                                        <option value="${currentMatchData.team1_id}" ${game.winning_team_id == currentMatchData.team1_id ? 'selected' : ''}>${currentMatchData.team1_name}</option>
                                        <option value="${currentMatchData.team2_id}" ${game.winning_team_id == currentMatchData.team2_id ? 'selected' : ''}>${currentMatchData.team2_name}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    `;
                    document.getElementById('manualGamesContainer').insertAdjacentHTML('beforeend', gameHtml);
                });
            } else {
                // No existing games, add one empty game
                addManualGame();
            }

            document.getElementById('manualEntryModal').classList.remove('hidden');
        } catch (error) {
            console.error('Error loading match data:', error);
            alert('Failed to load match data. Opening empty form...');
            // Fall back to opening empty manual entry
            openManualEntry(matchId);
        }
    };

    // Debug: Check if functions are defined
    console.log('Functions defined:');
    console.log('- openFillResult:', typeof openFillResult);
    console.log('- openManualEntry:', typeof openManualEntry);
    console.log('- openNoShowModal:', typeof openNoShowModal);
    console.log('- openEditMatch:', typeof openEditMatch);

    // Debug: Test function availability on window
    window.testButtons = function() {
        console.log('Testing button functions...');
        console.log('openFillResult exists:', typeof window.openFillResult !== 'undefined');
        console.log('openManualEntry exists:', typeof window.openManualEntry !== 'undefined');
        console.log('openNoShowModal exists:', typeof window.openNoShowModal !== 'undefined');
        console.log('openEditMatch exists:', typeof window.openEditMatch !== 'undefined');
    };

    console.log('Run testButtons() in console to verify functions are available');
</script>
