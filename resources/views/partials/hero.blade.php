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
                    <a href="{{ route('dashboard.tournaments.create') }}" class="inline-flex items-center justify-center px-6 py-3 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors border border-slate-700">
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
                        <p class="text-xs text-slate-500 mb-3">Tournament Progress</p>
                        @php
                            // Define stages to display based on tournament configuration
                            $displayStages = ['announced', 'registration', 'screening', 'bracket', 'finished'];

                            // Replace screening with qualifiers if tournament has qualifiers
                            if ($featuredTournament->has_qualifiers) {
                                $displayStages = array_map(function($s) {
                                    return $s === 'screening' ? 'qualifiers' : $s;
                                }, $displayStages);
                            }

                            $currentStage = $featuredTournament->getCurrentStage();
                            $currentStageIndex = array_search($currentStage, $displayStages);

                            // If current stage is not in display stages, find closest match
                            if ($currentStageIndex === false) {
                                if ($currentStage === 'draft') {
                                    $currentStageIndex = -1;
                                } elseif ($currentStage === 'archived') {
                                    $currentStageIndex = count($displayStages);
                                } else {
                                    $currentStageIndex = 0;
                                }
                            }
                        @endphp
                        <div class="flex items-center justify-between gap-2">
                            @foreach($displayStages as $index => $stageName)
                                @php
                                    $isActive = $stageName === $currentStage;
                                    $isPast = $currentStageIndex !== false && $index < $currentStageIndex;
                                    $isFuture = $currentStageIndex !== false && $index > $currentStageIndex;

                                    $stageColor = $statusColors[$stageName] ?? ['bg' => 'bg-slate-500/20', 'text' => 'text-slate-400', 'border' => 'border-slate-500/30'];
                                @endphp
                                <div class="flex-1">
                                    <div class="relative">
                                        @if($isActive)
                                            <div class="absolute -inset-1 bg-gradient-to-r from-pink-500/20 to-fuchsia-500/20 rounded-lg blur"></div>
                                        @endif
                                        <div class="relative bg-slate-800 rounded-lg border px-3 py-2 transition-all
                                            @if($isActive)
                                                {{ $stageColor['border'] }} shadow-lg
                                            @elseif($isPast)
                                                border-slate-600/50
                                            @else
                                                border-slate-700/30
                                            @endif">
                                            <div class="text-center">
                                                <p class="text-xs font-semibold truncate
                                                    @if($isActive)
                                                        {{ $stageColor['text'] }}
                                                    @elseif($isPast)
                                                        text-slate-400
                                                    @else
                                                        text-slate-600
                                                    @endif">
                                                    {{ $stageLabels[$stageName] ?? ucfirst($stageName) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if(!$loop->last)
                                    <div class="flex items-center">
                                        <svg class="w-3 h-3 @if($isPast) text-slate-600 @else text-slate-700 @endif" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @endif
                            @endforeach
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
