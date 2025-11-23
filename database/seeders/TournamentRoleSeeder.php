<?php

namespace Database\Seeders;

use App\Models\TournamentRole;
use Illuminate\Database\Seeder;

class TournamentRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Host',
                'tournament_id' => null,
                'is_protected' => true,
                'description' => 'Full tournament management access',
            ],
            [
                'name' => 'Organizer',
                'tournament_id' => null,
                'is_protected' => true,
                'description' => 'Full tournament management access',
            ],
            [
                'name' => 'Referee',
                'tournament_id' => null,
                'is_protected' => true,
                'description' => 'Manages and officiates matches',
            ],
            [
                'name' => 'Pooler',
                'tournament_id' => null,
                'is_protected' => true,
                'description' => 'Creates and manages mappools',
            ],
            [
                'name' => 'Commentator',
                'tournament_id' => null,
                'is_protected' => true,
                'description' => 'Provides live commentary for matches',
            ],
            [
                'name' => 'Streamer',
                'tournament_id' => null,
                'is_protected' => true,
                'description' => 'Broadcasts tournament matches',
            ],
            [
                'name' => 'Designer',
                'tournament_id' => null,
                'is_protected' => true,
                'description' => 'Creates graphics and visual assets',
            ],
            [
                'name' => 'Developer',
                'tournament_id' => null,
                'is_protected' => true,
                'description' => 'Technical and development support',
            ],
        ];

        foreach ($roles as $role) {
            TournamentRole::firstOrCreate(
                ['name' => $role['name'], 'tournament_id' => null],
                $role
            );
        }
    }
}
