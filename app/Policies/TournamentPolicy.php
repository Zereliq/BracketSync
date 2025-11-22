<?php

namespace App\Policies;

use App\Models\Tournament;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\DB;

class TournamentPolicy
{
    /**
     * Everyone who is logged in can view their dashboard tournaments list.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Creation allowed if user has < 5 active tournaments.
     */
    public function create(User $user): Response
    {
        $activeCount = Tournament::where('created_by', $user->id)
            ->whereIn('status', ['draft', 'published', 'ongoing'])
            ->count();

        return $activeCount < 5
            ? Response::allow()
            : Response::deny('You already have 5 active tournaments. Finish or archive one before creating another.');
    }

    /**
     * Update allowed if creator OR has allowed tournament role.
     */
    public function update(User $user, Tournament $tournament): Response
    {
        if ($tournament->created_by === $user->id) {
            return Response::allow();
        }

        if ($this->hasAllowedTournamentRole($user, $tournament)) {
            return Response::allow();
        }

        return Response::deny('You are not authorized to manage this tournament.');
    }

    /**
     * Edit uses same rules as update.
     */
    public function edit(User $user, Tournament $tournament): Response
    {
        return $this->update($user, $tournament);
    }

    /**
     * Delete uses same rules as update.
     */
    public function delete(User $user, Tournament $tournament): Response
    {
        if ($tournament->created_by === $user->id) {
            return Response::allow();
        }
    }

    /**
     * Optional: view specific tournament in dashboard.
     */
    public function view(User $user, Tournament $tournament): Response
    {
        // Let creator or staff view
        return $this->update($user, $tournament);
    }

    /**
     * Checks if user has an allowed staff role in this tournament.
     */
    protected function hasAllowedTournamentRole(User $user, Tournament $tournament): bool
    {
        $allowedRoles = [
            'creator',
            'organization',
            'organizer',
            'pooler',
            'referee',
            'streamer',
            'commentator',
            'designer',
            'developer',
        ];

        return DB::table('tournaments_roles_users')
            ->join('tournamentroles', 'tournamentroles.id', '=', 'tournaments_roles_users.role_id')
            ->where('tournaments_roles_users.user_id', $user->id)
            ->where('tournaments_roles_users.tournament_id', $tournament->id)
            ->whereIn('tournamentroles.name', $allowedRoles)
            ->exists();
    }
}
