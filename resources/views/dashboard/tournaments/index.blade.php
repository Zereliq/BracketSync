@extends('layouts.dashboard')

@section('title', 'My Tournaments - Dashboard')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-white mb-2">My Tournaments</h1>
        <p class="text-slate-400">Manage all tournaments you're involved with</p>
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

    <!-- My Staff Tournaments -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-800">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-white">My Staff Tournaments</h2>
                <a href="{{ route('dashboard.tournaments.create') }}" class="px-4 py-2 bg-pink-500 hover:bg-pink-600 text-white text-sm font-medium rounded-lg transition-colors">
                    Create Tournament
                </a>
            </div>
        </div>

        <div class="p-6">
            @if($staffTournaments->count() > 0)
                <div class="space-y-4">
                    @foreach($staffTournaments as $tournament)
                        <a href="{{ route('dashboard.tournaments.show', $tournament) }}" class="block group">
                            <div class="p-5 bg-slate-800/30 hover:bg-slate-800/50 rounded-lg transition-all border border-slate-700/50 hover:border-pink-500/50">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-3">
                                            <h3 class="text-lg text-white font-bold group-hover:text-pink-400 transition-colors">
                                                {{ $tournament->name }}
                                            </h3>
                                            @if($tournament->edition)
                                                <span class="px-2 py-0.5 bg-slate-700/50 text-slate-300 text-xs rounded">{{ $tournament->edition }}</span>
                                            @endif
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($tournament->status === 'draft') bg-slate-500/20 text-slate-300 border border-slate-500/30
                                                @elseif($tournament->status === 'published') bg-blue-500/20 text-blue-300 border border-blue-500/30
                                                @elseif($tournament->status === 'ongoing') bg-green-500/20 text-green-300 border border-green-500/30
                                                @elseif($tournament->status === 'finished') bg-purple-500/20 text-purple-300 border border-purple-500/30
                                                @else bg-slate-500/20 text-slate-300 border border-slate-500/30
                                                @endif">
                                                {{ ucfirst($tournament->status) }}
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                            <div class="flex items-center space-x-2">
                                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                                                </svg>
                                                <span class="text-slate-300">
                                                    @if($tournament->mode === 'standard') osu!
                                                    @elseif($tournament->mode === 'piano') mania
                                                    @elseif($tournament->mode === 'fruit') catch
                                                    @elseif($tournament->mode === 'drums') taiko
                                                    @else {{ ucfirst($tournament->mode) }}
                                                    @endif
                                                </span>
                                            </div>

                                            <div class="flex items-center space-x-2">
                                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                                <span class="text-slate-300">{{ $tournament->getFormattedTeamSize() }}</span>
                                            </div>

                                            <div class="flex items-center space-x-2">
                                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                </svg>
                                                <span class="text-slate-300">{{ $tournament->bracket_size }} teams</span>
                                            </div>

                                            <div class="flex items-center space-x-2">
                                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                                </svg>
                                                <span class="text-slate-300">{{ ucfirst($tournament->elim_type) }} elim</span>
                                            </div>
                                        </div>

                                        @php
                                            $currentStage = $tournament->getCurrentStage();
                                            $allStages = [
                                                'draft' => 'Draft',
                                                'announced' => 'Announced',
                                                'registration' => 'Registration',
                                                'screening' => 'Screening',
                                                'qualifiers' => 'Qualifiers',
                                                'bracket' => 'Bracket',
                                                'finished' => 'Finished',
                                                'archived' => 'Archived'
                                            ];

                                            // Remove qualifiers stage if tournament doesn't have them
                                            if (!$tournament->has_qualifiers) {
                                                unset($allStages['qualifiers']);
                                            }
                                        @endphp

                                        <!-- Tournament Stage Progress -->
                                        <div class="mt-4 pt-4 border-t border-slate-700/50">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-xs font-medium text-slate-400">Tournament Progress</span>
                                                <span class="text-xs font-semibold text-pink-400">{{ ucfirst($currentStage) }}</span>
                                            </div>

                                            <div class="flex items-center space-x-1">
                                                @foreach($allStages as $stageKey => $stageLabel)
                                                    @php
                                                        $isPast = array_search($stageKey, array_keys($allStages)) < array_search($currentStage, array_keys($allStages));
                                                        $isCurrent = $stageKey === $currentStage;
                                                    @endphp

                                                    <div class="flex-1 group relative">
                                                        <div class="h-1.5 rounded-full transition-all
                                                            @if($isCurrent) bg-gradient-to-r from-pink-500 to-fuchsia-500 shadow-lg shadow-pink-500/50
                                                            @elseif($isPast) bg-green-500/80
                                                            @else bg-slate-700
                                                            @endif">
                                                        </div>

                                                        <!-- Tooltip on hover -->
                                                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-slate-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-10 border border-slate-700">
                                                            {{ $stageLabel }}
                                                            @if($isCurrent)
                                                                <span class="text-pink-400"> (Current)</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        @if($tournament->signup_start || $tournament->signup_end || $tournament->rank_min || $tournament->rank_max)
                                            <div class="flex flex-wrap items-center gap-3 mt-3 pt-3 border-t border-slate-700/50">
                                                @if($tournament->signup_start && $tournament->signup_end)
                                                    <div class="flex items-center space-x-1.5 text-xs text-slate-400">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        <span>{{ $tournament->signup_start->format('M j') }} - {{ $tournament->signup_end->format('M j, Y') }}</span>
                                                    </div>
                                                @endif
                                                @if($tournament->rank_min || $tournament->rank_max)
                                                    <div class="flex items-center space-x-1.5 text-xs text-slate-400">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                                        </svg>
                                                        <span>{{ $tournament->getRankRangeDisplay() }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <svg class="w-5 h-5 text-slate-500 group-hover:text-pink-400 transition-colors ml-4 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">No staff tournaments</h3>
                    <p class="text-slate-400">You haven't created or been assigned to any tournaments yet.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- My Player Tournaments -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-800">
            <h2 class="text-xl font-bold text-white">My Player Tournaments</h2>
        </div>

        <div class="p-6">
            @if($playerTournaments->count() > 0)
                <div class="space-y-4">
                    @foreach($playerTournaments as $tournament)
                        <a href="{{ route('dashboard.tournaments.show', $tournament) }}" class="block group">
                            <div class="p-5 bg-slate-800/30 hover:bg-slate-800/50 rounded-lg transition-all border border-slate-700/50 hover:border-pink-500/50">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-3">
                                            <h3 class="text-lg text-white font-bold group-hover:text-pink-400 transition-colors">
                                                {{ $tournament->name }}
                                            </h3>
                                            @if($tournament->edition)
                                                <span class="px-2 py-0.5 bg-slate-700/50 text-slate-300 text-xs rounded">{{ $tournament->edition }}</span>
                                            @endif
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($tournament->status === 'draft') bg-slate-500/20 text-slate-300 border border-slate-500/30
                                                @elseif($tournament->status === 'published') bg-blue-500/20 text-blue-300 border border-blue-500/30
                                                @elseif($tournament->status === 'ongoing') bg-green-500/20 text-green-300 border border-green-500/30
                                                @elseif($tournament->status === 'finished') bg-purple-500/20 text-purple-300 border border-purple-500/30
                                                @else bg-slate-500/20 text-slate-300 border border-slate-500/30
                                                @endif">
                                                {{ ucfirst($tournament->status) }}
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                            <div class="flex items-center space-x-2">
                                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                                                </svg>
                                                <span class="text-slate-300">
                                                    @if($tournament->mode === 'standard') osu!
                                                    @elseif($tournament->mode === 'piano') mania
                                                    @elseif($tournament->mode === 'fruit') catch
                                                    @elseif($tournament->mode === 'drums') taiko
                                                    @else {{ ucfirst($tournament->mode) }}
                                                    @endif
                                                </span>
                                            </div>

                                            <div class="flex items-center space-x-2">
                                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                                <span class="text-slate-300">{{ $tournament->getFormattedTeamSize() }}</span>
                                            </div>

                                            <div class="flex items-center space-x-2">
                                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                </svg>
                                                <span class="text-slate-300">{{ $tournament->bracket_size }} teams</span>
                                            </div>

                                            <div class="flex items-center space-x-2">
                                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                                </svg>
                                                <span class="text-slate-300">{{ ucfirst($tournament->elim_type) }} elim</span>
                                            </div>
                                        </div>

                                        @php
                                            $currentStage = $tournament->getCurrentStage();
                                            $allStages = [
                                                'draft' => 'Draft',
                                                'announced' => 'Announced',
                                                'registration' => 'Registration',
                                                'screening' => 'Screening',
                                                'qualifiers' => 'Qualifiers',
                                                'bracket' => 'Bracket',
                                                'finished' => 'Finished',
                                                'archived' => 'Archived'
                                            ];

                                            if (!$tournament->has_qualifiers) {
                                                unset($allStages['qualifiers']);
                                            }
                                        @endphp

                                        <div class="mt-4 pt-4 border-t border-slate-700/50">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-xs font-medium text-slate-400">Tournament Progress</span>
                                                <span class="text-xs font-semibold text-pink-400">{{ ucfirst($currentStage) }}</span>
                                            </div>

                                            <div class="flex items-center space-x-1">
                                                @foreach($allStages as $stageKey => $stageLabel)
                                                    @php
                                                        $isPast = array_search($stageKey, array_keys($allStages)) < array_search($currentStage, array_keys($allStages));
                                                        $isCurrent = $stageKey === $currentStage;
                                                    @endphp

                                                    <div class="flex-1 group relative">
                                                        <div class="h-1.5 rounded-full transition-all
                                                            @if($isCurrent) bg-gradient-to-r from-pink-500 to-fuchsia-500 shadow-lg shadow-pink-500/50
                                                            @elseif($isPast) bg-green-500/80
                                                            @else bg-slate-700
                                                            @endif">
                                                        </div>

                                                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-slate-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-10 border border-slate-700">
                                                            {{ $stageLabel }}
                                                            @if($isCurrent)
                                                                <span class="text-pink-400"> (Current)</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        @if($tournament->signup_start || $tournament->signup_end || $tournament->rank_min || $tournament->rank_max)
                                            <div class="flex flex-wrap items-center gap-3 mt-3 pt-3 border-t border-slate-700/50">
                                                @if($tournament->signup_start && $tournament->signup_end)
                                                    <div class="flex items-center space-x-1.5 text-xs text-slate-400">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        <span>{{ $tournament->signup_start->format('M j') }} - {{ $tournament->signup_end->format('M j, Y') }}</span>
                                                    </div>
                                                @endif
                                                @if($tournament->rank_min || $tournament->rank_max)
                                                    <div class="flex items-center space-x-1.5 text-xs text-slate-400">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                                        </svg>
                                                        <span>{{ $tournament->getRankRangeDisplay() }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <svg class="w-5 h-5 text-slate-500 group-hover:text-pink-400 transition-colors ml-4 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">No player tournaments</h3>
                    <p class="text-slate-400">You haven't registered for any tournaments yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
