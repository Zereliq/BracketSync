@php
    $isDashboard = $isDashboard ?? request()->routeIs('dashboard.*');
    $canEdit = auth()->check() && auth()->user()->can('update', $tournament);
    $teams = $teams ?? collect();
    $routePrefix = $isDashboard ? 'dashboard.tournaments.' : 'tournaments.';
@endphp

<div class="space-y-6">
    {{-- Team Creation Form --}}
    @if($isTeamTournament && auth()->check())
        @php
            $user = auth()->user();
            $userIsRegistered = \App\Models\TournamentPlayer::where('tournament_id', $tournament->id)
                ->where('user_id', $user->id)
                ->exists();
            $userIsOnTeam = \App\Models\TeamUser::whereHas('team', function($q) use ($tournament) {
                $q->where('tournament_id', $tournament->id);
            })->where('user_id', $user->id)->exists();
        @endphp

        @if($userIsRegistered && !$userIsOnTeam)
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <h3 class="text-lg font-bold text-white mb-4">Create Your Team</h3>
                <form action="{{ route('dashboard.tournaments.teams.store', $tournament) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="teamname" class="block text-sm font-medium text-slate-300 mb-2">Team Name</label>
                        <input type="text"
                               name="teamname"
                               id="teamname"
                               required
                               class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white placeholder-slate-500 focus:border-pink-500 focus:ring-1 focus:ring-pink-500"
                               placeholder="Enter team name...">
                    </div>
                    <button type="submit" class="px-6 py-2.5 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors">
                        Create Team
                    </button>
                </form>
            </div>
        @endif
    @endif

    @if(!$isTeamTournament)
        <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-xl p-4">
            <div class="flex items-start space-x-3">
                <svg class="w-5 h-5 text-yellow-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div class="text-sm text-yellow-300">
                    <p class="font-medium mb-1">Solo Tournament</p>
                    <p>This is a 1v1 tournament. Teams are not applicable. See the Players tab for registrations.</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Teams List --}}
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <h2 class="text-xl font-bold text-white mb-4">
            Teams
            <span class="text-slate-400 text-base font-normal ml-2">({{ $teams->count() }})</span>
        </h2>

        @if($teams->isEmpty())
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h3 class="text-xl font-bold text-slate-400 mb-2">No Teams Yet</h3>
                <p class="text-slate-500">Teams will appear here once players register.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4">
                @foreach($teams as $team)
                    <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-6 hover:border-slate-600 transition-colors">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 rounded-lg bg-gradient-to-br from-pink-500 to-purple-600 flex items-center justify-center">
                                    <span class="text-white text-2xl font-bold">{{ substr($team->name, 0, 2) }}</span>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">{{ $team->name }}</h3>
                                    <p class="text-sm text-slate-400">
                                        {{ $team->members->count() }} / {{ $tournament->max_teamsize }}
                                        {{ Str::plural('member', $team->members->count()) }}
                                    </p>
                                </div>
                            </div>
                            @php
                                $userIsCaptain = auth()->check() && $team->members->where('id', auth()->id())->where('pivot.is_captain', true)->isNotEmpty();
                            @endphp
                            @if($userIsCaptain)
                                <div class="flex items-center space-x-2">
                                    <button type="button"
                                            onclick="toggleInviteModal{{ $team->id }}()"
                                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                        Invite Player
                                    </button>
                                    <form action="{{ route('dashboard.teams.destroy', $team) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Are you sure you want to delete this team?')"
                                                class="text-slate-400 hover:text-red-400 transition-colors"
                                                title="Delete team">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        @if($team->members->isNotEmpty())
                            <div class="border-t border-slate-700 pt-4 mt-4">
                                <h4 class="text-sm font-medium text-slate-400 mb-3">Team Members</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach($team->members as $member)
                                        <div class="flex items-center space-x-3 p-3 bg-slate-900/50 rounded-lg">
                                            @if($member->avatar_url)
                                                <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}" class="w-12 h-12 rounded-full border-2 border-slate-700">
                                            @else
                                                <div class="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center">
                                                    <span class="text-slate-300 text-sm font-medium">{{ substr($member->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <p class="text-white font-medium truncate">{{ $member->name }}</p>
                                                <div class="flex items-center space-x-2 text-xs text-slate-400">
                                                    @if($member->country_code)
                                                        <span class="uppercase">{{ $member->country_code }}</span>
                                                    @endif
                                                    @if($member->rank)
                                                        <span>#{{ number_format($member->rank) }}</span>
                                                    @endif
                                                    @if($member->pp)
                                                        <span>{{ number_format($member->pp) }}pp</span>
                                                    @endif
                                                    @if($member->hit_accuracy)
                                                        <span>{{ number_format($member->hit_accuracy, 2) }}%</span>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($member->pivot && $member->pivot->is_captain)
                                                <span class="px-2 py-1 bg-pink-500/20 text-pink-400 text-xs font-medium rounded border border-pink-500/30">
                                                    Captain
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    {{-- Invite Modal for each team --}}
                    @if($userIsCaptain && isset($playersWithoutTeam))
                        <div id="inviteModal{{ $team->id }}" class="hidden mt-4 p-4 bg-slate-800 border border-slate-700 rounded-lg">
                            <h4 class="text-sm font-bold text-white mb-3">Invite Player to Team</h4>
                            @if($playersWithoutTeam->isNotEmpty())
                                <div class="space-y-2 max-h-64 overflow-y-auto">
                                    @foreach($playersWithoutTeam as $registration)
                                        <form action="{{ route('dashboard.teams.invite', $team) }}" method="POST" class="flex items-center justify-between p-3 bg-slate-900/50 rounded-lg hover:bg-slate-900 transition-colors">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $registration->user->id }}">
                                            <div class="flex items-center space-x-3 flex-1">
                                                @if($registration->user->avatar_url)
                                                    <img src="{{ $registration->user->avatar_url }}" alt="{{ $registration->user->name }}" class="w-10 h-10 rounded-full">
                                                @else
                                                    <div class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center">
                                                        <span class="text-slate-300 text-sm">{{ substr($registration->user->name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                                <div>
                                                    <p class="text-white font-medium">{{ $registration->user->name }}</p>
                                                    @if($registration->looking_for_team)
                                                        <span class="text-xs text-blue-400">Looking for Team</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <button type="submit" class="px-3 py-1.5 bg-pink-500 hover:bg-pink-600 text-white text-sm font-medium rounded transition-colors">
                                                Invite
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-slate-400">No available players to invite.</p>
                            @endif
                        </div>

                        <script>
                            function toggleInviteModal{{ $team->id }}() {
                                const modal = document.getElementById('inviteModal{{ $team->id }}');
                                modal.classList.toggle('hidden');
                            }
                        </script>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>
