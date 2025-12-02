<?php

namespace App\Services;

use App\Models\MatchModel;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentPlayer;

class BracketGenerationService
{
    /**
     * Check if tournament is ready for bracket generation.
     */
    public function canGenerateBracket(Tournament $tournament): array
    {
        $errors = [];

        // Check if bracket already exists
        if ($tournament->matches()->exists()) {
            $errors[] = 'Bracket has already been generated. Delete existing matches first.';
        }

        // Check if tournament has qualifiers
        if ($tournament->has_qualifiers) {
            // Check if qualifiers are finished
            // This would need to check if all qualifier slots are completed
            // For now, we'll assume qualifiers are tracked via reservations
            $totalSlots = $tournament->qualifiersSlots()->count();
            if ($totalSlots === 0) {
                $errors[] = 'No qualifier slots have been created yet.';
            }

            // Check if all slots have been completed (you may need to adjust this logic)
            $completedReservations = $tournament->qualifiersReservations()
                ->where('status', 'completed')
                ->count();

            $pendingReservations = $tournament->qualifiersReservations()
                ->where('status', 'pending')
                ->count();

            if ($pendingReservations > 0) {
                $errors[] = 'Qualifiers have not finished yet. There are still pending qualifier matches.';
            }
        }

        // Check if tournament has enough players/teams
        $participantCount = $this->getParticipantCount($tournament);
        if ($participantCount < 2) {
            $errors[] = 'Not enough participants. At least 2 participants are required.';
        }

        // Check if bracket size can accommodate participants
        if ($participantCount > $tournament->bracket_size) {
            $errors[] = "Too many participants ({$participantCount}) for bracket size ({$tournament->bracket_size}). Increase bracket size or reduce participants.";
        }

        return [
            'can_generate' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Generate bracket for the tournament.
     */
    public function generateBracket(Tournament $tournament, ?array $seedingOrder = null): bool
    {
        // Get seeded participants
        $participants = $this->getSeedededParticipants($tournament, $seedingOrder);

        if ($participants->isEmpty()) {
            return false;
        }

        // Generate matches based on elimination type
        switch ($tournament->elim_type) {
            case 'single':
                return $this->generateSingleEliminationBracket($tournament, $participants);
            case 'double':
                return $this->generateDoubleEliminationBracket($tournament, $participants);
            case 'caterpillar':
                return $this->generateCaterpillarBracket($tournament, $participants);
            default:
                return false;
        }
    }

    /**
     * Get participants ordered by seeding.
     */
    protected function getSeedededParticipants(Tournament $tournament, ?array $seedingOrder = null)
    {
        $isTeamTournament = $tournament->isTeamTournament();

        if ($isTeamTournament) {
            $query = Team::where('tournament_id', $tournament->id);
        } else {
            $query = TournamentPlayer::where('tournament_id', $tournament->id)
                ->with('user');
        }

        // If custom seeding order is provided, use it
        if ($seedingOrder) {
            $orderedIds = array_keys($seedingOrder);

            if ($isTeamTournament) {
                return Team::whereIn('id', $orderedIds)
                    ->orderByRaw('FIELD(id, '.implode(',', $orderedIds).')')
                    ->get();
            } else {
                return TournamentPlayer::whereIn('id', $orderedIds)
                    ->with('user')
                    ->orderByRaw('FIELD(id, '.implode(',', $orderedIds).')')
                    ->get();
            }
        }

        // Otherwise, use seeding_type
        switch ($tournament->seeding_type) {
            case 'rank':
                // Order by player rank (lowest rank number = highest seed)
                if ($isTeamTournament) {
                    // For teams, you might want to calculate average rank
                    return $query->get()->sortBy(function ($team) {
                        $avgRank = $team->members->avg(fn ($member) => $member->user->rank ?? 999999);

                        return $avgRank;
                    })->values();
                } else {
                    return $query->get()->sortBy(fn ($p) => $p->user->rank ?? 999999)->values();
                }

            case 'avg_score':
            case 'mp_percent':
            case 'points':
                // These seeding types use qualifier results
                // Order participants by their qualifier performance (descending)
                // TODO: Implement proper qualifier result calculation
                // For now, return by rank as fallback
                if ($isTeamTournament) {
                    return $query->get()->sortBy(function ($team) {
                        $avgRank = $team->members->avg(fn ($member) => $member->user->rank ?? 999999);

                        return $avgRank;
                    })->values();
                } else {
                    return $query->get()->sortBy(fn ($p) => $p->user->rank ?? 999999)->values();
                }

            case 'drawing':
                // Random seeding
                return $query->get()->shuffle();

            case 'custom':
            default:
                // Custom or unknown - return unsorted, will need manual ordering
                return $query->get();
        }
    }

    /**
     * Generate single elimination bracket.
     */
    protected function generateSingleEliminationBracket(Tournament $tournament, $participants): bool
    {
        $bracketSize = $tournament->bracket_size;
        $participantCount = $participants->count();

        // Calculate number of rounds needed
        $rounds = log($bracketSize, 2);

        // Determine if we need a play-in round
        $needsPlayIn = $participantCount > ($bracketSize / 2);

        $matchNumber = 1;
        $isTeamTournament = $tournament->isTeamTournament();

        // Generate Round 1 matches
        $round = 1;
        $pairings = $this->generateSingleElimPairings($participants, $bracketSize);

        foreach ($pairings as $pairing) {
            MatchModel::create([
                'tournament_id' => $tournament->id,
                'team1_id' => $isTeamTournament ? ($pairing['participant1']->id ?? null) : ($pairing['participant1']->user_id ?? null),
                'team2_id' => $isTeamTournament ? ($pairing['participant2']->id ?? null) : ($pairing['participant2']->user_id ?? null),
                'team1_seed' => $pairing['seed1'],
                'team2_seed' => $pairing['seed2'],
                'round' => $round,
                'stage' => 'bracket',
                'status' => 'pending',
            ]);
        }

        // Generate placeholder matches for subsequent rounds
        $currentRoundMatches = count($pairings);
        for ($r = 2; $r <= $rounds; $r++) {
            $nextRoundMatches = $currentRoundMatches / 2;
            for ($m = 0; $m < $nextRoundMatches; $m++) {
                MatchModel::create([
                    'tournament_id' => $tournament->id,
                    'round' => $r,
                    'stage' => 'bracket',
                    'status' => 'pending',
                ]);
            }
            $currentRoundMatches = $nextRoundMatches;
        }

        return true;
    }

    /**
     * Generate double elimination bracket.
     */
    protected function generateDoubleEliminationBracket(Tournament $tournament, $participants): bool
    {
        // Similar to single elimination but with losers bracket
        // This is a simplified version - full implementation would be more complex
        $this->generateSingleEliminationBracket($tournament, $participants);

        return true;
    }

    /**
     * Generate caterpillar bracket.
     */
    protected function generateCaterpillarBracket(Tournament $tournament, $participants): bool
    {
        // Caterpillar format implementation
        // This would need specific logic for caterpillar brackets
        return $this->generateSingleEliminationBracket($tournament, $participants);
    }

    /**
     * Generate pairings for single elimination based on seeding.
     */
    protected function generateSingleElimPairings($participants, int $bracketSize): array
    {
        $count = $participants->count();
        $pairings = [];

        // Standard single elimination seeding (1 vs bracket_size, 2 vs bracket_size-1, etc.)
        $seeds = range(1, $bracketSize);
        $matchups = $this->getSingleElimMatchups($bracketSize);

        foreach ($matchups as $matchup) {
            $seed1 = $matchup[0];
            $seed2 = $matchup[1];

            // Get participant for seed (or null if bye)
            $p1 = $seed1 <= $count ? $participants[$seed1 - 1] : null;
            $p2 = $seed2 <= $count ? $participants[$seed2 - 1] : null;

            if ($p1 || $p2) {
                $pairings[] = [
                    'participant1' => $p1,
                    'participant2' => $p2,
                    'seed1' => $seed1,
                    'seed2' => $seed2,
                ];
            }
        }

        return $pairings;
    }

    /**
     * Get standard single elimination matchups for first round.
     * Uses proper tournament seeding to ensure top seeds meet in finals.
     */
    protected function getSingleElimMatchups(int $bracketSize): array
    {
        // Proper tournament seeding ensures:
        // - Seed 1 and 2 are in opposite halves (can only meet in finals)
        // - Top seeds are distributed optimally throughout the bracket
        //
        // For 8: 1v8, 4v5, 2v7, 3v6
        // For 16: 1v16, 8v9, 4v13, 5v12, 2v15, 7v10, 3v14, 6v11
        // For 32: Similar pattern extended

        $matchups = [];
        $seeds = range(1, $bracketSize);

        // Generate proper bracket seeding using recursive pairing
        $orderedSeeds = $this->generateBracketSeeding($bracketSize);

        // Pair adjacent seeds in the ordered list
        for ($i = 0; $i < count($orderedSeeds); $i += 2) {
            $matchups[] = [$orderedSeeds[$i], $orderedSeeds[$i + 1]];
        }

        return $matchups;
    }

    /**
     * Generate proper bracket seeding order recursively.
     * This ensures top seeds are optimally distributed.
     */
    protected function generateBracketSeeding(int $bracketSize): array
    {
        if ($bracketSize === 2) {
            return [1, 2];
        }

        // Recursively build the seeding
        $previousRound = $this->generateBracketSeeding($bracketSize / 2);
        $currentRound = [];

        foreach ($previousRound as $seed) {
            $currentRound[] = $seed;
            $currentRound[] = $bracketSize + 1 - $seed;
        }

        return $currentRound;
    }

    /**
     * Get participant count for tournament.
     */
    protected function getParticipantCount(Tournament $tournament): int
    {
        if ($tournament->isTeamTournament()) {
            return $tournament->teams()->count();
        } else {
            return $tournament->registeredPlayers()->count();
        }
    }

    /**
     * Check if custom seeding is needed.
     */
    public function needsCustomSeeding(Tournament $tournament): bool
    {
        return $tournament->seeding_type === 'custom';
    }
}
