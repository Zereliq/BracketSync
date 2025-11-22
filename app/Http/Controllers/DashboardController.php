<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard home page.
     */
    public function index(Request $request): mixed
    {
        $user = auth()->user();

        // Start building the query
        $query = Tournament::query()
            ->where(function ($q) use ($user) {
                // Show tournaments created by user
                $q->where('created_by', $user->id)
                    // Or tournaments where user has a staff role
                    ->orWhereHas('tournamentRoleLinks', function ($roleQuery) use ($user) {
                        $roleQuery->where('user_id', $user->id);
                    })
                    // Or tournaments where user is registered as a player
                    ->orWhereHas('registeredPlayers', function ($playerQuery) use ($user) {
                        $playerQuery->where('user_id', $user->id);
                    });
            });

        // Apply visibility rules for archived and draft tournaments
        $query->where(function ($q) use ($user) {
            // For archived and draft tournaments, only show if user has a staff role
            $q->whereNotIn('status', ['archived', 'draft'])
                ->orWhere(function ($subQuery) use ($user) {
                    $subQuery->whereIn('status', ['archived', 'draft'])
                        ->where(function ($roleCheck) use ($user) {
                            // Show if user is creator
                            $roleCheck->where('created_by', $user->id)
                                // Or has a staff role
                                ->orWhereHas('tournamentRoleLinks', function ($roleQuery) use ($user) {
                                    $roleQuery->where('user_id', $user->id);
                                });
                        });
                });
        });

        // Filter by mode
        if ($request->filled('mode')) {
            $query->where('mode', $request->mode);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by team size
        if ($request->filled('team_size')) {
            if ($request->team_size === 'solo') {
                $query->where(function ($q) {
                    $q->where('format', 1)
                        ->orWhere('min_teamsize', 1)
                        ->where('max_teamsize', 1);
                });
            } else {
                $query->where(function ($q) {
                    $q->where('format', '>', 1)
                        ->orWhere('min_teamsize', '>', 1)
                        ->orWhere('max_teamsize', '>', 1);
                });
            }
        }

        // Filter by created tournaments only
        if ($request->filled('created') && $request->created === 'true') {
            $query->where('created_by', $user->id);
        }

        // Filter by staff tournaments
        if ($request->filled('staff') && $request->staff === 'true') {
            $query->whereHas('tournamentRoleLinks', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        // Filter by registered tournaments (as player)
        if ($request->filled('registered') && $request->registered === 'true') {
            $query->whereHas('registeredPlayers', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        // Sort by
        $sortBy = $request->get('sort', 'recent');
        if ($sortBy === 'recent') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sortBy === 'name') {
            $query->orderBy('name', 'asc');
        } elseif ($sortBy === 'signup_start') {
            $query->orderBy('signup_start', 'asc');
        }

        $myTournaments = $query->get();

        $activeTournamentsCount = Tournament::where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
                ->orWhereHas('tournamentRoleLinks', function ($roleQuery) use ($user) {
                    $roleQuery->where('user_id', $user->id);
                });
        })
            ->whereIn('status', ['draft', 'published', 'ongoing'])
            ->count();

        $totalTournamentsCount = Tournament::where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
                ->orWhereHas('tournamentRoleLinks', function ($roleQuery) use ($user) {
                    $roleQuery->where('user_id', $user->id);
                })
                ->orWhereHas('registeredPlayers', function ($playerQuery) use ($user) {
                    $playerQuery->where('user_id', $user->id);
                });
        })->count();

        return view('dashboard.index', compact('myTournaments', 'activeTournamentsCount', 'totalTournamentsCount'));
    }
}
