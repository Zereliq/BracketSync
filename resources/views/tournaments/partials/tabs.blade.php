@php
    $isDashboard = request()->routeIs('dashboard.*');
    $routePrefix = $isDashboard ? 'dashboard.tournaments.' : 'tournaments.';
    $currentTab = $currentTab ?? 'tournament';

    $tabs = [
        [
            'key' => 'tournament',
            'label' => 'Tournament',
            'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
            'route' => 'show',
        ],
        [
            'key' => 'staff',
            'label' => 'Staff',
            'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
            'route' => 'staff',
        ],
        [
            'key' => 'players',
            'label' => 'Players',
            'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
            'route' => 'players',
        ],
    ];

    // Only show Teams tab for team tournaments (format > 1)
    if ($tournament->isTeamTournament()) {
        $tabs[] = [
            'key' => 'teams',
            'label' => 'Teams',
            'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
            'route' => 'teams',
        ];
    }

    // Add remaining tabs
    $tabs = array_merge($tabs, [
        [
            'key' => 'qualifiers',
            'label' => 'Qualifiers',
            'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
            'route' => 'qualifiers',
        ],
        [
            'key' => 'matches',
            'label' => 'Matches',
            'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
            'route' => 'matches',
        ],
        [
            'key' => 'bracket',
            'label' => 'Bracket',
            'icon' => 'M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 17a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1v-2zM14 17a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1v-2z',
            'route' => 'bracket',
            'dashboardOnly' => true,
        ],
        [
            'key' => 'mappools',
            'label' => 'Mappools',
            'icon' => 'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3',
            'route' => 'mappools',
        ],
    ]);

    // Filter out dashboard-only tabs when not in dashboard
    if (!$isDashboard) {
        $tabs = array_filter($tabs, function($tab) {
            return !isset($tab['dashboardOnly']) || !$tab['dashboardOnly'];
        });
    }
@endphp

<div class="mb-6">
    <div class="border-b border-slate-800">
        <div class="overflow-x-auto -mx-4 px-4 sm:mx-0 sm:px-0">
            <nav class="flex space-x-2 min-w-max" aria-label="Tournament tabs">
                @foreach($tabs as $tab)
                    @php
                        $isActive = $currentTab === $tab['key'];
                        $routeName = $routePrefix . $tab['route'];
                    @endphp
                    <a href="{{ route($routeName, $tournament) }}"
                       class="flex items-center space-x-2 px-5 py-3.5 font-medium text-sm rounded-t-lg transition-all border-b-2 whitespace-nowrap {{ $isActive ? 'bg-fuchsia-500/10 text-fuchsia-400 border-fuchsia-500' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 border-transparent' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"></path>
                        </svg>
                        <span>{{ $tab['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </div>
    </div>
</div>
