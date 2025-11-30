@extends('layouts.dashboard')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('dashboard.tournaments.show', ['tournament' => $tournament, 'tab' => 'mappools']) }}" class="text-pink-400 hover:text-pink-300 transition-colors inline-flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Back to Mappools</span>
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-500/20 border border-green-500/30 text-green-400 px-6 py-4 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-500/20 border border-red-500/30 text-red-400 px-6 py-4 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="space-y-6">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-8">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-white">{{ $mappool->name }}</h1>
                <p class="text-slate-400 mt-2">{{ $mappool->stage }} • {{ $mappool->is_public ? 'Public' : 'Private' }}</p>
            </div>

            <div class="bg-slate-800/50 rounded-lg p-6">
                <h2 class="text-xl font-bold text-white mb-4">Add Beatmap</h2>

                <form action="{{ route('dashboard.tournaments.mappools.maps.add', [$tournament, $mappool]) }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-1">
                            <label for="beatmap_id" class="block text-sm font-medium text-slate-300 mb-2">
                                Beatmap ID <span class="text-red-400">*</span>
                            </label>
                            <input
                                type="number"
                                id="beatmap_id"
                                name="beatmap_id"
                                class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                placeholder="e.g., 3756824"
                                required
                            >
                            <p class="mt-1 text-xs text-slate-500">Find this in the beatmap URL: osu.ppy.sh/b/<strong>3756824</strong></p>
                            @error('beatmap_id')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-1">
                            <label for="mod_type" class="block text-sm font-medium text-slate-300 mb-2">
                                Mod Type <span class="text-red-400">*</span>
                            </label>
                            <select
                                id="mod_type"
                                name="mod_type"
                                class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                required
                            >
                                <option value="">Select mod type</option>
                                <option value="NM">NM (No Mod)</option>
                                <option value="HD">HD (Hidden)</option>
                                <option value="HR">HR (Hard Rock)</option>
                                <option value="DT">DT (Double Time)</option>
                                <option value="FM">FM (Freemod)</option>
                                <option value="TB">TB (Tiebreaker)</option>
                            </select>
                            @error('mod_type')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-1">
                            <label for="slot" class="block text-sm font-medium text-slate-300 mb-2">
                                Slot <span class="text-red-400">*</span>
                            </label>
                            <input
                                type="text"
                                id="slot"
                                name="slot"
                                class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                placeholder="e.g., NM1, HD2, TB1"
                                maxlength="10"
                                required
                            >
                            @error('slot')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors inline-flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>Add Map</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if($mappool->maps && $mappool->maps->isNotEmpty())
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-8">
                <h2 class="text-xl font-bold text-white mb-6">Maps in Pool ({{ $mappool->maps->count() }})</h2>

                @php
                    $mapsByMod = $mappool->maps->groupBy('mod_type');
                @endphp

                <div class="space-y-4">
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
                                @foreach($maps as $mappoolMap)
                                    <div class="flex items-center justify-between p-3 bg-slate-900 rounded-lg border border-slate-700 hover:border-slate-600 transition-colors">
                                        <div class="flex items-center space-x-3 flex-1 min-w-0">
                                            <span class="text-slate-500 font-medium">{{ $mappoolMap->slot }}</span>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-white font-medium truncate">{{ $mappoolMap->map->artist }} - {{ $mappoolMap->map->title }}</p>
                                                <p class="text-sm text-slate-400 truncate">{{ $mappoolMap->map->version }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4 ml-4">
                                            @if($mappoolMap->map->star_rating)
                                                <div class="flex items-center space-x-1">
                                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                    <span class="text-sm text-slate-300">{{ number_format($mappoolMap->map->star_rating, 2) }}</span>
                                                </div>
                                            @endif
                                            @if($mappoolMap->map->osu_beatmap_id)
                                                <a href="https://osu.ppy.sh/b/{{ $mappoolMap->map->osu_beatmap_id }}" target="_blank" class="text-pink-400 hover:text-pink-300 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                    </svg>
                                                </a>
                                            @endif
                                            <form action="{{ route('dashboard.tournaments.mappools.maps.remove', [$tournament, $mappool, $mappoolMap]) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this map from the pool?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-slate-400 hover:text-red-400 transition-colors" title="Remove map">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                </svg>
                <h3 class="text-xl font-bold text-slate-400 mb-2">No Maps Yet</h3>
                <p class="text-slate-500">Add beatmaps to this pool using the form above.</p>
            </div>
        @endif
    </div>
</div>
@endsection
