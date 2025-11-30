<!DOCTYPE html>
<html lang="en" class="scroll-smooth" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'BracketSync Dashboard - Manage your osu! tournaments')">
    <title>@yield('title', 'Dashboard - BracketSync')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('head')
</head>
<body class="bg-slate-950 text-slate-100 antialiased min-h-screen">
    <div class="flex min-h-screen">
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen"
             x-cloak
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
        </div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed lg:sticky top-0 left-0 z-50 w-64 h-screen bg-slate-900 border-r border-slate-800 flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0">

            <!-- Logo -->
            <div class="flex items-center justify-between px-6 py-6 border-b border-slate-800">
                <a href="{{ route('homepage') }}" class="flex items-center space-x-2 group">
                    <div class="w-8 h-8 bg-gradient-to-br from-pink-500 to-purple-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-white">BracketSync</span>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-8 overflow-y-auto">
                <!-- Tournament Management Section -->
                <div>
                    <h3 class="px-3 mb-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tournament Management</h3>
                    <div class="space-y-1">
                        <a href="{{ route('dashboard.index') }}"
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('dashboard.index') ? 'bg-pink-500/10 text-pink-400 border border-pink-500/20' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <span class="font-medium">Dashboard</span>
                        </a>

                        <a href="{{ route('dashboard.tournaments.index') }}"
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('dashboard.tournaments.*') && !request()->routeIs('dashboard.tournaments.create') ? 'bg-pink-500/10 text-pink-400 border border-pink-500/20' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <span class="font-medium">My Tournaments</span>
                        </a>

                        <a href="{{ route('dashboard.tournaments.create') }}"
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('dashboard.tournaments.create') ? 'bg-pink-500/10 text-pink-400 border border-pink-500/20' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span class="font-medium">Create Tournament</span>
                        </a>

                        <a href="{{ route('dashboard.matches.index') }}"
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('dashboard.matches.*') ? 'bg-pink-500/10 text-pink-400 border border-pink-500/20' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                            <span class="font-medium">Matches</span>
                        </a>

                        <a href="{{ route('dashboard.teams.index') }}"
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('dashboard.teams.*') ? 'bg-pink-500/10 text-pink-400 border border-pink-500/20' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="font-medium">Teams</span>
                        </a>
                    </div>
                </div>

                <!-- Support Section -->
                <div>
                    <div class="border-t border-slate-800 mb-4"></div>
                    <h3 class="px-3 mb-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Support</h3>
                    <div class="space-y-1">
                        <a href="{{ route('dashboard.tickets.index') }}"
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('dashboard.tickets.*') ? 'bg-pink-500/10 text-pink-400 border border-pink-500/20' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span class="font-medium">Support Tickets</span>
                        </a>
                    </div>
                </div>

                <!-- Admin Section (Only for admins) -->
                @if(auth()->user()?->isAdmin())
                <div>
                    <div class="border-t border-slate-800 mb-4"></div>
                    <h3 class="px-3 mb-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Admin</h3>
                    <div class="space-y-1">
                        <a href="{{ route('dashboard.admin.users.index') }}"
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('dashboard.admin.users.*') ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span class="font-medium">Users & Roles</span>
                        </a>

                        <a href="{{ route('dashboard.admin.tournaments.index') }}"
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('dashboard.admin.tournaments.*') ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            <span class="font-medium">All Tournaments</span>
                        </a>

                        <a href="{{ route('dashboard.admin.queue.index') }}"
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('dashboard.admin.queue.*') ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"></path>
                            </svg>
                            <span class="font-medium">Queue Management</span>
                        </a>
                    </div>
                </div>
                @endif
            </nav>

            <!-- User Profile Section -->
            <div class="px-4 py-4 border-t border-slate-800">
                <div class="flex items-center justify-between px-3 py-2.5 bg-slate-800/50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        @if(auth()->user()->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full border-2 border-slate-700">
                        @else
                            <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center">
                                <span class="text-slate-300 text-sm font-medium">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                            @if(auth()->user()->siteRole)
                                <p class="text-xs text-slate-400 truncate">{{ ucfirst(auth()->user()->siteRole->name) }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        {{-- Language Switcher --}}
                        <div class="relative" x-data="{ open: false }">
                            <button type="button"
                                    @click="open = !open"
                                    @click.away="open = false"
                                    class="text-slate-400 hover:text-white transition-colors"
                                    title="Language">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                                </svg>
                            </button>
                            <div x-show="open"
                                 x-cloak
                                 class="absolute right-0 bottom-full mb-2 w-40 bg-slate-900 border border-slate-800 rounded-lg shadow-xl z-50">
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

                        @php
                            $pendingTeamInvites = \App\Models\TeamInvitation::where('user_id', auth()->id())
                                ->where('status', 'pending')
                                ->count();
                            $pendingStaffInvites = \App\Models\StaffInvitation::where('user_id', auth()->id())
                                ->where('status', 'pending')
                                ->count();
                            $pendingTournamentInvites = \App\Models\TournamentInvitation::where('user_id', auth()->id())
                                ->where('status', 'pending')
                                ->count();
                            $pendingInvites = $pendingTeamInvites + $pendingStaffInvites + $pendingTournamentInvites;
                        @endphp
                        <div class="relative">
                            <button type="button"
                                    onclick="toggleNotifications()"
                                    class="text-slate-400 hover:text-white transition-colors relative"
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
                            <button type="submit" class="text-slate-400 hover:text-red-400 transition-colors" title="Logout">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Notifications Dropdown -->
        <div id="notificationsDropdown" class="hidden fixed right-4 top-16 w-96 bg-slate-900 border border-slate-800 rounded-xl shadow-2xl z-50 max-h-[calc(100vh-5rem)] overflow-hidden flex flex-col">
            <div class="p-4 border-b border-slate-800 flex items-center justify-between">
                <h3 class="text-lg font-bold text-white">Invitations</h3>
                <button onclick="toggleNotifications()" class="text-slate-400 hover:text-white">
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

                    $tournamentInvitations = \App\Models\TournamentInvitation::where('user_id', auth()->id())
                        ->where('status', 'pending')
                        ->with(['tournament', 'inviter'])
                        ->latest()
                        ->get();
                @endphp

                {{-- Tournament Invitations --}}
                @foreach($tournamentInvitations as $invitation)
                    <div class="bg-pink-500/10 border border-pink-500/30 rounded-lg p-4 space-y-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-4 h-4 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"></path>
                                </svg>
                                <span class="text-xs font-semibold text-pink-400 uppercase">Tournament Invitation</span>
                            </div>
                            <p class="text-white font-medium">{{ $invitation->tournament->name }}</p>
                            @if($invitation->tournament->edition)
                                <p class="text-sm text-pink-300">{{ $invitation->tournament->edition }}</p>
                            @endif
                            <p class="text-xs text-slate-400 mt-1">
                                Invited by {{ $invitation->inviter->name }} • {{ $invitation->created_at->diffForHumans() }}
                            </p>
                        </div>

                        <div class="flex space-x-2">
                            <form action="{{ route('tournaments.invitations.accept', [$invitation->tournament, $invitation]) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                    Accept
                                </button>
                            </form>
                            <form action="{{ route('tournaments.invitations.decline', [$invitation->tournament, $invitation]) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                                    Decline
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach

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

                @if($teamInvitations->isEmpty() && $staffInvitations->isEmpty() && $tournamentInvitations->isEmpty())
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
            function toggleNotifications() {
                const dropdown = document.getElementById('notificationsDropdown');
                dropdown.classList.toggle('hidden');
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                const dropdown = document.getElementById('notificationsDropdown');
                const notifButton = event.target.closest('button[onclick="toggleNotifications()"]');

                if (!dropdown.contains(event.target) && !notifButton && !dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                }
            });
        </script>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-screen">
            <!-- Mobile Header -->
            <header class="lg:hidden sticky top-0 z-30 bg-slate-900 border-b border-slate-800 px-4 py-4">
                <div class="flex items-center justify-between">
                    <button @click="sidebarOpen = true" class="text-slate-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-pink-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-white">BracketSync</span>
                    </div>
                    <div class="w-6"></div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 p-6 lg:p-8">
                <div id="toast-container" class="fixed top-20 right-4 z-50 space-y-3 pointer-events-none"></div>

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
    <style>
        [x-cloak] { display: none !important; }
    </style>
</body>
</html>
