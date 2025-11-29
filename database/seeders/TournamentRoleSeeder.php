<?php

namespace Database\Seeders;

use App\Models\TournamentRole;
use App\Services\TournamentRoleService;
use Illuminate\Database\Seeder;

class TournamentRoleSeeder extends Seeder
{
    public function run(): void
    {
        $standardRoles = TournamentRoleService::getStandardRoles();

        foreach ($standardRoles as $roleData) {
            // Remove permissions from role data for global role creation
            $roleDataWithoutPermissions = $roleData;
            unset($roleDataWithoutPermissions['permissions']);

            TournamentRole::firstOrCreate(
                [
                    'name' => $roleData['name'],
                    'tournament_id' => null,
                ],
                array_merge($roleDataWithoutPermissions, ['tournament_id' => null])
            );
        }
    }
}
