@php
    $isDashboard = $isDashboard ?? request()->routeIs('dashboard.*');
    $canEdit = auth()->check() && auth()->user()->can('update', $tournament);
    $matches = $matches ?? collect();
    $routePrefix = $isDashboard ? 'dashboard.tournaments.' : 'tournaments.';
@endphp

<div class="space-y-6">
    @if($canEdit)
        <div class="flex justify-end">
            <button type="button" class="px-6 py-2.5 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors">
                Create Match
            </button>
        </div>
    @endif

    @if($matches->isEmpty())
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
            </svg>
            <h3 class="text-xl font-bold text-slate-400 mb-2">No Matches Scheduled</h3>
            <p class="text-slate-500">No matches have been scheduled for this tournament yet.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-4">
            @foreach($matches as $match)
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 hover:border-slate-700 transition-colors">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="px-3 py-1 bg-slate-800 rounded-lg">
                                <span class="text-sm font-medium text-slate-400">{{ $match->round ?? 'TBD' }}</span>
                            </div>
                            @if($match->scheduled_at)
                                <div class="flex items-center space-x-2 text-sm text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>{{ $match->scheduled_at->format('M j, Y g:i A') }}</span>
                                </div>
                            @endif
                        </div>
                        @if($canEdit)
                            <div class="flex items-center space-x-2">
                                <button type="button" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium rounded-lg transition-colors">
                                    Edit
                                </button>
                                <button type="button" class="text-slate-400 hover:text-red-400 transition-colors" title="Delete match">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-7 gap-4 items-center">
                        <div class="md:col-span-3">
                            @if($match->teams && $match->teams->count() > 0)
                                <div class="flex items-center space-x-3 p-4 bg-slate-800/50 rounded-lg">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-pink-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                                        <span class="text-white text-sm font-bold">{{ substr($match->teams->first()->name, 0, 2) }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-white font-medium truncate">{{ $match->teams->first()->name }}</p>
                                    </div>
                                    <div class="text-2xl font-bold text-white">
                                        {{ $match->team1_score ?? '0' }}
                                    </div>
                                </div>
                            @else
                                <div class="p-4 bg-slate-800/50 rounded-lg text-center">
                                    <span class="text-slate-500">TBD</span>
                                </div>
                            @endif
                        </div>

                        <div class="md:col-span-1 flex justify-center">
                            <span class="text-slate-500 font-bold text-lg">VS</span>
                        </div>

                        <div class="md:col-span-3">
                            @if($match->teams && $match->teams->count() > 1)
                                <div class="flex items-center space-x-3 p-4 bg-slate-800/50 rounded-lg">
                                    <div class="text-2xl font-bold text-white">
                                        {{ $match->team2_score ?? '0' }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-white font-medium truncate text-right">{{ $match->teams->last()->name }}</p>
                                    </div>
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center flex-shrink-0">
                                        <span class="text-white text-sm font-bold">{{ substr($match->teams->last()->name, 0, 2) }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="p-4 bg-slate-800/50 rounded-lg text-center">
                                    <span class="text-slate-500">TBD</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($match->referee)
                        <div class="border-t border-slate-800 pt-4 mt-4">
                            <div class="flex items-center space-x-2 text-sm text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>Referee: {{ $match->referee->name }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
