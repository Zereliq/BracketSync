<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TournamentRoleController extends Controller
{
    public function index(Tournament $tournament)
    {
        Gate::authorize('update', $tournament);

        if (! $tournament->isHost()) {
            abort(403, 'Only the tournament host can manage roles.');
        }

        $roles = $tournament->customRoles()->with('permissions')->get();

        return view('dashboard.tournaments.roles.index', compact('tournament', 'roles'));
    }

    public function create(Tournament $tournament)
    {
        Gate::authorize('update', $tournament);

        if (! $tournament->isHost()) {
            abort(403, 'Only the tournament host can manage roles.');
        }

        $resources = [
            'tournament' => 'Tournament Settings',
            'staff' => 'Staff Management',
            'players' => 'Players',
            'teams' => 'Teams',
            'qualifiers' => 'Qualifiers',
            'matches' => 'Matches',
            'bracket' => 'Bracket',
            'mappools' => 'Mappools',
        ];

        return view('dashboard.tournaments.roles.create', compact('tournament', 'resources'));
    }

    public function store(Request $request, Tournament $tournament)
    {
        Gate::authorize('update', $tournament);

        if (! $tournament->isHost()) {
            abort(403, 'Only the tournament host can manage roles.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'permissions' => 'required|array',
            'permissions.*' => 'required|in:none,view,edit',
        ]);

        $role = $tournament->customRoles()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'is_protected' => false,
        ]);

        foreach ($validated['permissions'] as $resource => $permission) {
            $role->permissions()->create([
                'resource' => $resource,
                'permission' => $permission,
            ]);
        }

        return redirect()
            ->route('dashboard.tournaments.roles.index', $tournament)
            ->with('success', 'Role created successfully.');
    }

    public function edit(Tournament $tournament, TournamentRole $role)
    {
        Gate::authorize('update', $tournament);

        if (! $tournament->isHost()) {
            abort(403, 'Only the tournament host can manage roles.');
        }

        if ($role->tournament_id !== $tournament->id) {
            abort(404);
        }

        if ($role->is_protected) {
            abort(403, 'Protected roles cannot be edited.');
        }

        $resources = [
            'tournament' => 'Tournament Settings',
            'staff' => 'Staff Management',
            'players' => 'Players',
            'teams' => 'Teams',
            'qualifiers' => 'Qualifiers',
            'matches' => 'Matches',
            'bracket' => 'Bracket',
            'mappools' => 'Mappools',
        ];

        $role->load('permissions');

        $currentPermissions = $role->permissions->pluck('permission', 'resource')->toArray();

        return view('dashboard.tournaments.roles.edit', compact('tournament', 'role', 'resources', 'currentPermissions'));
    }

    public function update(Request $request, Tournament $tournament, TournamentRole $role)
    {
        Gate::authorize('update', $tournament);

        if (! $tournament->isHost()) {
            abort(403, 'Only the tournament host can manage roles.');
        }

        if ($role->tournament_id !== $tournament->id) {
            abort(404);
        }

        if ($role->is_protected) {
            abort(403, 'Protected roles cannot be edited.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'permissions' => 'required|array',
            'permissions.*' => 'required|in:none,view,edit',
        ]);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        $role->permissions()->delete();

        foreach ($validated['permissions'] as $resource => $permission) {
            $role->permissions()->create([
                'resource' => $resource,
                'permission' => $permission,
            ]);
        }

        return redirect()
            ->route('dashboard.tournaments.roles.index', $tournament)
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Tournament $tournament, TournamentRole $role)
    {
        Gate::authorize('update', $tournament);

        if (! $tournament->isHost()) {
            abort(403, 'Only the tournament host can manage roles.');
        }

        if ($role->tournament_id !== $tournament->id) {
            abort(404);
        }

        if ($role->is_protected) {
            abort(403, 'Protected roles cannot be deleted.');
        }

        // Check if any users have this role
        if ($role->links()->count() > 0) {
            return redirect()
                ->route('dashboard.tournaments.roles.index', $tournament)
                ->with('error', 'Cannot delete role that is assigned to users.');
        }

        $role->delete();

        return redirect()
            ->route('dashboard.tournaments.roles.index', $tournament)
            ->with('success', 'Role deleted successfully.');
    }

    public function updateAll(Request $request, Tournament $tournament)
    {
        Gate::authorize('update', $tournament);

        if (! $tournament->isHost()) {
            abort(403, 'Only the tournament host can manage roles.');
        }

        $validated = $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'required|array',
            'roles.*.*' => 'required|in:none,view,edit',
        ]);

        $resources = ['tournament', 'staff', 'players', 'teams', 'qualifiers', 'matches', 'bracket', 'mappools'];

        foreach ($validated['roles'] as $roleId => $permissions) {
            $role = $tournament->customRoles()->find($roleId);

            if (! $role) {
                continue;
            }

            // Delete existing permissions
            $role->permissions()->delete();

            // Create new permissions
            foreach ($resources as $resource) {
                $permission = $permissions[$resource] ?? 'none';

                $role->permissions()->create([
                    'resource' => $resource,
                    'permission' => $permission,
                ]);
            }
        }

        return redirect()
            ->route('dashboard.tournaments.roles.index', $tournament)
            ->with('success', 'Role permissions updated successfully.');
    }

    public function storeCustom(Request $request, Tournament $tournament)
    {
        Gate::authorize('update', $tournament);

        if (! $tournament->isHost()) {
            abort(403, 'Only the tournament host can manage roles.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $role = $tournament->customRoles()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'is_protected' => false,
        ]);

        // Create default permissions (all set to 'none')
        $resources = ['tournament', 'staff', 'players', 'teams', 'qualifiers', 'matches', 'bracket', 'mappools'];

        foreach ($resources as $resource) {
            $role->permissions()->create([
                'resource' => $resource,
                'permission' => 'none',
            ]);
        }

        return redirect()
            ->route('dashboard.tournaments.roles.index', $tournament)
            ->with('success', "Custom role '{$role->name}' created successfully. Set permissions in the table above.");
    }
}
