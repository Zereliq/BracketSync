@php
    $isDashboard = $isDashboard ?? request()->routeIs('dashboard.*');
    $canEdit = auth()->check() && auth()->user()->can('editMatches', $tournament);
    $rounds = $rounds ?? collect();
@endphp

<div class="space-y-6">
    {{-- Settings Header --}}
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 border border-slate-700 rounded-xl p-6">
        <div class="flex items-center space-x-4 mb-3">
            <div class="w-12 h-12 rounded-xl bg-pink-500/20 border border-pink-500/30 flex items-center justify-center">
                <svg class="w-6 h-6 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white">Match Settings</h2>
                <p class="text-slate-400">Configure match settings for each round</p>
            </div>
        </div>
    </div>

    @if($rounds->isEmpty())
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
            </svg>
            <h3 class="text-xl font-bold text-slate-400 mb-2">No Rounds Available</h3>
            <p class="text-slate-500">Generate a bracket to configure match settings.</p>
        </div>
    @else
        @if($canEdit)
            {{-- Editable Settings Form for Referees/Staff --}}
            <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
                <div class="bg-gradient-to-r from-slate-800/50 to-slate-900/50 px-6 py-4 border-b border-slate-800">
                    <h3 class="text-lg font-bold text-white">Configure Settings</h3>
                    <p class="text-sm text-slate-400 mt-1">Set the Best Of format and Mappool for each round</p>
                </div>

                <form id="roundSettingsForm" class="p-6">
                    @csrf
                    <div class="space-y-4">
                        @foreach($rounds as $round)
                            <div class="bg-slate-800/50 rounded-lg p-5 border border-slate-700 hover:border-slate-600 transition-colors">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-lg bg-pink-500/20 border border-pink-500/30 flex items-center justify-center">
                                            <span class="text-pink-400 font-bold text-sm">R{{ $round['number'] }}</span>
                                        </div>
                                        <div>
                                            <h3 class="text-white font-semibold">{{ $round['name'] }}</h3>
                                            <p class="text-xs text-slate-400">{{ $round['count'] }} {{ Str::plural('match', $round['count']) }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    {{-- Best Of Setting --}}
                                    <div>
                                        <label for="round_{{ $round['number'] }}_best_of" class="block text-sm font-medium text-slate-300 mb-2">Best Of</label>
                                        <select name="rounds[{{ $round['number'] }}][best_of]" id="round_{{ $round['number'] }}_best_of" class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                            <option value="">Not Set</option>
                                            <option value="1" {{ $round['best_of'] == 1 ? 'selected' : '' }}>BO1</option>
                                            <option value="3" {{ $round['best_of'] == 3 ? 'selected' : '' }}>BO3</option>
                                            <option value="5" {{ $round['best_of'] == 5 ? 'selected' : '' }}>BO5</option>
                                            <option value="7" {{ $round['best_of'] == 7 ? 'selected' : '' }}>BO7</option>
                                            <option value="9" {{ $round['best_of'] == 9 ? 'selected' : '' }}>BO9</option>
                                            <option value="11" {{ $round['best_of'] == 11 ? 'selected' : '' }}>BO11</option>
                                            <option value="13" {{ $round['best_of'] == 13 ? 'selected' : '' }}>BO13</option>
                                        </select>
                                    </div>

                                    {{-- Mappool Assignment --}}
                                    <div>
                                        <label for="round_{{ $round['number'] }}_mappool" class="block text-sm font-medium text-slate-300 mb-2">Mappool</label>
                                        <select name="rounds[{{ $round['number'] }}][mappool_id]" id="round_{{ $round['number'] }}_mappool" class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                            <option value="">No Mappool</option>
                                            @foreach($tournament->mappools ?? [] as $mappool)
                                                <option value="{{ $mappool->id }}" {{ $round['mappool_id'] == $mappool->id ? 'selected' : '' }}>{{ $mappool->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-3 pt-6 border-t border-slate-700 mt-6">
                        <button type="submit" class="px-6 py-2.5 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
                            <span>Save All Settings</span>
                        </button>
                        <button type="button" onclick="window.location.reload()" class="px-6 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors">
                            Reset to Current
                        </button>
                    </div>
                </form>
            </div>
        @else
            {{-- Read-Only Display for Players/Visitors --}}
            <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
                <div class="bg-gradient-to-r from-slate-800/50 to-slate-900/50 px-6 py-4 border-b border-slate-800">
                    <h3 class="text-lg font-bold text-white">Match Settings Overview</h3>
                    <p class="text-sm text-slate-400 mt-1">View the configured settings for each round</p>
                </div>

                <div class="p-6 space-y-4">
                    @foreach($rounds as $round)
                        <div class="bg-slate-800/30 rounded-lg p-5 border border-slate-700">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-lg bg-pink-500/20 border border-pink-500/30 flex items-center justify-center">
                                        <span class="text-pink-400 font-bold text-sm">R{{ $round['number'] }}</span>
                                    </div>
                                    <div>
                                        <h3 class="text-white font-semibold">{{ $round['name'] }}</h3>
                                        <p class="text-xs text-slate-400">{{ $round['count'] }} {{ Str::plural('match', $round['count']) }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Best Of Display --}}
                                <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-800">
                                    <div class="flex items-center space-x-2 text-slate-400 mb-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                        </svg>
                                        <span class="text-xs font-medium">Match Format</span>
                                    </div>
                                    @if($round['best_of'])
                                        <div class="flex items-center space-x-2">
                                            <span class="text-2xl font-bold text-white">BO{{ $round['best_of'] }}</span>
                                            <span class="text-sm text-slate-400">(First to {{ ceil($round['best_of'] / 2) }})</span>
                                        </div>
                                    @else
                                        <p class="text-slate-500 italic">Not configured</p>
                                    @endif
                                </div>

                                {{-- Mappool Display --}}
                                <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-800">
                                    <div class="flex items-center space-x-2 text-slate-400 mb-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                                        </svg>
                                        <span class="text-xs font-medium">Mappool</span>
                                    </div>
                                    @if($round['mappool_id'])
                                        @php
                                            $mappool = $tournament->mappools->firstWhere('id', $round['mappool_id']);
                                        @endphp
                                        @if($mappool)
                                            <div class="flex items-center justify-between">
                                                <span class="text-white font-semibold">{{ $mappool->name }}</span>
                                                <a href="{{ route('dashboard.tournaments.mappools', $tournament) }}" class="text-xs text-pink-400 hover:text-pink-300 transition-colors">
                                                    View Maps
                                                </a>
                                            </div>
                                        @else
                                            <p class="text-slate-500 italic">Mappool not found</p>
                                        @endif
                                    @else
                                        <p class="text-slate-500 italic">No mappool assigned</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>

@if($canEdit)
<script>
    // Handle round settings form submission
    document.getElementById('roundSettingsForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);

        try {
            const response = await fetch(`{{ route('dashboard.tournaments.matches.update-round-settings', $tournament) }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'Accept': 'application/json',
                },
                body: formData
            });

            if (response.ok) {
                window.location.reload();
            } else {
                alert('Failed to update match settings');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred');
        }
    });
</script>
@endif
