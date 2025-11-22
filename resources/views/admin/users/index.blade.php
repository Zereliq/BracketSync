@extends('layouts.dashboard')

@section('title', 'Manage User Roles - Admin')

@section('content')
<section>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Users & Roles</h1>
                <p class="text-slate-400">Update site roles for users</p>
            </div>
            <form method="GET" action="{{ route('dashboard.admin.users.index') }}" class="flex items-center space-x-2">
                <div class="relative">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Search username..."
                        class="bg-slate-900 border border-slate-700 text-white rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent w-64"
                    >
                    @if(request('q'))
                        <a href="{{ route('dashboard.admin.users.index') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </a>
                    @endif
                </div>
                <button type="submit" class="px-4 py-2 bg-pink-500 hover:bg-pink-600 text-white text-sm font-medium rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-500/20 border border-green-500/30 text-green-400 px-6 py-4 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-500/20 border border-red-500/30 text-red-400 px-6 py-4 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        @if(request('q'))
            <div class="mb-6 flex items-center space-x-2 text-sm text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <span>Showing results for "<span class="text-white">{{ request('q') }}</span>"</span>
                <a href="{{ route('dashboard.admin.users.index') }}" class="text-pink-400 hover:text-pink-300 ml-2">Clear search</a>
            </div>
        @endif

        <div class="bg-slate-900 rounded-2xl shadow-xl border border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-800/50 border-b border-slate-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">User</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">osu! ID</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">Current Role</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">Change Role</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        @if($user->avatar_url)
                                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full border-2 border-slate-700">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center">
                                                <span class="text-slate-300 font-medium">{{ substr($user->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-white font-medium">{{ $user->name }}</div>
                                            <div class="text-slate-400 text-sm">{{ $user->email ?? 'No email' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-slate-300 font-mono text-sm">{{ $user->osu_id }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->siteRole)
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            @if($user->siteRole->name === 'admin') bg-pink-500/20 text-pink-400 border border-pink-500/30
                                            @elseif($user->siteRole->name === 'mod') bg-purple-500/20 text-purple-400 border border-purple-500/30
                                            @else bg-slate-500/20 text-slate-400 border border-slate-500/30
                                            @endif">
                                            {{ ucfirst($user->siteRole->name) }}
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-slate-500/20 text-slate-400 rounded-full text-xs font-medium border border-slate-500/30">No role</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <form method="POST" action="{{ route('dashboard.admin.users.role.update', $user) }}" class="flex items-center space-x-3">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" class="bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                            <option value="player" {{ $user->siteRole?->name === 'player' ? 'selected' : '' }}>Player</option>
                                            <option value="mod" {{ $user->siteRole?->name === 'mod' ? 'selected' : '' }}>Mod</option>
                                            <option value="admin" {{ $user->siteRole?->name === 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                        <button type="submit" class="px-4 py-2 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors">
                                            Update
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('dashboard.tournaments.index') }}?user={{ $user->id }}" class="text-pink-400 hover:text-pink-300 text-sm font-medium">
                                        View Tournaments
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    @if(request('q'))
                                        No users found matching "{{ request('q') }}".
                                    @else
                                        No users found in the system.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-slate-800">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        <div class="mt-8 bg-slate-900/50 rounded-lg border border-slate-800 p-6">
            <h3 class="text-lg font-semibold text-white mb-3">Role Descriptions</h3>
            <div class="space-y-3 text-sm">
                <div class="flex items-start space-x-3">
                    <span class="px-3 py-1 bg-pink-500/20 text-pink-400 rounded-full text-xs font-medium border border-pink-500/30">Admin</span>
                    <p class="text-slate-400 flex-1">Full access to all features including user management, can edit any tournament, bypass all restrictions.</p>
                </div>
                <div class="flex items-start space-x-3">
                    <span class="px-3 py-1 bg-purple-500/20 text-purple-400 rounded-full text-xs font-medium border border-purple-500/30">Mod</span>
                    <p class="text-slate-400 flex-1">Moderator permissions for overseeing tournaments and resolving disputes (to be implemented).</p>
                </div>
                <div class="flex items-start space-x-3">
                    <span class="px-3 py-1 bg-slate-500/20 text-slate-400 rounded-full text-xs font-medium border border-slate-500/30">Player</span>
                    <p class="text-slate-400 flex-1">Standard user - can create up to 5 active tournaments, participate in tournaments, manage own tournaments.</p>
                </div>
            </div>
        </div>
</section>
@endsection
