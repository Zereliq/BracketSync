@php
    $isDashboard = $isDashboard ?? request()->routeIs('dashboard.*');
    $canEdit = auth()->check() && auth()->user()->can('update', $tournament);
    $mappools = $mappools ?? collect();
    $routePrefix = $isDashboard ? 'dashboard.tournaments.' : 'tournaments.';
@endphp

<div class="space-y-6">
    @if($canEdit)
        <div class="flex justify-end">
            <button type="button" class="px-6 py-2.5 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors">
                Create Mappool
            </button>
        </div>
    @endif

    @if($mappools->isEmpty())
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
            </svg>
            <h3 class="text-xl font-bold text-slate-400 mb-2">No Mappools</h3>
            <p class="text-slate-500">No mappools have been created for this tournament yet.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-4">
            @foreach($mappools as $mappool)
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-white">{{ $mappool->name }}</h3>
                            @if($mappool->stage)
                                <p class="text-sm text-slate-400">{{ $mappool->stage }}</p>
                            @endif
                        </div>
                        @if($canEdit)
                            <div class="flex items-center space-x-2">
                                <button type="button" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium rounded-lg transition-colors">
                                    Edit
                                </button>
                                <button type="button" class="text-slate-400 hover:text-red-400 transition-colors" title="Delete mappool">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        @endif
                    </div>

                    @if($mappool->maps && $mappool->maps->isNotEmpty())
                        <div class="space-y-2">
                            @php
                                $mapsByMod = $mappool->maps->groupBy('mod');
                            @endphp

                            @foreach($mapsByMod as $mod => $maps)
                                <div class="bg-slate-800/50 rounded-lg p-4">
                                    <div class="flex items-center space-x-2 mb-3">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold
                                            @if($mod === 'NM') bg-blue-500/20 text-blue-400
                                            @elseif($mod === 'HD') bg-yellow-500/20 text-yellow-400
                                            @elseif($mod === 'HR') bg-red-500/20 text-red-400
                                            @elseif($mod === 'DT') bg-purple-500/20 text-purple-400
                                            @elseif($mod === 'FM') bg-green-500/20 text-green-400
                                            @elseif($mod === 'TB') bg-pink-500/20 text-pink-400
                                            @else bg-slate-500/20 text-slate-400
                                            @endif">
                                            {{ $mod }}
                                        </span>
                                        <span class="text-sm text-slate-400">{{ $maps->count() }} {{ $maps->count() === 1 ? 'map' : 'maps' }}</span>
                                    </div>

                                    <div class="space-y-2">
                                        @foreach($maps as $index => $map)
                                            <div class="flex items-center justify-between p-3 bg-slate-900 rounded-lg border border-slate-700 hover:border-slate-600 transition-colors">
                                                <div class="flex items-center space-x-3 flex-1 min-w-0">
                                                    <span class="text-slate-500 font-medium">{{ $mod }}{{ $index + 1 }}</span>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-white font-medium truncate">{{ $map->artist }} - {{ $map->title }}</p>
                                                        <p class="text-sm text-slate-400 truncate">{{ $map->difficulty }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-4 ml-4">
                                                    @if($map->star_rating)
                                                        <div class="flex items-center space-x-1">
                                                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                            </svg>
                                                            <span class="text-sm text-slate-300">{{ number_format($map->star_rating, 2) }}</span>
                                                        </div>
                                                    @endif
                                                    @if($map->beatmap_id)
                                                        <a href="https://osu.ppy.sh/b/{{ $map->beatmap_id }}" target="_blank" class="text-pink-400 hover:text-pink-300 transition-colors">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                            </svg>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-slate-500">No maps in this pool</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
