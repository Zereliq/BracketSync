@php
    $statusColors = [
        'draft' => ['bg' => 'bg-slate-500/20', 'text' => 'text-slate-400', 'border' => 'border-slate-500/30'],
        'announced' => ['bg' => 'bg-blue-500/20', 'text' => 'text-blue-400', 'border' => 'border-blue-500/30'],
        'ongoing' => ['bg' => 'bg-green-500/20', 'text' => 'text-green-400', 'border' => 'border-green-500/30'],
        'finished' => ['bg' => 'bg-slate-500/20', 'text' => 'text-slate-400', 'border' => 'border-slate-500/30'],
    ];

    $modeLabels = [
        'osu' => 'osu! standard',
        'taiko' => 'osu!taiko',
        'catch' => 'osu!catch',
        'mania' => 'osu!mania',
    ];

    $elimTypeLabels = [
        'single' => 'Single Elim',
        'double' => 'Double Elim',
    ];

    $stageLabels = [
        'draft' => 'In draft',
        'announced' => 'Announced',
        'registration' => 'Registration open',
        'screening' => 'Screening players',
        'qualifiers' => 'Qualifiers in progress',
        'bracket' => 'Bracket stage',
        'finished' => 'Tournament finished',
        'archived' => 'Archived',
    ];
@endphp

<section id="tournaments" class="py-16 bg-slate-900/50">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-3xl font-bold text-white">Active Tournaments</h2>
                <p class="text-slate-400 mt-2">Join the competition or spectate live matches</p>
            </div>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($tournaments as $tournament)
                @php
                    $statusColor = $statusColors[$tournament->status] ?? ['bg' => 'bg-slate-500/20', 'text' => 'text-slate-400', 'border' => 'border-slate-500/30'];
                    $stage = $tournament->getCurrentStage();
                @endphp
                <a href="{{ route('tournaments.show', $tournament) }}" class="bg-slate-900 rounded-2xl shadow-lg border border-slate-800 p-6 hover:border-pink-500/50 transition-all block">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-white">{{ $tournament->name }}</h3>
                            @if($tournament->abbreviation)
                                <p class="text-sm text-slate-400 mt-1">{{ $tournament->abbreviation }}</p>
                            @endif
                        </div>
                        <span class="px-3 py-1 {{ $statusColor['bg'] }} {{ $statusColor['text'] }} text-xs font-medium rounded-full border {{ $statusColor['border'] }}">
                            {{ ucfirst($tournament->status) }}
                        </span>
                    </div>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Mode</span>
                            <span class="text-slate-200 font-medium">{{ $modeLabels[$tournament->mode] ?? ucfirst($tournament->mode) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Format</span>
                            <span class="text-slate-200 font-medium">{{ $tournament->getFormattedTeamSize() }}, {{ $elimTypeLabels[$tournament->elim_type] ?? ucfirst($tournament->elim_type) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">{{ $tournament->isTeamTournament() ? 'Teams' : 'Players' }}</span>
                            <span class="text-slate-200 font-medium">
                                @if($tournament->isTeamTournament())
                                    {{ $tournament->teams_count ?? 0 }}@if($tournament->bracket_size) / {{ $tournament->bracket_size }}@endif
                                @else
                                    {{ $tournament->registered_players_count ?? 0 }}@if($tournament->bracket_size) / {{ $tournament->bracket_size }}@endif
                                @endif
                            </span>
                        </div>
                        <div class="pt-3 border-t border-slate-800">
                            <p class="text-slate-400 text-xs">{{ $stageLabels[$stage] ?? ucfirst($stage) }}</p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-slate-400">No active tournaments at the moment</p>
                    <p class="text-sm text-slate-500 mt-2">Check back soon for new tournaments!</p>
                </div>
            @endforelse
        </div>
        <div class="mt-8 text-center">
            <a href="{{ route('tournaments.index') }}" class="inline-flex items-center px-6 py-3 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors border border-slate-700">
                View all tournaments
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</section>
