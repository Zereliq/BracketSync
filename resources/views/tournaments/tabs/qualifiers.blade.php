@php
    $isDashboard = request()->routeIs('dashboard.*');
    $canEdit = $isDashboard && auth()->check() && auth()->user()->can('update', $tournament);
@endphp

<div class="space-y-6">
    @if($tournament->has_qualifiers)
        {{-- Qualifiers Content --}}
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-white">Qualifiers</h2>
                @if($canEdit)
                    <button type="button" class="px-6 py-2.5 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors">
                        Manage Qualifiers
                    </button>
                @endif
            </div>

            {{-- Placeholder for qualifier content --}}
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                <h3 class="text-xl font-bold text-slate-400 mb-2">Qualifiers Not Started</h3>
                <p class="text-slate-500">Qualifier information will be displayed here once available.</p>
            </div>
        </div>

        @if($tournament->qualifier_results_public)
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <h2 class="text-xl font-bold text-white mb-4">Qualifier Results</h2>

                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="text-xl font-bold text-slate-400 mb-2">No Results Yet</h3>
                    <p class="text-slate-500">Qualifier results will be published here once available.</p>
                </div>
            </div>
        @endif
    @else
        {{-- No Qualifiers Message --}}
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl font-bold text-slate-400 mb-2">No Qualifiers</h3>
                <p class="text-slate-500">This tournament does not have a qualifier stage.</p>
            </div>
        </div>
    @endif
</div>
