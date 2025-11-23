<?php

namespace Database\Seeders;

use App\Models\TournamentRole;
use App\Models\TournamentRolePermission;
use Illuminate\Database\Seeder;

class TournamentRolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $resources = ['tournament', 'staff', 'players', 'teams', 'qualifiers', 'matches', 'bracket', 'mappools'];

        $rolePermissions = [
            'Host' => [
                'tournament' => 'edit',
                'staff' => 'edit',
                'players' => 'edit',
                'teams' => 'edit',
                'qualifiers' => 'edit',
                'matches' => 'edit',
                'bracket' => 'edit',
                'mappools' => 'edit',
            ],
            'Organizer' => [
                'tournament' => 'edit',
                'staff' => 'edit',
                'players' => 'edit',
                'teams' => 'edit',
                'qualifiers' => 'edit',
                'matches' => 'edit',
                'bracket' => 'edit',
                'mappools' => 'edit',
            ],
            'Referee' => [
                'tournament' => 'view',
                'staff' => 'view',
                'players' => 'view',
                'teams' => 'view',
                'qualifiers' => 'edit',
                'matches' => 'edit',
                'bracket' => 'view',
                'mappools' => 'view',
            ],
            'Pooler' => [
                'tournament' => 'view',
                'staff' => 'view',
                'players' => 'view',
                'teams' => 'view',
                'qualifiers' => 'view',
                'matches' => 'view',
                'bracket' => 'view',
                'mappools' => 'edit',
            ],
            'Commentator' => [
                'tournament' => 'view',
                'staff' => 'view',
                'players' => 'view',
                'teams' => 'view',
                'qualifiers' => 'view',
                'matches' => 'view',
                'bracket' => 'view',
                'mappools' => 'view',
            ],
            'Streamer' => [
                'tournament' => 'view',
                'staff' => 'view',
                'players' => 'view',
                'teams' => 'view',
                'qualifiers' => 'view',
                'matches' => 'view',
                'bracket' => 'view',
                'mappools' => 'view',
            ],
            'Designer' => [
                'tournament' => 'view',
                'staff' => 'view',
                'players' => 'view',
                'teams' => 'view',
                'qualifiers' => 'view',
                'matches' => 'view',
                'bracket' => 'view',
                'mappools' => 'view',
            ],
            'Developer' => [
                'tournament' => 'view',
                'staff' => 'view',
                'players' => 'view',
                'teams' => 'view',
                'qualifiers' => 'view',
                'matches' => 'view',
                'bracket' => 'view',
                'mappools' => 'view',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = TournamentRole::where('name', $roleName)
                ->whereNull('tournament_id')
                ->first();

            if (! $role) {
                continue;
            }

            foreach ($permissions as $resource => $permission) {
                TournamentRolePermission::firstOrCreate(
                    [
                        'role_id' => $role->id,
                        'resource' => $resource,
                    ],
                    [
                        'permission' => $permission,
                    ]
                );
            }
        }
    }
}
