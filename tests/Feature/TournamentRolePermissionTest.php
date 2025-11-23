<?php

use App\Models\Tournament;
use App\Models\TournamentRole;
use App\Models\TournamentRolePermission;
use App\Models\TournamentRoleUser;
use App\Models\User;

test('host can create custom roles for tournament', function () {
    $user = User::factory()->create();
    $tournament = Tournament::factory()->create(['created_by' => $user->id]);

    $this->actingAs($user);

    $response = $this->post(route('dashboard.tournaments.roles.store', $tournament), [
        'name' => 'Map Selector',
        'description' => 'Selects maps for the tournament',
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
    ]);

    $response->assertRedirect(route('dashboard.tournaments.roles.index', $tournament));
    $this->assertDatabaseHas('tournamentroles', [
        'tournament_id' => $tournament->id,
        'name' => 'Map Selector',
        'description' => 'Selects maps for the tournament',
    ]);
});

test('custom role permissions are saved correctly', function () {
    $user = User::factory()->create();
    $tournament = Tournament::factory()->create(['created_by' => $user->id]);

    $role = TournamentRole::create([
        'tournament_id' => $tournament->id,
        'name' => 'Test Role',
        'is_protected' => false,
    ]);

    TournamentRolePermission::create([
        'role_id' => $role->id,
        'resource' => 'mappools',
        'permission' => 'edit',
    ]);

    TournamentRolePermission::create([
        'role_id' => $role->id,
        'resource' => 'players',
        'permission' => 'view',
    ]);

    expect($role->hasPermission('mappools', 'edit'))->toBeTrue();
    expect($role->hasPermission('mappools', 'view'))->toBeTrue();
    expect($role->hasPermission('players', 'view'))->toBeTrue();
    expect($role->hasPermission('players', 'edit'))->toBeFalse();
});

test('user with role can access permitted tabs', function () {
    $creator = User::factory()->create();
    $staffUser = User::factory()->create();
    $tournament = Tournament::factory()->create(['created_by' => $creator->id]);

    $role = TournamentRole::create([
        'tournament_id' => $tournament->id,
        'name' => 'Map Manager',
        'is_protected' => false,
    ]);

    TournamentRolePermission::create([
        'role_id' => $role->id,
        'resource' => 'mappools',
        'permission' => 'edit',
    ]);

    TournamentRolePermission::create([
        'role_id' => $role->id,
        'resource' => 'tournament',
        'permission' => 'view',
    ]);

    TournamentRoleUser::create([
        'tournament_id' => $tournament->id,
        'user_id' => $staffUser->id,
        'role_id' => $role->id,
    ]);

    $this->actingAs($staffUser);

    // Should be able to access mappools
    $response = $this->get(route('dashboard.tournaments.mappools', $tournament));
    $response->assertOk();

    // Should be able to view tournament settings
    $response = $this->get(route('dashboard.tournaments.show', $tournament));
    $response->assertOk();
});

test('user without permission cannot access restricted tabs', function () {
    $creator = User::factory()->create();
    $staffUser = User::factory()->create();
    $tournament = Tournament::factory()->create(['created_by' => $creator->id]);

    $role = TournamentRole::create([
        'tournament_id' => $tournament->id,
        'name' => 'Limited Role',
        'is_protected' => false,
    ]);

    TournamentRolePermission::create([
        'role_id' => $role->id,
        'resource' => 'players',
        'permission' => 'view',
    ]);

    TournamentRolePermission::create([
        'role_id' => $role->id,
        'resource' => 'mappools',
        'permission' => 'none',
    ]);

    TournamentRoleUser::create([
        'tournament_id' => $tournament->id,
        'user_id' => $staffUser->id,
        'role_id' => $role->id,
    ]);

    $this->actingAs($staffUser);

    // Should NOT be able to access mappools
    $response = $this->get(route('dashboard.tournaments.mappools', $tournament));
    $response->assertRedirect();
});

test('tournament creator always has full permissions', function () {
    $creator = User::factory()->create();
    $tournament = Tournament::factory()->create(['created_by' => $creator->id]);

    expect($tournament->userHasPermission($creator, 'tournament', 'edit'))->toBeTrue();
    expect($tournament->userHasPermission($creator, 'staff', 'edit'))->toBeTrue();
    expect($tournament->userHasPermission($creator, 'players', 'edit'))->toBeTrue();
    expect($tournament->userHasPermission($creator, 'mappools', 'edit'))->toBeTrue();
});

test('protected roles cannot be deleted', function () {
    $user = User::factory()->create();
    $tournament = Tournament::factory()->create(['created_by' => $user->id]);

    $role = TournamentRole::create([
        'tournament_id' => $tournament->id,
        'name' => 'Protected Role',
        'is_protected' => true,
    ]);

    $this->actingAs($user);

    $response = $this->delete(route('dashboard.tournaments.roles.destroy', [$tournament, $role]));
    $response->assertForbidden();
});

test('roles with assigned users cannot be deleted', function () {
    $creator = User::factory()->create();
    $staffUser = User::factory()->create();
    $tournament = Tournament::factory()->create(['created_by' => $creator->id]);

    $role = TournamentRole::create([
        'tournament_id' => $tournament->id,
        'name' => 'Assigned Role',
        'is_protected' => false,
    ]);

    TournamentRoleUser::create([
        'tournament_id' => $tournament->id,
        'user_id' => $staffUser->id,
        'role_id' => $role->id,
    ]);

    $this->actingAs($creator);

    $response = $this->delete(route('dashboard.tournaments.roles.destroy', [$tournament, $role]));
    $response->assertRedirect();
    $this->assertDatabaseHas('tournamentroles', ['id' => $role->id]);
});
