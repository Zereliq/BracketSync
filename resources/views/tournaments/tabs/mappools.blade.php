@php
    $isDashboard = $isDashboard ?? request()->routeIs('dashboard.*');
    $canEdit = auth()->check() && auth()->user()->can('editMappools', $tournament);
    $mappools = $mappools ?? collect();
    $routePrefix = $isDashboard ? 'dashboard.tournaments.' : 'tournaments.';
    $currentMappoolId = $currentMappoolId ?? null;
@endphp

<div class="space-y-6">
    @if($canEdit)
        <div class="flex justify-end">
            <a href="{{ route('dashboard.tournaments.mappools.create', $tournament) }}" class="px-6 py-2.5 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors inline-flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Create Mappool</span>
            </a>
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
                @php
                    $isCurrentMappool = $currentMappoolId && $mappool->id === $currentMappoolId;
                    $mappoolId = 'mappool-' . $mappool->id;
                @endphp
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 {{ $isCurrentMappool ? 'ring-2 ring-pink-500/50' : '' }}">
                    {{-- Collapsible Header --}}
                    <div class="flex items-center justify-between" id="{{ $mappoolId }}-header">
                        <button
                            onclick="toggleMappool('{{ $mappoolId }}')"
                            class="flex items-center space-x-4 flex-1 hover:opacity-80 transition-opacity text-left"
                        >
                            <svg class="w-5 h-5 text-slate-400 transition-transform duration-200 mappool-chevron flex-shrink-0" id="{{ $mappoolId }}-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                            <div>
                                <div class="flex items-center space-x-2">
                                    <h3 class="text-xl font-bold text-white">{{ $mappool->name }}</h3>
                                    @if($isCurrentMappool)
                                        <span class="px-2 py-1 bg-pink-500/20 text-pink-400 text-xs font-bold rounded-full">Current</span>
                                    @endif
                                </div>
                                @if($mappool->stage)
                                    <p class="text-sm text-slate-400">{{ $mappool->stage }}</p>
                                @endif
                            </div>
                        </button>
                        @if($canEdit)
                            <div class="flex items-center space-x-2 flex-shrink-0">
                                <a href="{{ route('dashboard.tournaments.mappools.edit', [$tournament, $mappool]) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium rounded-lg transition-colors">
                                    Edit
                                </a>
                                <form action="{{ route('dashboard.tournaments.mappools.destroy', [$tournament, $mappool]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this mappool?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-400 transition-colors" title="Delete mappool">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    {{-- Collapsible Content --}}
                    <div id="{{ $mappoolId }}" class="mappool-content mt-4" style="display: {{ $isCurrentMappool ? 'block' : 'none' }}">
                        @if($mappool->maps && $mappool->maps->isNotEmpty())
                            <div class="space-y-2">
                                @php
                                    $mapsByMod = $mappool->maps->groupBy('mod_type');
                                    // Define the order of mod types
                                    $modOrder = ['NM', 'HD', 'HR', 'DT', 'FM', 'TB'];
                                    // Sort the groups by the defined order
                                    $sortedMapsByMod = collect($modOrder)
                                        ->filter(fn($mod) => $mapsByMod->has($mod))
                                        ->mapWithKeys(fn($mod) => [$mod => $mapsByMod->get($mod)]);
                                @endphp

                            @foreach($sortedMapsByMod as $mod => $maps)
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
                                        @foreach($maps as $mappoolMap)
                                            <div class="relative overflow-hidden rounded-lg border border-slate-700 hover:border-slate-600 transition-all group">
                                                {{-- Background Banner --}}
                                                @if($mappoolMap->map->cover_url)
                                                    <div class="absolute inset-0 bg-cover bg-center opacity-50 group-hover:opacity-60 transition-opacity" style="background-image: url('{{ $mappoolMap->map->cover_url }}');"></div>
                                                    <div class="absolute inset-0 bg-gradient-to-r from-slate-900/90 via-slate-900/70 to-slate-900/50"></div>
                                                @else
                                                    <div class="absolute inset-0 bg-slate-900"></div>
                                                @endif

                                                {{-- Content --}}
                                                <div class="relative flex items-center justify-between p-3">
                                                    <div class="flex items-center space-x-3 flex-1 min-w-0">
                                                        <span class="text-slate-400 font-bold">{{ $mappoolMap->mod_type }}{{ $mappoolMap->slot }}</span>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-white font-semibold truncate">{{ $mappoolMap->map->artist }} - {{ $mappoolMap->map->title }}</p>
                                                            <p class="text-xs text-slate-300 truncate">[{{ $mappoolMap->map->version }}]</p>
                                                        </div>
                                                    </div>

                                                    <div class="flex items-center space-x-3 ml-4">
                                                        {{-- Map Statistics --}}
                                                        <div class="flex items-center gap-3 flex-wrap">
                                                            @if($mappoolMap->map->length_seconds)
                                                                <div class="flex items-center space-x-1">
                                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                    </svg>
                                                                    <span class="text-xs text-slate-400">{{ gmdate('i:s', $mappoolMap->map->length_seconds) }}</span>
                                                                </div>
                                                            @endif
                                                            @if($mappoolMap->map->bpm)
                                                                <div class="flex items-center space-x-1">
                                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                                                                    </svg>
                                                                    <span class="text-xs text-slate-400">{{ number_format($mappoolMap->map->bpm, 0) }}</span>
                                                                </div>
                                                            @endif
                                                            @if($mappoolMap->map->cs)
                                                                <span class="text-xs text-slate-400">CS<span class="text-white font-semibold ml-0.5">{{ number_format($mappoolMap->map->cs, 1) }}</span></span>
                                                            @endif
                                                            @if($mappoolMap->map->ar)
                                                                <span class="text-xs text-slate-400">AR<span class="text-white font-semibold ml-0.5">{{ number_format($mappoolMap->map->ar, 1) }}</span></span>
                                                            @endif
                                                            @if($mappoolMap->map->od)
                                                                <span class="text-xs text-slate-400">OD<span class="text-white font-semibold ml-0.5">{{ number_format($mappoolMap->map->od, 1) }}</span></span>
                                                            @endif
                                                            @if($mappoolMap->map->hp)
                                                                <span class="text-xs text-slate-400">HP<span class="text-white font-semibold ml-0.5">{{ number_format($mappoolMap->map->hp, 1) }}</span></span>
                                                            @endif
                                                        </div>

                                                        @if($mappoolMap->map->star_rating)
                                                            <div class="flex items-center space-x-1 px-2 py-1 bg-yellow-500/20 rounded-lg border border-yellow-500/30">
                                                                <svg class="w-3.5 h-3.5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                                </svg>
                                                                <span class="text-xs font-bold text-yellow-300">{{ number_format($mappoolMap->map->star_rating, 2) }}</span>
                                                            </div>
                                                        @endif

                                                        {{-- Copy Beatmap ID Button --}}
                                                        @if($mappoolMap->map->osu_beatmap_id)
                                                            <button onclick="copyBeatmapId({{ $mappoolMap->map->osu_beatmap_id }})" class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-lg transition-colors" title="Copy Beatmap ID">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                                </svg>
                                                            </button>

                                                            {{-- Link to osu! --}}
                                                            <a href="https://osu.ppy.sh/b/{{ $mappoolMap->map->osu_beatmap_id }}" target="_blank" class="p-1.5 bg-pink-500 hover:bg-pink-600 text-white rounded-lg transition-colors" title="Open on osu!">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                                </svg>
                                                            </a>
                                                        @endif
                                                    </div>
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
                </div>
            @endforeach
        </div>
    @endif
</div>

<script>
    function toggleMappool(mappoolId) {
        const content = document.getElementById(mappoolId);
        const chevron = document.getElementById(mappoolId + '-chevron');

        if (content.style.display === 'none') {
            content.style.display = 'block';
            chevron.style.transform = 'rotate(0deg)';
        } else {
            content.style.display = 'none';
            chevron.style.transform = 'rotate(-90deg)';
        }
    }

    // Initialize chevron rotation for collapsed mappools
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.mappool-content').forEach(function(content) {
            if (content.style.display === 'none') {
                const chevron = document.getElementById(content.id + '-chevron');
                if (chevron) {
                    chevron.style.transform = 'rotate(-90deg)';
                }
            }
        });
    });

    function copyBeatmapId(beatmapId) {
        const button = event.currentTarget;
        const originalHTML = button.innerHTML;

        // Try modern clipboard API first
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(beatmapId).then(() => {
                showCopySuccess(button, originalHTML);
            }).catch(err => {
                console.error('Clipboard API failed:', err);
                fallbackCopy(beatmapId, button, originalHTML);
            });
        } else {
            // Fallback for older browsers or non-secure contexts
            fallbackCopy(beatmapId, button, originalHTML);
        }
    }

    function fallbackCopy(text, button, originalHTML) {
        // Create a temporary textarea
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();

        try {
            const successful = document.execCommand('copy');
            if (successful) {
                showCopySuccess(button, originalHTML);
            } else {
                showCopyError();
            }
        } catch (err) {
            console.error('Fallback copy failed:', err);
            showCopyError();
        }

        document.body.removeChild(textarea);
    }

    function showCopySuccess(button, originalHTML) {
        button.innerHTML = `
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        `;
        button.classList.remove('bg-slate-800');
        button.classList.add('bg-green-600');

        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.classList.remove('bg-green-600');
            button.classList.add('bg-slate-800');
        }, 1500);
    }

    function showCopyError() {
        alert('Failed to copy beatmap ID. Please copy manually from the URL.');
    }
</script>
