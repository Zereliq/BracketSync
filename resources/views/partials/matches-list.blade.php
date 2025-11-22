<section id="matches" class="py-16">
    <div class="max-w-6xl mx-auto px-4">
        <div class="mb-10">
            <h2 class="text-3xl font-bold text-white">Upcoming Matches</h2>
            <p class="text-slate-400 mt-2">Don't miss the action - watch live or catch the VODs</p>
        </div>
        <div class="space-y-4">
            @forelse($upcomingMatches as $match)
                <div class="bg-slate-900 rounded-2xl shadow-lg border border-slate-800 p-6 hover:border-pink-500/50 transition-all">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex-1">
                            <p class="text-xs text-slate-500 mb-2">{{ $match->tournament->name }} - {{ $match->stage ?? 'Match' }}</p>
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 bg-pink-500 rounded-full"></div>
                                    <span class="text-white font-medium">{{ $match->team1?->name ?? 'TBD' }}</span>
                                </div>
                                <span class="text-slate-500 font-bold">vs</span>
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 bg-fuchsia-500 rounded-full"></div>
                                    <span class="text-white font-medium">{{ $match->team2?->name ?? 'TBD' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <p class="text-xs text-slate-500">Scheduled</p>
                                <p class="text-white font-medium">{{ $match->scheduled_at->format('M d, Y H:i') }}</p>
                            </div>
                            <a href="{{ route('tournaments.show', $match->tournament) }}" class="px-4 py-2 bg-pink-500 hover:bg-pink-600 text-white text-sm font-medium rounded-lg transition-colors">
                                View match
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-slate-900 rounded-2xl shadow-lg border border-slate-800">
                    <p class="text-slate-400">No upcoming matches scheduled</p>
                    <p class="text-sm text-slate-500 mt-2">Check back later for match schedules!</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
