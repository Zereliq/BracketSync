<?php

namespace App\Console\Commands;

use App\Models\Tournament;
use Illuminate\Console\Command;

class UpdateTournamentStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tournaments:update-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update tournament statuses based on registration dates';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Find tournaments that are announced but registration has started
        $tournaments = Tournament::query()
            ->where('status', 'announced')
            ->whereNotNull('signup_start')
            ->where('signup_start', '<=', now())
            ->get();

        if ($tournaments->isEmpty()) {
            $this->info('No tournaments need status update.');

            return Command::SUCCESS;
        }

        $count = 0;

        foreach ($tournaments as $tournament) {
            $tournament->update(['status' => 'registrations']);
            $count++;

            $this->info("Updated tournament '{$tournament->name}' to registrations status.");
        }

        $this->info("Successfully updated {$count} tournament(s).");

        return Command::SUCCESS;
    }
}
