<?php

namespace App\Listeners;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LogCommandHistory
{
    public function handleStarting(CommandStarting $event): void
    {
        $cacheKey = 'command_start_time_'.$event->command.'_'.getmypid();
        Cache::put($cacheKey, microtime(true), 60);
    }

    public function handleFinished(CommandFinished $event): void
    {
        $command = $event->command;
        $cacheKey = 'command_start_time_'.$command.'_'.getmypid();
        $startTime = Cache::pull($cacheKey, microtime(true));
        $duration = (int) ((microtime(true) - $startTime) * 1000);

        // Prevent duplicate logging within 1 second
        $dedupeKey = 'command_logged_'.$command.'_'.getmypid().'_'.time();
        if (Cache::has($dedupeKey)) {
            return;
        }

        Cache::put($dedupeKey, true, 2);

        DB::table('command_history')->insert([
            'command' => $command,
            'output' => null,
            'exit_code' => $event->exitCode,
            'duration_ms' => $duration,
            'started_at' => now()->subMilliseconds($duration),
            'completed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
