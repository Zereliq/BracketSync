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
        'cancelled' => ['color' => 'red', 'label' => 'Cancelled', 'icon' => 'x'],
    ];
@endphp

<div class="space-y-6">
    @if($canEdit)
        {{-- Match Settings Overview --}}
        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
            <div class="bg-gradient-to-r from-slate-800/50 to-slate-900/50 px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <svg class="w-6 h-6 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <h2 class="text-xl font-bold text-white">Match Settings by Round</h2>
                </div>
                <button type="button" onclick="toggleSettings()" id="toggleSettingsBtn" class="text-slate-400 hover:text-white transition-colors">
                    <svg id="toggleIcon" class="w-5 h-5 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>

            <div id="settingsContent" class="hidden">
                <form id="roundSettingsForm" class="p-6">
                    @csrf
                    <div class="space-y-4">
                        @if(!empty($rounds))
                            @foreach($rounds as $round)
                                <div class="bg-slate-800/50 rounded-lg p-5 border border-slate-700 hover:border-slate-600 transition-colors">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 rounded-lg bg-pink-500/20 border border-pink-500/30 flex items-center justify-center">
                                                <span class="text-pink-400 font-bold text-sm">R{{ $round['number'] }}</span>
                                            </div>
                                            <div>
                                                <h3 class="text-white font-semibold">{{ $round['name'] }}</h3>
                                                <p class="text-xs text-slate-400">{{ $round['count'] }} {{ Str::plural('match', $round['count']) }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        {{-- Best Of Setting --}}
                                        <div>
                                            <label for="round_{{ $round['number'] }}_best_of" class="block text-sm font-medium text-slate-300 mb-2">Best Of</label>
                                            <select name="rounds[{{ $round['number'] }}][best_of]" id="round_{{ $round['number'] }}_best_of" class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                                <option value="">Not Set</option>
                                                <option value="1">BO1</option>
                                                <option value="3">BO3</option>
                                                <option value="5">BO5</option>
                                                <option value="7">BO7</option>
                                                <option value="9">BO9</option>
                                                <option value="11">BO11</option>
                                                <option value="13">BO13</option>
                                            </select>
                                        </div>

                                        {{-- Mappool Assignment --}}
                                        <div>
                                            <label for="round_{{ $round['number'] }}_mappool" class="block text-sm font-medium text-slate-300 mb-2">Mappool</label>
                                            <select name="rounds[{{ $round['number'] }}][mappool_id]" id="round_{{ $round['number'] }}_mappool" class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                                <option value="">No Mappool</option>
                                                @foreach($tournament->mappools ?? [] as $mappool)
                                                    <option value="{{ $mappool->id }}">{{ $mappool->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="flex items-center gap-3 pt-6 border-t border-slate-700 mt-6">
                        <button type="submit" class="px-6 py-2.5 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Save All Settings</span>
                        </button>
                        <button type="button" onclick="loadCurrentSettings()" class="px-6 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors">
                            Reset to Current
                        </button>
                    </div>
                </form>
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

                    // Get game count
                    $gameCount = $match->games ? $match->games->count() : 0;

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
                                    {{-- Fill Result Button (only if match is not completed) --}}
                                    @if($match->status !== 'completed')
                                        <button type="button"
                                                onclick="openFillResult({{ $match->id }})"
                                                class="px-4 py-2 bg-pink-500 hover:bg-pink-600 text-white text-sm font-medium rounded-lg transition-colors flex items-center space-x-2"
                                                title="Fill match result">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>Fill Result</span>
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
                            $hasMatchDetails = $match->scheduled_at || $duration || $gameCount > 0 || $match->mappool;
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
                                        </div>
                                        <div class="flex flex-col items-end">
                                            <div class="text-3xl font-bold {{ $team1Won ? 'text-green-400' : 'text-white' }}">
                                                {{ $match->games->where('winning_team_id', $team1->id)->count() }}
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
                                                {{ $match->games->where('winning_team_id', $team2->id)->count() }}
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
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 {{ $hasRefSection ? 'mb-4' : '' }}">
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

                            {{-- Games Played --}}
                            @if($gameCount > 0)
                                <div class="bg-slate-800/30 rounded-lg p-3 border border-slate-800">
                                    <div class="flex items-center space-x-2 text-slate-400 mb-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                                        </svg>
                                        <span class="text-xs font-medium">Maps Played</span>
                                    </div>
                                    <p class="text-white text-sm font-semibold">{{ $gameCount }} {{ Str::plural('map', $gameCount) }}</p>
                                </div>
                            @endif

                            {{-- Mappool --}}
                            @if($match->mappool)
                                <div class="bg-slate-800/30 rounded-lg p-3 border border-slate-800">
                                    <div class="flex items-center space-x-2 text-slate-400 mb-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="text-xs font-medium">Mappool</span>
                                    </div>
                                    <p class="text-white text-sm font-semibold truncate">{{ $match->mappool->name }}</p>
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
<div id="fillResultModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-slate-900 rounded-xl border border-slate-800 max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-slate-800">
            <h2 class="text-xl font-bold text-white">Fill Match Result</h2>
        </div>
        <form id="fillResultForm" class="p-6 space-y-4">
            @csrf
            <input type="hidden" id="result_match_id" name="match_id">

            <div class="bg-slate-800/50 rounded-lg p-4 mb-4">
                <p class="text-sm text-slate-400 mb-2">Select the winning team/player:</p>
                <div id="resultParticipants" class="space-y-2">
                    {{-- Populated by JavaScript --}}
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4">
                <button type="submit" class="flex-1 px-6 py-2.5 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors">
                    Submit Result
                </button>
                <button type="button" onclick="closeFillResult()" class="flex-1 px-6 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors">
                    Cancel
                </button>
            </div>
        </form>
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
    });
@endphp

<script>
    // Match data for JavaScript
    const matches = @json($matchesData);
    const rounds = @json($rounds);

    // Toggle settings panel
    function toggleSettings() {
        const content = document.getElementById('settingsContent');
        const icon = document.getElementById('toggleIcon');

        content.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    }

    // Load current settings into form
    function loadCurrentSettings() {
        // Group matches by round
        const matchesByRound = {};
        matches.forEach(match => {
            if (!matchesByRound[match.round]) {
                matchesByRound[match.round] = [];
            }
            matchesByRound[match.round].push(match);
        });

        // Set form values based on first match in each round
        rounds.forEach(round => {
            const roundNumber = round.number;
            const roundMatches = matchesByRound[roundNumber] || [];

            if (roundMatches.length > 0) {
                const firstMatch = roundMatches[0];

                const bestOfSelect = document.getElementById(`round_${roundNumber}_best_of`);
                const mappoolSelect = document.getElementById(`round_${roundNumber}_mappool`);

                if (bestOfSelect) bestOfSelect.value = firstMatch.best_of || '';
                if (mappoolSelect) mappoolSelect.value = firstMatch.mappool_id || '';
            }
        });
    }

    // Load settings on page load
    document.addEventListener('DOMContentLoaded', () => {
        loadCurrentSettings();
    });

    function openFillResult(matchId) {
        const match = matches.find(m => m.id === matchId);
        if (!match) return;

        document.getElementById('result_match_id').value = matchId;

        const participantsDiv = document.getElementById('resultParticipants');
        participantsDiv.innerHTML = `
            <label class="flex items-center space-x-3 p-3 bg-slate-800 rounded-lg cursor-pointer hover:bg-slate-700 transition-colors">
                <input type="radio" name="winner_id" value="${match.team1_id}" class="w-4 h-4 text-pink-500 bg-slate-800 border-slate-700 focus:ring-pink-500">
                <span class="text-white font-medium">${match.team1_name}</span>
            </label>
            <label class="flex items-center space-x-3 p-3 bg-slate-800 rounded-lg cursor-pointer hover:bg-slate-700 transition-colors">
                <input type="radio" name="winner_id" value="${match.team2_id}" class="w-4 h-4 text-pink-500 bg-slate-800 border-slate-700 focus:ring-pink-500">
                <span class="text-white font-medium">${match.team2_name}</span>
            </label>
        `;

        document.getElementById('fillResultModal').classList.remove('hidden');
    }

    function closeFillResult() {
        document.getElementById('fillResultModal').classList.add('hidden');
    }

    // Handle round settings form submission
    document.getElementById('roundSettingsForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);

        try {
            const response = await fetch(`{{ route('dashboard.tournaments.matches.update-round-settings', $tournament) }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'Accept': 'application/json',
                },
                body: formData
            });

            if (response.ok) {
                window.location.reload();
            } else {
                alert('Failed to update match settings');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred');
        }
    });

    // Handle fill result form submission
    document.getElementById('fillResultForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const matchId = formData.get('match_id');

        if (!formData.get('winner_id')) {
            alert('Please select a winner');
            return;
        }

        try {
            const response = await fetch(`{{ route('dashboard.tournaments.matches.fill-result', $tournament) }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'Accept': 'application/json',
                },
                body: formData
            });

            if (response.ok) {
                window.location.reload();
            } else {
                alert('Failed to submit result');
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
</script>
