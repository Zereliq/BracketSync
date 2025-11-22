<?php

namespace Database\Seeders;

use App\Models\SiteRole;
use Illuminate\Database\Seeder;

class SiteRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['player', 'mod', 'admin'];

        foreach ($roles as $roleName) {
            SiteRole::firstOrCreate(
                ['name' => $roleName]
            );
        }
    }
}
