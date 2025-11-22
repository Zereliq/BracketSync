@extends('layouts.dashboard')

@section('title', 'My Tournaments - Dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">My Tournaments</h1>
            <p class="text-slate-400">Manage your tournament creations</p>
        </div>
        <a href="{{ route('dashboard.tournaments.create') }}" class="px-6 py-3 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Create Tournament</span>
        </a>
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

    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        @if($tournaments->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-800/50 border-b border-slate-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">Tournament</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">Format</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">Signup Window</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($tournaments as $tournament)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-white font-medium">{{ $tournament->name }}</div>
                                        @if($tournament->edition)
                                            <div class="text-slate-400 text-sm">{{ $tournament->edition }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col space-y-1">
                                        <span class="text-slate-300 text-sm">{{ ucfirst($tournament->mode) }}</span>
                                        <span class="text-slate-400 text-xs">{{ $tournament->team_size }}v{{ $tournament->team_size }} • {{ $tournament->bracket_size }} teams</span>
                                        <span class="text-slate-400 text-xs">{{ ucfirst($tournament->elim_type) }} Elim</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        @if($tournament->status === 'draft') bg-slate-500/20 text-slate-400 border border-slate-500/30
                                        @elseif($tournament->status === 'published') bg-blue-500/20 text-blue-400 border border-blue-500/30
                                        @elseif($tournament->status === 'ongoing') bg-green-500/20 text-green-400 border border-green-500/30
                                        @elseif($tournament->status === 'finished') bg-purple-500/20 text-purple-400 border border-purple-500/30
                                        @else bg-slate-500/20 text-slate-400 border border-slate-500/30
                                        @endif">
                                        {{ ucfirst($tournament->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($tournament->signup_start && $tournament->signup_end)
                                        <div class="text-slate-300 text-sm">
                                            <div>{{ $tournament->signup_start->format('M d, Y') }}</div>
                                            <div class="text-slate-400 text-xs">to {{ $tournament->signup_end->format('M d, Y') }}</div>
                                        </div>
                                    @else
                                        <span class="text-slate-500 text-sm">Not set</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <a href="{{ route('dashboard.tournaments.edit', $tournament) }}" class="text-pink-400 hover:text-pink-300 text-sm font-medium">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('dashboard.tournaments.destroy', $tournament) }}" onsubmit="return confirm('Are you sure you want to delete this tournament?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-300 text-sm font-medium">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($tournaments->hasPages())
                <div class="px-6 py-4 border-t border-slate-800">
                    {{ $tournaments->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">No tournaments yet</h3>
                <p class="text-slate-400 mb-6">Create your first tournament to get started!</p>
                <a href="{{ route('dashboard.tournaments.create') }}" class="inline-flex items-center px-6 py-3 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Your First Tournament
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
