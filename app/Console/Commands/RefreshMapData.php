<?php

namespace App\Console\Commands;

use App\Models\Map;
use App\Services\OsuApiService;
use Illuminate\Console\Command;

class RefreshMapData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maps:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh all map data from osu! API to populate new fields';

    /**
     * Execute the console command.
     */
    public function handle(OsuApiService $osuApi)
    {
        $maps = Map::all();
        $this->info("Refreshing {$maps->count()} maps...");

        $bar = $this->output->createProgressBar($maps->count());
        $bar->start();

        $updated = 0;
        $failed = 0;

        foreach ($maps as $map) {
            $beatmapData = $osuApi->getBeatmap($map->osu_beatmap_id);

            if ($beatmapData) {
                $map->update($beatmapData);
                $updated++;
            } else {
                $failed++;
                $this->newLine();
                $this->warn("Failed to fetch data for beatmap ID: {$map->osu_beatmap_id}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Successfully updated {$updated} maps.");

        if ($failed > 0) {
            $this->warn("Failed to update {$failed} maps.");
        }

        return 0;
    }
}
