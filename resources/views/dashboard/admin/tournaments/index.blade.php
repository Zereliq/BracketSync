@extends('layouts.dashboard')

@section('title', 'All Tournaments - Admin')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-white mb-2">All Tournaments</h1>
        <p class="text-slate-400">Admin view of all tournaments in the system</p>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        @if($tournaments->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-800/50 border-b border-slate-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">Tournament</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">Creator</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">Mode</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">Created</th>
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
                                    <div class="flex items-center space-x-3">
                                        @if($tournament->creator->avatar_url)
                                            <img src="{{ $tournament->creator->avatar_url }}" alt="{{ $tournament->creator->name }}" class="w-8 h-8 rounded-full border-2 border-slate-700">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center">
                                                <span class="text-slate-300 text-xs font-medium">{{ substr($tournament->creator->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <span class="text-slate-300 text-sm">{{ $tournament->creator->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-slate-300 text-sm">{{ ucfirst($tournament->mode) }}</span>
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
                                    <span class="text-slate-400 text-sm">{{ $tournament->created_at->format('M d, Y') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('dashboard.tournaments.show', $tournament) }}" class="text-pink-400 hover:text-pink-300 text-sm font-medium">
                                        View
                                    </a>
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
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">No tournaments found</h3>
                <p class="text-slate-400">The system doesn't have any tournaments yet</p>
            </div>
        @endif
    </div>
</div>
@endsection
