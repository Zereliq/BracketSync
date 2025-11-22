<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteRole;
use App\Models\User;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    /**
     * Display a listing of users with their roles.
     */
    public function index(Request $request): mixed
    {
        if (! auth()->check()) {
            return redirect()->route('auth.osu.redirect')
                ->with('error', 'You must be logged in to access this page.');
        }

        if (! auth()->user()->isAdmin()) {
            abort(403, 'Only administrators can access this area.');
        }

        $q = $request->query('q');

        $users = User::with('siteRole')
            ->when($q, function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('osu_username', 'like', "%{$q}%");
            })
            ->orderBy('siterole_id', 'asc')
            ->paginate(20)
            ->withQueryString();

        $roles = SiteRole::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Update the role of a specific user.
     */
    public function update(Request $request, User $user)
    {
        if (! auth()->check()) {
            return redirect()->route('auth.osu.redirect')
                ->with('error', 'You must be logged in to access this page.');
        }

        if (! auth()->user()->isAdmin()) {
            abort(403, 'Only administrators can access this area.');
        }

        $validated = $request->validate([
            'role' => 'required|string|in:player,mod,admin',
        ]);

        $role = SiteRole::where('name', $validated['role'])->first();

        if (! $role) {
            return back()->with('error', 'Invalid role selected.');
        }

        $user->update([
            'siterole_id' => $role->id,
        ]);

        return back()->with('success', "User {$user->name} role updated to {$role->name}.");
    }
}
