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

    let currentMatchId = null;
    let currentMatchData = null;
    let fetchedGames = [];

    function openFillResult(matchId) {
        const match = matches.find(m => m.id === matchId);
        if (!match) return;

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
    }

    function resetModal() {
        document.getElementById('matchIdStep').classList.remove('hidden');
        document.getElementById('resultsStep').classList.add('hidden');
        document.getElementById('fetchError').classList.add('hidden');
        document.getElementById('fetchLoading').classList.add('hidden');
        document.getElementById('osu_match_id').value = '';
        fetchedGames = [];
    }

    function closeFillResult() {
        document.getElementById('fillResultModal').classList.add('hidden');
        resetModal();
    }

    async function fetchOsuMatch() {
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

    function showError(title, details, issues) {
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

    function displayResults(data) {
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

    function removeGame(index) {
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
</script>
