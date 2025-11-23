@php
    $isDashboard = $isDashboard ?? request()->routeIs('dashboard.*');
    $canEdit = auth()->check() && auth()->user()->can('update', $tournament);
    $routePrefix = $isDashboard ? 'dashboard.tournaments.' : 'tournaments.';
@endphp

<div class="space-y-6">
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 17a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1v-2zM14 17a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1v-2z"></path>
        </svg>
        <h3 class="text-xl font-bold text-slate-400 mb-2">Bracket Coming Soon</h3>
        <p class="text-slate-500">The tournament bracket will be displayed here once matches are finalized.</p>

        @if($canEdit)
            <div class="mt-6">
                <p class="text-sm text-slate-600">Generate the bracket from the Matches tab or Teams tab.</p>
            </div>
        @endif
    </div>
</div>
