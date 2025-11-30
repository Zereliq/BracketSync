<?php

namespace App\Http\Controllers;

use App\Models\Map;
use App\Models\Mappool;
use App\Models\MappoolMap;
use App\Models\Tournament;
use App\Services\OsuApiService;
use Illuminate\Http\Request;

class MappoolController extends Controller
{
    public function __construct(private OsuApiService $osuApi) {}

    /**
     * Show the form for creating a new mappool.
     */
    public function create(Tournament $tournament)
    {
        if (! auth()->check() || ! auth()->user()->can('editMappools', $tournament)) {
            return redirect()
                ->route('dashboard.tournaments.show', $tournament)
                ->with('error', 'You do not have permission to create mappools.');
        }

        return view('dashboard.tournaments.mappools.create', [
            'tournament' => $tournament,
        ]);
    }

    /**
     * Store a newly created mappool in storage.
     */
    public function store(Request $request, Tournament $tournament)
    {
        if (! auth()->check() || ! auth()->user()->can('editMappools', $tournament)) {
            return back()->with('error', 'You do not have permission to create mappools.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'stage' => 'required|string|max:255',
            'is_public' => 'boolean',
        ]);

        $mappool = Mappool::create([
            'tournament_id' => $tournament->id,
            'name' => $request->name,
            'stage' => $request->stage,
            'is_public' => $request->boolean('is_public', false),
        ]);

        return redirect()
            ->route('dashboard.tournaments.mappools.edit', [$tournament, $mappool])
            ->with('success', 'Mappool created successfully! Now add maps to it.');
    }

    /**
     * Show the form for editing the specified mappool.
     */
    public function edit(Tournament $tournament, Mappool $mappool)
    {
        if (! auth()->check() || ! auth()->user()->can('editMappools', $tournament)) {
            return redirect()
                ->route('dashboard.tournaments.show', $tournament)
                ->with('error', 'You do not have permission to edit mappools.');
        }

        $mappool->load(['maps.map']);

        return view('dashboard.tournaments.mappools.edit', [
            'tournament' => $tournament,
            'mappool' => $mappool,
        ]);
    }

    /**
     * Add a map to the mappool.
     */
    public function addMap(Request $request, Tournament $tournament, Mappool $mappool)
    {
        if (! auth()->check() || ! auth()->user()->can('editMappools', $tournament)) {
            return back()->with('error', 'You do not have permission to add maps.');
        }

        $request->validate([
            'beatmap_id' => 'required|integer',
            'mod_type' => 'required|in:NM,HD,HR,DT,FM,TB',
            'slot' => 'required|string|max:10',
        ]);

        // Fetch beatmap data from osu! API
        $beatmapData = $this->osuApi->getBeatmap($request->beatmap_id);

        if (! $beatmapData) {
            return back()->with('error', 'Failed to fetch beatmap data from osu! API. Please check the beatmap ID.');
        }

        // Create or update the map in our database
        $map = Map::updateOrCreate(
            ['osu_beatmap_id' => $beatmapData['osu_beatmap_id']],
            $beatmapData
        );

        // Add map to mappool
        MappoolMap::create([
            'mappool_id' => $mappool->id,
            'map_id' => $map->id,
            'slot' => $request->slot,
            'mod_type' => $request->mod_type,
            'is_tiebreaker' => $request->mod_type === 'TB',
        ]);

        return back()->with('success', "Successfully added {$map->artist} - {$map->title} [{$map->version}] to the mappool!");
    }

    /**
     * Remove a map from the mappool.
     */
    public function removeMap(Tournament $tournament, Mappool $mappool, MappoolMap $mappoolMap)
    {
        if (! auth()->check() || ! auth()->user()->can('editMappools', $tournament)) {
            return back()->with('error', 'You do not have permission to remove maps.');
        }

        $mappoolMap->delete();

        return back()->with('success', 'Map removed from mappool successfully.');
    }

    /**
     * Delete the specified mappool.
     */
    public function destroy(Tournament $tournament, Mappool $mappool)
    {
        if (! auth()->check() || ! auth()->user()->can('editMappools', $tournament)) {
            return back()->with('error', 'You do not have permission to delete mappools.');
        }

        $mappool->delete();

        return redirect()
            ->route('dashboard.tournaments.show', [$tournament, 'tab' => 'mappools'])
            ->with('success', 'Mappool deleted successfully.');
    }
}
