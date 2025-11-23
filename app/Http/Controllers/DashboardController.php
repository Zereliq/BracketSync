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

        // Get staff tournaments (created by user or user has a staff role)
        $staffTournaments = Tournament::query()
            ->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                    ->orWhereHas('tournamentRoleLinks', function ($roleQuery) use ($user) {
                        $roleQuery->where('user_id', $user->id);
                    });
            })
            ->where(function ($q) use ($user) {
                // For archived and draft tournaments, only show if user has a staff role
                $q->whereNotIn('status', ['archived', 'draft'])
                    ->orWhere(function ($subQuery) use ($user) {
                        $subQuery->whereIn('status', ['archived', 'draft'])
                            ->where(function ($roleCheck) use ($user) {
                                $roleCheck->where('created_by', $user->id)
                                    ->orWhereHas('tournamentRoleLinks', function ($roleQuery) use ($user) {
                                        $roleQuery->where('user_id', $user->id);
                                    });
                            });
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Get player tournaments (user is registered as a player)
        $playerTournaments = Tournament::query()
            ->whereHas('registeredPlayers', function ($playerQuery) use ($user) {
                $playerQuery->where('user_id', $user->id);
            })
            ->whereNotIn('status', ['archived', 'draft'])
            ->orderBy('created_at', 'desc')
            ->get();

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

        return view('dashboard.index', compact('staffTournaments', 'playerTournaments', 'activeTournamentsCount', 'totalTournamentsCount'));
    }
}
