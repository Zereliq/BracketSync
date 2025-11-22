<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * Display a listing of teams.
     */
    public function index(): mixed
    {
        $teams = Team::with(['tournament', 'members'])
            ->latest()
            ->paginate(15);

        return view('dashboard.teams.index', compact('teams'));
    }

    /**
     * Display the specified team.
     */
    public function show(Team $team): mixed
    {
        $team->load(['tournament', 'members']);

        return view('dashboard.teams.show', compact('team'));
    }
}
