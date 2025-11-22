@php
    $isDashboard = request()->routeIs('dashboard.*');
    $canEdit = $isDashboard && auth()->check() && auth()->user()->can('update', $tournament);
    $teams = $teams ?? collect();
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($registeredPlayers as $registration)
                    @php
                        $player = $registration->user;
                    @endphp
                    <div class="flex items-center justify-between p-4 bg-slate-800/50 rounded-lg border border-slate-700 hover:border-pink-500/30 transition-colors">
                        <div class="flex items-center space-x-3 flex-1 min-w-0">
                            @if($player->avatar_url)
                                <img src="{{ $player->avatar_url }}" alt="{{ $player->name }}" class="w-14 h-14 rounded-full border-2 border-slate-700">
                            @else
                                <div class="w-14 h-14 rounded-full bg-slate-700 flex items-center justify-center">
                                    <span class="text-slate-300 text-lg font-medium">{{ substr($player->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2">
                                    <p class="text-white font-semibold truncate text-lg">{{ $player->name }}</p>
                                    @if($isTeamTournament && $registration->looking_for_team)
                                        <span class="px-2 py-0.5 bg-blue-500/20 text-blue-400 text-xs font-medium rounded border border-blue-500/30 whitespace-nowrap">
                                            Looking for Team
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-3 mt-1 text-xs text-slate-400">
                                    @if($player->country_code)
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                                            </svg>
                                            <span class="uppercase font-medium">{{ $player->country_code }}</span>
                                        </div>
                                    @endif
                                    @if($player->rank)
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                            </svg>
                                            <span class="font-medium">#{{ number_format($player->rank) }}</span>
                                        </div>
                                    @endif
                                    @if($player->pp)
                                        <div class="flex items-center space-x-1">
                                            <span class="font-medium">{{ number_format($player->pp) }}pp</span>
                                        </div>
                                    @endif
                                    @if($player->hit_accuracy)
                                        <div class="flex items-center space-x-1">
                                            <span class="font-medium">{{ number_format($player->hit_accuracy, 2) }}%</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($canEdit)
                            <button type="button" class="text-slate-400 hover:text-red-400 transition-colors ml-3" title="Remove player">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
