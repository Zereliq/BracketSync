@extends('layouts.dashboard')

@section('title', 'Teams - Dashboard')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-white mb-2">Teams</h1>
        <p class="text-slate-400">View and manage tournament teams</p>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        @if($teams->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($teams as $team)
                    <div class="p-4 bg-slate-800/30 rounded-lg border border-slate-700/50 hover:border-slate-600 transition-colors">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h3 class="text-white font-semibold mb-1">{{ $team->name }}</h3>
                                <p class="text-sm text-slate-400">
                                    {{ $team->tournament->name ?? 'Tournament' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-3 border-t border-slate-700">
                            <span class="text-xs text-slate-500">
                                {{ $team->members->count() ?? 0 }} members
                            </span>
                            <a href="{{ route('dashboard.teams.show', $team) }}" class="text-sm text-pink-400 hover:text-pink-300 font-medium">
                                View →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $teams->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">No teams found</h3>
                <p class="text-slate-400">Teams will appear here once tournaments have registered participants</p>
            </div>
        @endif
    </div>
</div>
@endsection
