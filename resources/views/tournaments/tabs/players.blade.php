@php
    $isDashboard = $isDashboard ?? request()->routeIs('dashboard.*');
    $canEdit = auth()->check() && auth()->user()->can('update', $tournament);
    $teams = $teams ?? collect();
    $routePrefix = $isDashboard ? 'dashboard.tournaments.' : 'tournaments.';
@endphp

<div class="space-y-6">
    {{-- Registration Status & Actions (Public View Only) --}}
    @if(!$isDashboard)
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <h2 class="text-xl font-bold text-white mb-4">Registration Status</h2>

            @if($signedUp)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-white font-medium">You are registered for this tournament</span>
                    </div>
                    <form action="{{ route('tournaments.players.withdraw', $tournament) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
                                onclick="return confirm('Are you sure you want to withdraw from this tournament?')">
                            Withdraw
                        </button>
                    </form>
                </div>
            @elseif($canSignup)
                <div class="space-y-4">
                    <p class="text-slate-300">You are eligible to sign up for this tournament.</p>

                    @if($isTeamTournament)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <form action="{{ route('tournaments.players.signup', $tournament) }}" method="POST">
                                @csrf
                                <input type="hidden" name="as_captain" value="1">
                                <button type="submit"
                                        class="w-full px-6 py-3 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors">
                                    Sign up as Captain
                                </button>
                                <p class="text-sm text-slate-400 mt-2">Create a new team and become its captain</p>
                            </form>

                            <form action="{{ route('tournaments.players.signup', $tournament) }}" method="POST">
                                @csrf
                                <input type="hidden" name="as_captain" value="0">
                                <button type="submit"
                                        class="w-full px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors">
                                    Sign up as Player
                                </button>
                                <p class="text-sm text-slate-400 mt-2">Join as a free agent looking for a team</p>
                            </form>
                        </div>
                    @else
                        <form action="{{ route('tournaments.players.signup', $tournament) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="px-6 py-3 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors">
                                Sign Up for Tournament
                            </button>
                        </form>
                    @endif
                </div>
            @else
                <div class="flex items-center space-x-3">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span class="text-yellow-400">{{ $signupError }}</span>
                </div>
            @endif

            {{-- Signup Window Info --}}
            @if($tournament->signup_start || $tournament->signup_end)
                <div class="mt-4 pt-4 border-t border-slate-800">
                    <div class="text-sm text-slate-400 space-y-1">
                        @if($tournament->signup_start)
                            <p><span class="font-medium">Opens:</span> {{ $tournament->signup_start->format('M j, Y g:i A') }}</p>
                        @endif
                        @if($tournament->signup_end)
                            <p><span class="font-medium">Closes:</span> {{ $tournament->signup_end->format('M j, Y g:i A') }}</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Registration Section (Dashboard) --}}
    @if($isDashboard && auth()->check())
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <h2 class="text-xl font-bold text-white mb-4">Registration</h2>

            @if($signedUp)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-white font-medium">You are registered for this tournament</span>
                    </div>
                    <form action="{{ route('tournaments.players.withdraw', $tournament) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
                                onclick="return confirm('Are you sure you want to withdraw from this tournament?')">
                            Withdraw
                        </button>
                    </form>
                </div>
            @elseif($canSignup)
                <div class="space-y-4">
                    <p class="text-slate-300">
                        @if($isTeamTournament)
                            Register for this tournament. You can join or create teams in the Teams tab after registering.
                        @else
                            Register for this tournament.
                        @endif
                    </p>

                    <form action="{{ route('tournaments.players.signup', $tournament) }}" method="POST" class="space-y-4">
                        @csrf

                        @if($isTeamTournament)
                            <div class="flex items-center space-x-3 p-4 bg-slate-800 rounded-lg border border-slate-700">
                                <input type="checkbox"
                                       name="looking_for_team"
                                       id="looking_for_team"
                                       value="1"
                                       class="w-5 h-5 rounded border-slate-600 bg-slate-700 text-pink-500 focus:ring-pink-500 focus:ring-offset-slate-900">
                                <label for="looking_for_team" class="text-sm text-slate-300 cursor-pointer">
                                    <span class="font-medium text-white">I'm looking for a team</span>
                                    <span class="block text-xs text-slate-400 mt-0.5">Check this if you want other players to know you're available to join a team</span>
                                </label>
                            </div>
                        @endif

                        <button type="submit"
                                class="px-6 py-3 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors">
                            Register
                        </button>
                    </form>
                </div>
            @else
                <div class="flex items-center space-x-3">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span class="text-yellow-400">{{ $signupError }}</span>
                </div>
            @endif

            {{-- Signup Window Info --}}
            @if($tournament->signup_start || $tournament->signup_end)
                <div class="mt-4 pt-4 border-t border-slate-800">
                    <div class="text-sm text-slate-400 space-y-1">
                        @if($tournament->signup_start)
                            <p><span class="font-medium">Opens:</span> {{ $tournament->signup_start->format('M j, Y g:i A') }}</p>
                        @endif
                        @if($tournament->signup_end)
                            <p><span class="font-medium">Closes:</span> {{ $tournament->signup_end->format('M j, Y g:i A') }}</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Registered Players List --}}
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <h2 class="text-xl font-bold text-white mb-4">
            Registered Players
            <span class="text-slate-400 text-base font-normal ml-2">({{ $registeredPlayers->count() }})</span>
        </h2>

        @if($registeredPlayers->isEmpty())
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h3 class="text-xl font-bold text-slate-400 mb-2">No Players Registered</h3>
                <p class="text-slate-500">No players have registered for this tournament yet.</p>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                @foreach($registeredPlayers as $registration)
                    @php
                        $player = $registration->user;
                    @endphp
                    <div class="relative group bg-slate-800/50 rounded-lg border border-slate-700 hover:border-pink-500/50 transition-all hover:shadow-lg hover:shadow-pink-500/10 overflow-hidden">
                        @if($canEdit)
                            <button type="button" class="absolute top-2 right-2 z-10 opacity-0 group-hover:opacity-100 bg-slate-900/90 hover:bg-red-600 text-slate-400 hover:text-white rounded-full p-1.5 transition-all" title="Remove player">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        @endif

                        <div class="p-4 flex flex-col items-center text-center">
                            {{-- Avatar --}}
                            @if($player->avatar_url)
                                <img src="{{ $player->avatar_url }}" alt="{{ $player->name }}" class="w-20 h-20 rounded-full border-2 border-slate-700 group-hover:border-pink-500/50 transition-colors mb-3">
                            @else
                                <div class="w-20 h-20 rounded-full bg-slate-700 group-hover:bg-slate-600 flex items-center justify-center border-2 border-slate-700 group-hover:border-pink-500/50 transition-all mb-3">
                                    <span class="text-slate-300 text-2xl font-medium">{{ substr($player->name, 0, 1) }}</span>
                                </div>
                            @endif

                            {{-- Player Name --}}
                            <h3 class="text-white font-semibold text-sm mb-2 truncate w-full px-1">{{ $player->name }}</h3>

                            {{-- Country Code --}}
                            @if($player->country_code)
                                <div class="inline-flex items-center px-2 py-0.5 bg-slate-700/50 rounded text-xs text-slate-300 font-medium mb-3">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                                    </svg>
                                    <span class="uppercase">{{ $player->country_code }}</span>
                                </div>
                            @endif

                            {{-- Stats Grid --}}
                            @if($player->rank || $player->pp || $player->hit_accuracy)
                                <div class="w-full space-y-2 border-t border-slate-700/50 pt-3">
                                    @if($player->rank)
                                        <div class="flex items-center justify-between px-2">
                                            <span class="text-xs text-slate-400">Rank</span>
                                            <span class="text-sm font-semibold text-pink-400">#{{ number_format($player->rank) }}</span>
                                        </div>
                                    @endif
                                    @if($player->pp)
                                        <div class="flex items-center justify-between px-2">
                                            <span class="text-xs text-slate-400">PP</span>
                                            <span class="text-sm font-semibold text-purple-400">{{ number_format($player->pp) }}</span>
                                        </div>
                                    @endif
                                    @if($player->hit_accuracy)
                                        <div class="flex items-center justify-between px-2">
                                            <span class="text-xs text-slate-400">Accuracy</span>
                                            <span class="text-sm font-semibold text-blue-400">{{ number_format($player->hit_accuracy, 2) }}%</span>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            {{-- Looking for Team Badge --}}
                            @if($isTeamTournament && $registration->looking_for_team)
                                <div class="mt-3 w-full">
                                    <span class="inline-flex items-center px-2 py-1 bg-blue-500/20 text-blue-400 text-xs font-medium rounded border border-blue-500/30">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        Looking for Team
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
