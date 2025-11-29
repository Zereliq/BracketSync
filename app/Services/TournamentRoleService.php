<?php

namespace App\Services;

class TournamentRoleService
{
    /**
     * Get the standard role definitions with permissions.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getStandardRoles(): array
    {
        return [
            [
                'name' => 'Host',
                'description' => 'Full control over the tournament',
                'is_protected' => true,
                'permissions' => [
                    'tournament' => 'edit',
                    'staff' => 'edit',
                    'players' => 'edit',
                    'teams' => 'edit',
                    'qualifiers' => 'edit',
                    'matches' => 'edit',
                    'bracket' => 'edit',
                    'mappools' => 'edit',
                ],
            ],
            [
                'name' => 'Organizer',
                'description' => 'Manages tournament operations and staff',
                'is_protected' => true,
                'permissions' => [
                    'tournament' => 'edit',
                    'staff' => 'edit',
                    'players' => 'edit',
                    'teams' => 'edit',
                    'qualifiers' => 'edit',
                    'matches' => 'edit',
                    'bracket' => 'edit',
                    'mappools' => 'edit',
                ],
            ],
            [
                'name' => 'Referee',
                'description' => 'Manages and officiates matches',
                'is_protected' => true,
                'permissions' => [
                    'tournament' => 'view',
                    'staff' => 'view',
                    'players' => 'edit',
                    'teams' => 'edit',
                    'qualifiers' => 'edit',
                    'matches' => 'edit',
                    'bracket' => 'view',
                    'mappools' => 'view',
                ],
            ],
            [
                'name' => 'Pooler',
                'description' => 'Creates and manages map pools',
                'is_protected' => true,
                'permissions' => [
                    'tournament' => 'view',
                    'staff' => 'view',
                    'players' => 'view',
                    'teams' => 'view',
                    'qualifiers' => 'view',
                    'matches' => 'view',
                    'bracket' => 'view',
                    'mappools' => 'edit',
                ],
            ],
            [
                'name' => 'Playtester',
                'description' => 'Tests maps and provides feedback',
                'is_protected' => true,
                'permissions' => [
                    'tournament' => 'view',
                    'staff' => 'view',
                    'players' => 'view',
                    'teams' => 'view',
                    'qualifiers' => 'view',
                    'matches' => 'view',
                    'bracket' => 'view',
                    'mappools' => 'view',
                ],
            ],
            [
                'name' => 'Streamer',
                'description' => 'Streams tournament matches',
                'is_protected' => true,
                'permissions' => [
                    'tournament' => 'view',
                    'staff' => 'view',
                    'players' => 'view',
                    'teams' => 'view',
                    'qualifiers' => 'view',
                    'matches' => 'view',
                    'bracket' => 'view',
                    'mappools' => 'view',
                ],
            ],
            [
                'name' => 'Commentator',
                'description' => 'Provides commentary for matches',
                'is_protected' => true,
                'permissions' => [
                    'tournament' => 'view',
                    'staff' => 'view',
                    'players' => 'view',
                    'teams' => 'view',
                    'qualifiers' => 'view',
                    'matches' => 'view',
                    'bracket' => 'view',
                    'mappools' => 'view',
                ],
            ],
            [
                'name' => 'Designer',
                'description' => 'Creates graphics and promotional materials',
                'is_protected' => true,
                'permissions' => [
                    'tournament' => 'view',
                    'staff' => 'view',
                    'players' => 'view',
                    'teams' => 'view',
                    'qualifiers' => 'view',
                    'matches' => 'view',
                    'bracket' => 'view',
                    'mappools' => 'view',
                ],
            ],
            [
                'name' => 'Developer',
                'description' => 'Handles technical aspects and integrations',
                'is_protected' => true,
                'permissions' => [
                    'tournament' => 'view',
                    'staff' => 'view',
                    'players' => 'view',
                    'teams' => 'view',
                    'qualifiers' => 'view',
                    'matches' => 'view',
                    'bracket' => 'view',
                    'mappools' => 'view',
                ],
            ],
        ];
    }
}
