<header class="sticky top-0 z-50 bg-slate-900/95 backdrop-blur-sm border-b border-slate-800">
    <nav class="max-w-6xl mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-8">
                <a href="{{ route('homepage') }}" class="text-xl font-bold bg-gradient-to-r from-pink-500 to-fuchsia-500 bg-clip-text text-transparent">
                    BracketSync
                </a>
                <div class="hidden md:flex items-center space-x-6">
                    <a href="/tournaments" class="text-slate-300 hover:text-pink-500 transition-colors">Tournaments</a>
                    <a href="/matches" class="text-slate-300 hover:text-pink-500 transition-colors">Matches</a>
                    <a href="{{ route('dashboard.index') }}" class="text-slate-300 hover:text-pink-500 transition-colors">Dashboard</a>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                {{-- Language Switcher (Always visible) --}}
                <div class="relative" x-data="{ open: false }">
                    <button type="button"
                            @click="open = !open"
                            @click.away="open = false"
                            class="px-3 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors border border-slate-700 rounded-lg"
                            title="Language">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                        </svg>
                    </button>
                    <div x-show="open"
                         x-cloak
                         class="absolute right-0 mt-2 w-40 bg-slate-900 border border-slate-800 rounded-lg shadow-xl z-50">
                        @foreach(config('app.available_locales', ['en']) as $locale)
                            <a href="{{ request()->fullUrlWithQuery(['lang' => $locale]) }}"
                               class="block px-4 py-2 text-sm hover:bg-slate-800 transition-colors {{ app()->getLocale() === $locale ? 'text-pink-400 font-medium' : 'text-slate-300' }} first:rounded-t-lg last:rounded-b-lg">
                                {{ config('app.locale_names')[$locale] ?? strtoupper($locale) }}
                                @if(app()->getLocale() === $locale)
                                    <svg class="w-4 h-4 inline ml-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>

                @auth
                    <div class="hidden md:flex items-center space-x-3">
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false" class="flex items-center space-x-3 px-4 py-2 bg-slate-800 hover:bg-slate-700 rounded-lg border border-slate-700 transition-colors cursor-pointer">
                                @if(auth()->user()->avatar_url)
                                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full">
                                @endif
                                <span class="text-white font-medium">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-56 bg-slate-900 border border-slate-800 rounded-lg shadow-xl z-50"
                                 style="display: none;">
                                <div class="py-2">
                                    <a href="{{ route('settings.index') }}" class="flex items-center space-x-2 px-4 py-2.5 text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span>Settings</span>
                                    </a>
                                    <div class="border-t border-slate-800 my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center space-x-2 px-4 py-2.5 text-red-400 hover:bg-slate-800 hover:text-red-300 transition-colors w-full text-left">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            <span>Logout</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
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
