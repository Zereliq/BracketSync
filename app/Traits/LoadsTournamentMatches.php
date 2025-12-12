<?php

namespace App\Traits;

use App\Models\Tournament;

trait LoadsTournamentMatches
{
    /**
     * Load and filter tournament matches.
     */
    protected function loadTournamentMatches(Tournament $tournament): array
    {
        $tournament->load([
            'matches.team1',
            'matches.team2',
            'matches.player1',
            'matches.player2',
            'matches.winner',
            'matches.noShowTeam',
            'matches.referee',
            'matches.mappool',
            'matches.scores',
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

        return [
            'matches' => $matches,
            'rounds' => $rounds,
            'selectedRound' => $selectedRound,
            'myMatches' => $myMatches,
        ];
    }

    /**
     * Get the display name for a round based on bracket size.
     */
    protected function getRoundName(int $round, int $totalRounds, ?int $bracketSize): string
    {
        if ($bracketSize === null) {
            return "Round {$round}";
        }

        $roundsFromEnd = $totalRounds - $round;

        return match ($roundsFromEnd) {
            0 => 'Finals',
            1 => 'Semi-Finals',
            2 => 'Quarter-Finals',
            default => 'Round of '.($bracketSize / pow(2, $roundsFromEnd)),
        };
    }
}
