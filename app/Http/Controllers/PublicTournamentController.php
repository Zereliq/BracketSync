<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use Illuminate\Http\Request;

class PublicTournamentController extends Controller
{
    /**
     * Display all tournaments with filtering.
     */
    public function index(Request $request): mixed
    {
        $query = Tournament::query()
            ->whereNotIn('status', ['draft', 'finished', 'archived'])
            ->withCount('likes');

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

        // Filter by "my tournaments" - tournaments where user has a role
        if ($request->filled('my_tournaments') && $request->my_tournaments === 'true' && auth()->check()) {
            $query->whereHas('tournamentRoleLinks', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }

        // Filter by liked tournaments
        if ($request->filled('liked') && $request->liked === 'true' && auth()->check()) {
            $query->whereHas('likes', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }

        // Sort by likes count (default) or other options
        $sortBy = $request->get('sort', 'likes');
        if ($sortBy === 'likes') {
            $query->orderBy('likes_count', 'desc');
        } elseif ($sortBy === 'newest') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sortBy === 'signup_start') {
            $query->orderBy('signup_start', 'asc');
        }

        $tournaments = $query->get();

        // Check which tournaments the user has liked
        $likedTournamentIds = auth()->check()
            ? auth()->user()->likedTournaments()->pluck('tournament_id')->toArray()
            : [];

        return view('tournaments.index', [
            'tournaments' => $tournaments,
            'likedTournamentIds' => $likedTournamentIds,
        ]);
    }

    /**
     * Like a tournament.
     */
    public function like(Tournament $tournament): mixed
    {
        if (! auth()->check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $user = auth()->user();

        if ($user->likedTournaments()->where('tournament_id', $tournament->id)->exists()) {
            return response()->json(['message' => 'Already liked'], 400);
        }

        $user->likedTournaments()->attach($tournament->id);

        return response()->json([
            'message' => 'Tournament liked',
            'likes_count' => $tournament->likes()->count(),
        ]);
    }

    /**
     * Unlike a tournament.
     */
    public function unlike(Tournament $tournament): mixed
    {
        if (! auth()->check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $user = auth()->user();

        if (! $user->likedTournaments()->where('tournament_id', $tournament->id)->exists()) {
            return response()->json(['message' => 'Not liked'], 400);
        }

        $user->likedTournaments()->detach($tournament->id);

        return response()->json([
            'message' => 'Tournament unliked',
            'likes_count' => $tournament->likes()->count(),
        ]);
    }

    /**
     * Display the tournament information tab (public).
     */
    public function show(Tournament $tournament): mixed
    {
        $tournament->load('creator');

        return view('tournaments.show', [
            'tournament' => $tournament,
            'currentTab' => 'tournament',
        ]);
    }

    /**
     * Display the tournament staff tab (public).
     */
    public function staff(Tournament $tournament): mixed
    {
        $tournament->load([
            'tournamentRoleLinks.user',
            'tournamentRoleLinks.role',
        ]);

        $staffByRole = $tournament->tournamentRoleLinks->groupBy('role.name');

        return view('tournaments.show', [
            'tournament' => $tournament,
            'currentTab' => 'staff',
            'staffByRole' => $staffByRole,
        ]);
    }

    /**
     * Display the tournament players tab (public).
     */
    public function players(Tournament $tournament): mixed
    {
        $tournament->load([
            'teams.members',
        ]);

        return view('tournaments.show', [
            'tournament' => $tournament,
            'currentTab' => 'players',
            'teams' => $tournament->teams,
        ]);
    }

    /**
     * Display the tournament teams tab (public).
     */
    public function teams(Tournament $tournament): mixed
    {
        $tournament->load([
            'teams.members',
        ]);

        return view('tournaments.show', [
            'tournament' => $tournament,
            'currentTab' => 'teams',
            'teams' => $tournament->teams,
            'isTeamTournament' => $tournament->isTeamTournament(),
        ]);
    }

    /**
     * Display the tournament qualifiers tab (public).
     */
    public function qualifiers(Tournament $tournament): mixed
    {
        return view('tournaments.show', [
            'tournament' => $tournament,
            'currentTab' => 'qualifiers',
        ]);
    }

    /**
     * Display the tournament matches tab (public).
     */
    public function matches(Tournament $tournament): mixed
    {
        $tournament->load([
            'matches.team1',
            'matches.team2',
            'matches.winner',
            'matches.referee',
            'matches.mappool',
            'matches.games',
            'matches.rolls.team',
        ]);

        // Get filter parameters
        $selectedRound = request()->query('round');
        $myMatches = request()->query('my_matches') === 'true';

        $matches = $tournament->matches;

        // Filter by user's matches (as player/team member or referee)
        if ($myMatches && auth()->check()) {
            $user = auth()->user();
            $matches = $matches->filter(function ($match) use ($user, $tournament) {
                // Check if user is the referee
                if ($match->referee_id === $user->id) {
                    return true;
                }

                // Check if user is in team1 or team2
                if ($tournament->isTeamTournament()) {
                    $userTeams = $user->teams()->where('tournament_id', $tournament->id)->pluck('id');

                    return $userTeams->contains($match->team1_id) || $userTeams->contains($match->team2_id);
                } else {
                    // For 1v1 tournaments, check if user is team1 or team2
                    return $match->team1_id === $user->id || $match->team2_id === $user->id;
                }
            });
        }

        // Filter by selected round
        if ($selectedRound !== null) {
            $matches = $matches->where('round', (int) $selectedRound);
        }

        // Group matches by round and determine round names
        $matchesByRound = $tournament->matches->groupBy('round')->sortKeys();
        $totalRounds = $matchesByRound->count();
        $rounds = [];

        foreach ($matchesByRound as $roundNumber => $roundMatches) {
            $rounds[] = [
                'number' => $roundNumber,
                'name' => $this->getRoundName($roundNumber, $totalRounds, $tournament->bracket_size),
                'count' => $roundMatches->count(),
            ];
        }

        return view('tournaments.show', [
            'tournament' => $tournament,
            'currentTab' => 'matches',
            'matches' => $matches,
            'rounds' => $rounds,
            'selectedRound' => $selectedRound,
            'myMatches' => $myMatches,
        ]);
    }

    /**
     * Get the display name for a round based on bracket size.
     */
    protected function getRoundName(int $round, int $totalRounds, int $bracketSize): string
    {
        $roundsFromEnd = $totalRounds - $round + 1;

        return match ($roundsFromEnd) {
            1 => 'Finals',
            2 => 'Semi-Finals',
            3 => 'Quarter-Finals',
            4 => 'Round of 16',
            5 => 'Round of 32',
            6 => 'Round of 64',
            7 => 'Round of 128',
            default => "Round {$round}",
        };
    }

    /**
     * Display the tournament bracket tab (public).
     */
    public function bracket(Tournament $tournament): mixed
    {
        return view('tournaments.show', [
            'tournament' => $tournament,
            'currentTab' => 'bracket',
        ]);
    }

    /**
     * Display the tournament mappools tab (public).
     */
    public function mappools(Tournament $tournament): mixed
    {
        $tournament->load([
            'mappools.maps',
        ]);

        return view('tournaments.show', [
            'tournament' => $tournament,
            'currentTab' => 'mappools',
            'mappools' => $tournament->mappools,
        ]);
    }
}
