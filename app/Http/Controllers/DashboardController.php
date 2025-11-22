<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard home page.
     */
    public function index(): mixed
    {
        $user = auth()->user();

        $myTournaments = Tournament::where('created_by', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $activeTournamentsCount = Tournament::where('created_by', $user->id)
            ->whereIn('status', ['draft', 'published', 'ongoing'])
            ->count();

        $totalTournamentsCount = Tournament::where('created_by', $user->id)->count();

        return view('dashboard.index', compact('myTournaments', 'activeTournamentsCount', 'totalTournamentsCount'));
    }
}
