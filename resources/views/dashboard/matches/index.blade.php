@extends('layouts.dashboard')

@section('title', 'Matches - Dashboard')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-white mb-2">Matches</h1>
        <p class="text-slate-400">View and manage tournament matches</p>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        @if($matches->count() > 0)
            <div class="space-y-4">
                @foreach($matches as $match)
                    <div class="p-4 bg-slate-800/30 rounded-lg border border-slate-700/50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-white font-semibold mb-1">
                                    {{ $match->tournament->name ?? 'Tournament' }}
                                </h3>
                                <p class="text-sm text-slate-400">
                                    Match ID: {{ $match->id }}
                                    @if($match->referee)
                                        • Referee: {{ $match->referee->name }}
                                    @endif
                                </p>
                            </div>
                            <a href="{{ route('dashboard.matches.show', $match) }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white text-sm font-medium rounded-lg transition-colors">
                                View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $matches->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">No matches found</h3>
                <p class="text-slate-400">Matches will appear here once tournaments start</p>
            </div>
        @endif
    </div>
</div>
@endsection
