@php
    $stageLabels = [
        'draft' => 'Draft',
        'announced' => 'Announced',
        'registration' => 'SIGNUP',
        'screening' => 'Screening',
        'qualifiers' => 'Qualifiers',
        'bracket' => 'Bracket',
        'finished' => 'Finished',
        'archived' => 'Archived',
    ];

    $statusColors = [
        'draft' => ['bg' => 'bg-slate-500/20', 'text' => 'text-slate-400', 'border' => 'border-slate-500/30'],
        'announced' => ['bg' => 'bg-blue-500/20', 'text' => 'text-blue-400', 'border' => 'border-blue-500/30'],
        'registration' => ['bg' => 'bg-blue-500/20', 'text' => 'text-blue-400', 'border' => 'border-blue-500/30'],
        'screening' => ['bg' => 'bg-yellow-500/20', 'text' => 'text-yellow-400', 'border' => 'border-yellow-500/30'],
        'qualifiers' => ['bg' => 'bg-purple-500/20', 'text' => 'text-purple-400', 'border' => 'border-purple-500/30'],
        'bracket' => ['bg' => 'bg-green-500/20', 'text' => 'text-green-400', 'border' => 'border-green-500/30'],
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
        'single' => 'Single Elimination',
        'double' => 'Double Elimination',
    ];
@endphp

<section class="relative overflow-hidden py-16 md:py-24">
    <div class="absolute inset-0 bg-gradient-to-br from-pink-500/10 via-transparent to-fuchsia-500/10"></div>
    <div class="max-w-6xl mx-auto px-4 relative">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div class="space-y-6">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight">
                    Organize and play
                    <span class="bg-gradient-to-r from-pink-500 to-fuchsia-500 bg-clip-text text-transparent">osu! tournaments</span>
                    with ease
                </h1>
                <p class="text-lg text-slate-400 leading-relaxed">
                    Streamline your competitive experience with automated seeding, bracket management,
                    mappool organization, and live scoring. Everything you need to run professional tournaments.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('tournaments.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors shadow-lg shadow-pink-500/30">
                        View active tournaments
                    </a>
                    <a href="#host" class="inline-flex items-center justify-center px-6 py-3 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors border border-slate-700">
                        Host a tournament
                    </a>
                </div>
            </div>
            @if($featuredTournament)
                @php
                    $stage = $featuredTournament->getCurrentStage();
                    $statusColor = $statusColors[$featuredTournament->status] ?? $statusColors[$stage] ?? ['bg' => 'bg-slate-500/20', 'text' => 'text-slate-400', 'border' => 'border-slate-500/30'];
                @endphp
                <div class="bg-slate-900 rounded-2xl shadow-2xl border border-slate-800 p-6 space-y-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-white">{{ $featuredTournament->name }}</h3>
                            @if($featuredTournament->edition)
                                <p class="text-sm text-slate-400 mt-1">{{ $featuredTournament->edition }}</p>
                            @endif
                        </div>
                        <span class="px-3 py-1 {{ $statusColor['bg'] }} {{ $statusColor['text'] }} text-xs font-medium rounded-full border {{ $statusColor['border'] }}">
                            {{ ucfirst($featuredTournament->status) }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="space-y-1">
                            <p class="text-slate-500 text-xs">Mode</p>
                            <p class="text-slate-200 font-medium">{{ $modeLabels[$featuredTournament->mode] ?? ucfirst($featuredTournament->mode) }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-slate-500 text-xs">Format</p>
                            <p class="text-slate-200 font-medium">{{ $featuredTournament->getFormattedTeamSize() }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-slate-500 text-xs">Bracket</p>
                            <p class="text-slate-200 font-medium">{{ $elimTypeLabels[$featuredTournament->elim_type] ?? ucfirst($featuredTournament->elim_type) }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-slate-500 text-xs">{{ $featuredTournament->isTeamTournament() ? 'Teams' : 'Players' }}</p>
                            <p class="text-slate-200 font-medium">
                                @if($featuredTournament->isTeamTournament())
                                    {{ $featuredTournament->teams_count ?? 0 }} registered
                                @else
                                    {{ $featuredTournament->registered_players_count ?? 0 }} registered
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-slate-800">
                        <p class="text-xs text-slate-500 mb-3">Current Round</p>
                        <div class="flex items-center justify-center">
                            <div class="h-16 bg-slate-800 rounded-lg border border-slate-700 flex items-center justify-center px-8">
                                <div class="text-center">
                                    <p class="text-lg text-white font-bold">{{ $stageLabels[$stage] ?? ucfirst($stage) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-slate-900 rounded-2xl shadow-2xl border border-slate-800 p-6 space-y-4">
                    <div class="text-center py-8">
                        <p class="text-slate-400">No featured tournament available</p>
                        <p class="text-sm text-slate-500 mt-2">Check back soon for upcoming tournaments!</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
