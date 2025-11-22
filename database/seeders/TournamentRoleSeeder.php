<?php

namespace Database\Seeders;

use App\Models\TournamentRole;
use Illuminate\Database\Seeder;

class TournamentRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Host'],
            ['name' => 'Organizer'],
            ['name' => 'Referee'],
            ['name' => 'Pooler'],
            ['name' => 'Commentator'],
            ['name' => 'Streamer'],
            ['name' => 'Designer'],
            ['name' => 'Developer'],
        ];

        foreach ($roles as $role) {
            TournamentRole::firstOrCreate($role);
        }
    }
}
