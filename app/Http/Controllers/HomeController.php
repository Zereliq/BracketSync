<?php

namespace App\Http\Controllers;

use App\Models\MatchModel;
use App\Models\Tournament;
use App\Models\TournamentPlayer;

class HomeController extends Controller
{
    public function index(): mixed
    {
        // Get most liked tournament for hero section
        $featuredTournament = Tournament::query()
            ->whereNotIn('status', ['draft', 'archived'])
            ->withCount('likes')
            ->orderBy('likes_count', 'desc')
            ->first();

        // Get active/registration tournaments for grid (6 tournaments)
        $tournaments = Tournament::query()
            ->whereNotIn('status', ['draft', 'archived'])
            ->withCount('likes', 'registeredPlayers', 'teams')
            ->orderBy('status', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // Get upcoming matches (5 matches)
        $upcomingMatches = MatchModel::query()
            ->with(['tournament', 'team1', 'team2'])
            ->where('status', 'scheduled')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at', 'asc')
            ->limit(5)
            ->get();

        // Calculate statistics
        $stats = [
            'activeTournaments' => Tournament::whereIn('status', ['announced', 'ongoing'])->count(),
            'registeredPlayers' => TournamentPlayer::distinct('user_id')->count(),
            'completedMatches' => MatchModel::where('status', 'completed')->count(),
            'gameModes' => 4, // osu!, taiko, catch, mania
        ];

        return view('homepage', compact(
            'featuredTournament',
            'tournaments',
            'upcomingMatches',
            'stats'
        ));
    }
}
