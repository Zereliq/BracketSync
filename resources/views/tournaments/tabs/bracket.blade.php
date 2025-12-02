@php
    $isDashboard = $isDashboard ?? request()->routeIs('dashboard.*');
    $canEdit = auth()->check() && auth()->user()->can('editBracket', $tournament);
    $routePrefix = $isDashboard ? 'dashboard.tournaments.' : 'tournaments.';
    $canGenerateBracket = $canGenerateBracket ?? false;
    $generationErrors = $generationErrors ?? [];
    $needsCustomSeeding = $needsCustomSeeding ?? false;
@endphp

<div class="space-y-6">
    @if($canEdit && $isDashboard)
        {{-- Generate Bracket Button Section --}}
        <div class="flex justify-end">
            @if(!$tournament->matches()->exists())
                @if($canGenerateBracket)
                    <form action="{{ route('dashboard.tournaments.bracket.generate', $tournament) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-6 py-3 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <span>Generate Bracket</span>
                        </button>
                    </form>
                @else
                    <button type="button" disabled class="px-6 py-3 bg-slate-700 text-slate-400 font-medium rounded-lg cursor-not-allowed flex items-center space-x-2" title="Cannot generate bracket yet">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span>Generate Bracket</span>
                    </button>
                @endif
            @endif
        </div>

        {{-- Generation Errors Display (only show when bracket hasn't been generated yet) --}}
        @if(!empty($generationErrors) && !$tournament->matches()->exists())
            <div class="bg-red-500/10 border border-red-500/30 rounded-xl p-6">
                <div class="flex items-start space-x-3">
                    <svg class="w-6 h-6 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-red-400 font-semibold mb-2">Cannot Generate Bracket</h3>
                        <ul class="space-y-1 text-red-300 text-sm">
                            @foreach($generationErrors as $error)
                                <li class="flex items-start space-x-2">
                                    <span class="text-red-400 mt-0.5">•</span>
                                    <span>{{ $error }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    @endif

    @if($tournament->matches()->exists())
        {{-- Bracket Display --}}
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 overflow-x-auto">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-white">{{ ucfirst($tournament->elim_type) }} Elimination Bracket</h2>
                <div class="text-sm text-slate-400">
                    <span class="font-medium text-white">{{ $tournament->bracket_size }}</span> {{ $tournament->isTeamTournament() ? 'team' : 'player' }} bracket
                </div>
            </div>

            @php
                $matches = $tournament->matches()
                    ->with(['team1', 'team2', 'player1', 'player2'])
                    ->orderBy('round')
                    ->orderBy('id')
                    ->get();
                $rounds = $matches->groupBy('round');
                $totalRounds = $rounds->count();
            @endphp

            {{-- Challonge-style horizontal bracket --}}
            <div class="flex gap-8 min-w-max pb-4">
                @foreach($rounds as $roundNumber => $roundMatches)
                    <div class="flex flex-col justify-around min-w-[280px]" style="min-height: {{ 120 * pow(2, $roundNumber - 1) }}px;">
                        {{-- Round Header --}}
                        <div class="text-center mb-4 sticky top-0 bg-slate-900 pb-2 z-10">
                            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider">
                                @if($roundNumber == $totalRounds)
                                    Finals
                                @elseif($roundNumber == $totalRounds - 1)
                                    Semi-Finals
                                @elseif($roundNumber == $totalRounds - 2)
                                    Quarter-Finals
                                @else
                                    Round {{ $roundNumber }}
                                @endif
                            </h3>
                        </div>

                        {{-- Matches in Round --}}
                        <div class="flex flex-col justify-around flex-1 gap-4" style="gap: {{ 40 * pow(2, $roundNumber - 1) }}px;">
                            @foreach($roundMatches as $match)
                                <div class="relative group">
                                    {{-- Match Container --}}
                                    <div class="bg-slate-800 border-2 border-slate-700 rounded-lg overflow-hidden hover:border-pink-500/50 transition-all shadow-lg">
                                        {{-- Participant 1 --}}
                                        <div class="flex items-center justify-between p-3 border-b border-slate-700 {{ $match->winner_team_id == $match->team1_id ? 'bg-green-500/10' : '' }}">
                                            <div class="flex items-center space-x-2 flex-1 min-w-0">
                                                @if($match->team1_seed)
                                                    <span class="text-xs font-bold text-slate-400 flex-shrink-0 w-6">#{{ $match->team1_seed }}</span>
                                                @endif
                                                @if($match->team1_id)
                                                    @if($tournament->isTeamTournament())
                                                        <span class="text-white font-medium truncate">{{ $match->team1->teamname ?? 'TBD' }}</span>
                                                    @else
                                                        <span class="text-white font-medium truncate">{{ $match->player1->name ?? 'TBD' }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-slate-500 italic">TBD</span>
                                                @endif
                                            </div>
                                            @if($match->winner_team_id == $match->team1_id)
                                                <svg class="w-5 h-5 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                            @endif
                                        </div>

                                        {{-- Participant 2 --}}
                                        <div class="flex items-center justify-between p-3 {{ $match->winner_team_id == $match->team2_id ? 'bg-green-500/10' : '' }}">
                                            <div class="flex items-center space-x-2 flex-1 min-w-0">
                                                @if($match->team2_seed)
                                                    <span class="text-xs font-bold text-slate-400 flex-shrink-0 w-6">#{{ $match->team2_seed }}</span>
                                                @endif
                                                @if($match->team2_id)
                                                    @if($tournament->isTeamTournament())
                                                        <span class="text-white font-medium truncate">{{ $match->team2->teamname ?? 'TBD' }}</span>
                                                    @else
                                                        <span class="text-white font-medium truncate">{{ $match->player2->name ?? 'TBD' }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-slate-500 italic">TBD</span>
                                                @endif
                                            </div>
                                            @if($match->winner_team_id == $match->team2_id)
                                                <svg class="w-5 h-5 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                            @endif
                                        </div>

                                        {{-- Match Info Footer --}}
                                        <div class="px-3 py-2 bg-slate-900/50 border-t border-slate-700">
                                            <div class="flex items-center justify-between text-xs">
                                                <span class="text-slate-500">Match {{ $match->id }}</span>
                                                @if($match->status === 'completed')
                                                    <span class="text-green-400 font-medium">Completed</span>
                                                @elseif($match->status === 'in_progress')
                                                    <span class="text-blue-400 font-medium">Live</span>
                                                @else
                                                    <span class="text-slate-500">Pending</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Connector Line to Next Round --}}
                                    @if($roundNumber < $totalRounds)
                                        <div class="absolute top-1/2 -right-8 w-8 h-0.5 bg-slate-700 transform -translate-y-1/2"></div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Scroll hint --}}
            @if($totalRounds > 2)
                <div class="text-center mt-4 text-xs text-slate-500">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                    </svg>
                    Scroll horizontally to view all rounds
                    <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </div>
            @endif
        </div>
    @else
        {{-- Empty State --}}
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 17a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1v-2zM14 17a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1v-2z"></path>
            </svg>
            <h3 class="text-xl font-bold text-slate-400 mb-2">Bracket Not Generated</h3>
            <p class="text-slate-500">
                @if($canEdit && $isDashboard)
                    @if($canGenerateBracket)
                        Click the "Generate Bracket" button above to create the tournament bracket.
                    @else
                        Complete the requirements above to generate the bracket.
                    @endif
                @else
                    The tournament bracket will be displayed here once it's generated by tournament staff.
                @endif
            </p>
        </div>
    @endif
</div>
