@php
    $isDashboard = $isDashboard ?? request()->routeIs('dashboard.*');
    $canEdit = auth()->check() && auth()->user()->can('editPlayers', $tournament);
    $teams = $teams ?? collect();
    $routePrefix = $isDashboard ? 'dashboard.tournaments.' : 'tournaments.';
@endphp

<div class="space-y-6">
    {{-- Pending Invitation (Public View Only) --}}
    @if(!$isDashboard && $pendingInvitation)
        <div class="bg-slate-900 border border-blue-500/50 rounded-xl p-6">
            <h2 class="text-xl font-bold text-white mb-4">Tournament Invitation</h2>
            <div class="space-y-4">
                <div class="flex items-start space-x-3 p-4 bg-blue-500/10 rounded-lg border border-blue-500/30">
                    <svg class="w-6 h-6 text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"></path>
                    </svg>
                    <div class="flex-1">
                        <p class="text-white font-medium">You've been invited to this tournament!</p>
                        <p class="text-sm text-slate-300 mt-1">
                            <span class="text-blue-400">{{ $pendingInvitation->inviter->name }}</span> has invited you to participate in this tournament.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <form action="{{ route('tournaments.invitations.accept', [$tournament, $pendingInvitation]) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit"
                                class="w-full px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors">
                            Accept Invitation
                        </button>
                    </form>
                    <form action="{{ route('tournaments.invitations.decline', [$tournament, $pendingInvitation]) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit"
                                class="w-full px-6 py-3 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors"
                                onclick="return confirm('Are you sure you want to decline this invitation?')">
                            Decline
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

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

    {{-- Invite Players Section (Dashboard - Staff Only) --}}
    @if($isDashboard && $canEdit)
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <h2 class="text-xl font-bold text-white mb-4">
                @if($tournament->signup_method === 'invitationals')
                    Invite Players
                @else
                    Add Player
                @endif
            </h2>
            <p class="text-slate-300 mb-4">
                @if($tournament->signup_method === 'invitationals')
                    Search for players to invite to this tournament.
                @else
                    Manually add players to this tournament.
                @endif
            </p>

            <form action="{{ route('dashboard.tournaments.invitations.store', $tournament) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="user_search" class="block text-sm font-medium text-slate-300 mb-2">Search for Player</label>
                    <div class="relative">
                        <input type="text"
                               id="user_search"
                               placeholder="Type a username to search..."
                               class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        <div id="search_results" class="absolute z-10 w-full mt-1 bg-slate-800 border border-slate-700 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto"></div>
                    </div>
                    <input type="hidden" name="user_id" id="selected_user_id">
                    <div id="selected_user" class="mt-2 hidden">
                        <div class="flex items-center justify-between p-3 bg-slate-800 rounded-lg border border-slate-700">
                            <div class="flex items-center space-x-3">
                                <img id="selected_user_avatar" src="" alt="" class="w-10 h-10 rounded-full">
                                <div>
                                    <p id="selected_user_name" class="text-white font-medium"></p>
                                    <p id="selected_user_rank" class="text-sm text-slate-400"></p>
                                </div>
                            </div>
                            <button type="button" onclick="clearSelection()" class="text-slate-400 hover:text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit"
                        id="invite_button"
                        disabled
                        class="px-6 py-3 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    @if($tournament->signup_method === 'invitationals')
                        Send Invitation
                    @else
                        Add Player
                    @endif
                </button>
            </form>

            <script>
                let searchTimeout;
                const searchInput = document.getElementById('user_search');
                const searchResults = document.getElementById('search_results');
                const selectedUserDiv = document.getElementById('selected_user');
                const selectedUserIdInput = document.getElementById('selected_user_id');
                const inviteButton = document.getElementById('invite_button');

                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    const query = this.value.trim();

                    if (query.length < 2) {
                        searchResults.classList.add('hidden');
                        return;
                    }

                    searchTimeout = setTimeout(() => {
                        fetch(`{{ route('dashboard.users.search') }}?q=${encodeURIComponent(query)}`)
                            .then(response => response.json())
                            .then(users => {
                                if (users.length === 0) {
                                    searchResults.innerHTML = '<div class="p-3 text-slate-400 text-sm">No users found</div>';
                                } else {
                                    searchResults.innerHTML = users.map(user => `
                                        <div class="p-3 hover:bg-slate-700 cursor-pointer border-b border-slate-700 last:border-0"
                                             onclick='selectUser(${JSON.stringify(user)})'>
                                            <div class="flex items-center space-x-3">
                                                <img src="${user.avatar_url || '/images/default-avatar.png'}" alt="${user.name}" class="w-8 h-8 rounded-full">
                                                <div class="flex-1">
                                                    <p class="text-white font-medium text-sm">${user.name}</p>
                                                    ${user.osu_username ? `<p class="text-xs text-slate-500">@${user.osu_username}</p>` : ''}
                                                    <div class="flex items-center gap-2 mt-0.5">
                                                        ${user.rank ? `<span class="text-xs text-slate-400">Rank: #${user.rank.toLocaleString()}</span>` : ''}
                                                        ${user.country_code ? `<span class="text-xs text-slate-400 uppercase">${user.country_code}</span>` : ''}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `).join('');
                                }
                                searchResults.classList.remove('hidden');
                            });
                    }, 300);
                });

                function selectUser(user) {
                    selectedUserIdInput.value = user.id;
                    document.getElementById('selected_user_avatar').src = user.avatar_url;
                    document.getElementById('selected_user_name').textContent = user.name;
                    document.getElementById('selected_user_rank').textContent = user.rank ? `Rank: #${user.rank.toLocaleString()}` : '';
                    selectedUserDiv.classList.remove('hidden');
                    searchResults.classList.add('hidden');
                    searchInput.value = '';
                    inviteButton.disabled = false;
                }

                function clearSelection() {
                    selectedUserIdInput.value = '';
                    selectedUserDiv.classList.add('hidden');
                    inviteButton.disabled = true;
                }

                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                        searchResults.classList.add('hidden');
                    }
                });
            </script>
        </div>
    @endif

    {{-- Pending Invitations (Staff Only) --}}
    @if($isDashboard && $canEdit && isset($pendingInvitations) && $pendingInvitations->isNotEmpty())
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <h2 class="text-xl font-bold text-white mb-4">
                Pending Invitations
                <span class="text-slate-400 text-base font-normal ml-2">({{ $pendingInvitations->count() }})</span>
            </h2>
            <p class="text-slate-400 text-sm mb-4">These players have been invited but haven't accepted yet.</p>

            <div class="space-y-3">
                @foreach($pendingInvitations as $invitation)
                    <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-4 hover:border-slate-600 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                @if($invitation->user->avatar_url)
                                    <img src="{{ $invitation->user->avatar_url }}" alt="{{ $invitation->user->name }}" class="w-12 h-12 rounded-full border-2 border-slate-700">
                                @else
                                    <div class="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center">
                                        <span class="text-slate-300 text-sm font-medium">{{ substr($invitation->user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-white font-medium">{{ $invitation->user->name }}</p>
                                    @if($invitation->user->osu_username)
                                        <p class="text-xs text-slate-500">@{{ $invitation->user->osu_username }}</p>
                                    @endif
                                    <div class="flex items-center gap-2 mt-1">
                                        @if($invitation->user->country_code)
                                            <span class="text-xs text-slate-400 uppercase">{{ $invitation->user->country_code }}</span>
                                        @endif
                                        <span class="text-xs text-slate-500">•</span>
                                        <span class="text-xs text-slate-400">Invited by {{ $invitation->inviter->name ?? 'Unknown' }}</span>
                                        <span class="text-xs text-slate-500">•</span>
                                        <span class="text-xs text-slate-400">{{ $invitation->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-3 py-1 bg-yellow-500/10 text-yellow-400 text-xs font-medium rounded-full border border-yellow-500/30">
                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Pending
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
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
                            <form action="{{ route('dashboard.tournaments.players.remove', [$tournament, $registration]) }}" method="POST" class="absolute top-2 right-2 z-10 opacity-0 group-hover:opacity-100">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-slate-900/90 hover:bg-red-600 text-slate-400 hover:text-white rounded-full p-1.5 transition-all" title="Remove player" onclick="return confirm('Are you sure you want to remove {{ $player->name }} from this tournament?')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </form>
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

                            {{-- Discord Username --}}
                            @if($player->discord_username)
                                <div class="mt-3 w-full">
                                    <div class="inline-flex items-center px-2 py-1 bg-indigo-500/20 text-indigo-400 text-xs font-medium rounded border border-indigo-500/30">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M20.317 4.37a19.791 19.791 0 00-4.885-1.515.074.074 0 00-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 00-5.487 0 12.64 12.64 0 00-.617-1.25.077.077 0 00-.079-.037A19.736 19.736 0 003.677 4.37a.07.07 0 00-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 00.031.057 19.9 19.9 0 005.993 3.03.078.078 0 00.084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 00-.041-.106 13.107 13.107 0 01-1.872-.892.077.077 0 01-.008-.128 10.2 10.2 0 00.372-.292.074.074 0 01.077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 01.078.01c.12.098.246.198.373.292a.077.077 0 01-.006.127 12.299 12.299 0 01-1.873.892.077.077 0 00-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 00.084.028 19.839 19.839 0 006.002-3.03.077.077 0 00.032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 00-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/>
                                        </svg>
                                        <span class="truncate">{{ $player->discord_username }}</span>
                                    </div>
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
