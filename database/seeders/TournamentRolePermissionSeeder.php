<?php

namespace Database\Seeders;

use App\Models\TournamentRole;
use App\Models\TournamentRolePermission;
use App\Services\TournamentRoleService;
use Illuminate\Database\Seeder;

class TournamentRolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $standardRoles = TournamentRoleService::getStandardRoles();

        foreach ($standardRoles as $roleData) {
            $role = TournamentRole::where('name', $roleData['name'])
                ->whereNull('tournament_id')
                ->first();

            if (! $role) {
                continue;
            }

            foreach ($roleData['permissions'] as $resource => $permission) {
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
