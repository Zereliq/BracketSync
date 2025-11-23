<header class="sticky top-0 z-50 bg-slate-900/95 backdrop-blur-sm border-b border-slate-800">
    <nav class="max-w-6xl mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-8">
                <a href="{{ route('homepage') }}" class="text-xl font-bold bg-gradient-to-r from-pink-500 to-fuchsia-500 bg-clip-text text-transparent">
                    BracketSync Tournaments
                </a>
                <div class="hidden md:flex items-center space-x-6">
                    <a href="#tournaments" class="text-slate-300 hover:text-pink-500 transition-colors">Tournaments</a>
                    <a href="#matches" class="text-slate-300 hover:text-pink-500 transition-colors">Matches</a>
                    <a href="{{ route('dashboard.index') }}" class="text-slate-300 hover:text-pink-500 transition-colors">Dashboard</a>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                @auth
                    <div class="hidden md:flex items-center space-x-3">
                        <div class="flex items-center space-x-3 px-4 py-2 bg-slate-800 rounded-lg border border-slate-700">
                            @if(auth()->user()->avatar_url)
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full">
                            @endif
                            <span class="text-white font-medium">{{ auth()->user()->name }}</span>
                        </div>
                        @php
                            $pendingTeamInvites = \App\Models\TeamInvitation::where('user_id', auth()->id())
                                ->where('status', 'pending')
                                ->count();
                            $pendingStaffInvites = \App\Models\StaffInvitation::where('user_id', auth()->id())
                                ->where('status', 'pending')
                                ->count();
                            $pendingInvites = $pendingTeamInvites + $pendingStaffInvites;
                        @endphp
                        <div class="relative">
                            <button type="button"
                                    onclick="toggleMainNavNotifications()"
                                    class="px-3 py-2.5 bg-slate-800 hover:bg-slate-700 text-white transition-colors border border-slate-700 rounded-lg relative"
                                    title="Notifications">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                @if($pendingInvites > 0)
                                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-pink-500 text-white text-xs flex items-center justify-center rounded-full">
                                        {{ $pendingInvites }}
                                    </span>
                                @endif
                            </button>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors border border-slate-700">
                                Logout
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('auth.osu.redirect') }}" class="hidden md:inline-flex items-center px-5 py-2.5 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors shadow-lg shadow-pink-500/30">
                        Sign in with osu!
                    </a>
                @endauth
                <button id="mobile-menu-btn" class="md:hidden p-2 text-slate-300 hover:text-pink-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div id="mobile-menu" class="hidden md:hidden pt-4 pb-2 space-y-3">
            <a href="#tournaments" class="block text-slate-300 hover:text-pink-500 transition-colors py-2">Tournaments</a>
            <a href="#matches" class="block text-slate-300 hover:text-pink-500 transition-colors py-2">Matches</a>
            <a href="{{ route('dashboard.index') }}" class="block text-slate-300 hover:text-pink-500 transition-colors py-2">Dashboard</a>
            @auth
                <div class="flex items-center space-x-3 px-4 py-2 bg-slate-800 rounded-lg border border-slate-700">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full">
                    @endif
                    <span class="text-white font-medium">{{ auth()->user()->name }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors border border-slate-700">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('auth.osu.redirect') }}" class="inline-flex items-center px-5 py-2.5 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors shadow-lg shadow-pink-500/30">
                    Sign in with osu!
                </a>
            @endauth
        </div>
    </nav>

    <!-- Notifications Dropdown -->
    @auth
    <div id="mainNavNotificationsDropdown" class="hidden fixed right-4 top-16 w-96 bg-slate-900 border border-slate-800 rounded-xl shadow-2xl z-50 max-h-[calc(100vh-5rem)] overflow-hidden flex flex-col">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Invitations</h3>
            <button onclick="toggleMainNavNotifications()" class="text-slate-400 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            @php
                $teamInvitations = \App\Models\TeamInvitation::where('user_id', auth()->id())
                    ->where('status', 'pending')
                    ->with(['team.tournament', 'inviter'])
                    ->latest()
                    ->get();

                $staffInvitations = \App\Models\StaffInvitation::where('user_id', auth()->id())
                    ->where('status', 'pending')
                    ->with(['tournament', 'role', 'inviter'])
                    ->latest()
                    ->get();
            @endphp

            {{-- Staff Invitations --}}
            @foreach($staffInvitations as $invitation)
                <div class="bg-purple-500/10 border border-purple-500/30 rounded-lg p-4 space-y-3">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-xs font-semibold text-purple-400 uppercase">Staff Invitation</span>
                        </div>
                        <p class="text-white font-medium">{{ $invitation->tournament->name }}</p>
                        <p class="text-sm text-purple-300">Role: {{ $invitation->role->name }}</p>
                        <p class="text-xs text-slate-400 mt-1">
                            Invited by {{ $invitation->inviter->name }} • {{ $invitation->created_at->diffForHumans() }}
                        </p>
                    </div>

                    <div class="flex space-x-2">
                        <form action="{{ route('dashboard.staff-invitations.accept', $invitation) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                Accept
                            </button>
                        </form>
                        <form action="{{ route('dashboard.staff-invitations.decline', $invitation) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                                Decline
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach

            {{-- Team Invitations --}}
            @foreach($teamInvitations as $invitation)
                <div class="bg-slate-800/50 border border-slate-700 rounded-lg p-4 space-y-3">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="text-xs font-semibold text-blue-400 uppercase">Team Invitation</span>
                        </div>
                        <p class="text-white font-medium">{{ $invitation->team->name }}</p>
                        <p class="text-sm text-slate-400">{{ $invitation->team->tournament->name }}</p>
                        <p class="text-xs text-slate-500 mt-1">
                            Invited by {{ $invitation->inviter->name }} • {{ $invitation->created_at->diffForHumans() }}
                        </p>
                    </div>

                    <div class="flex space-x-2">
                        <form action="{{ route('dashboard.invitations.accept', $invitation) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                Accept
                            </button>
                        </form>
                        <form action="{{ route('dashboard.invitations.decline', $invitation) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                                Decline
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach

            @if($teamInvitations->isEmpty() && $staffInvitations->isEmpty())
                <div class="text-center py-8">
                    <svg class="w-16 h-16 mx-auto text-slate-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-slate-400">No pending invitations</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function toggleMainNavNotifications() {
            const dropdown = document.getElementById('mainNavNotificationsDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('mainNavNotificationsDropdown');
            const notifButton = event.target.closest('button[onclick="toggleMainNavNotifications()"]');

            if (!dropdown.contains(event.target) && !notifButton && !dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
    @endauth
</header>
