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

    /**
     * Check if user can view a specific resource.
     */
    public function viewResource(User $user, Tournament $tournament, string $resource): Response
    {
        if ($tournament->userHasPermission($user, $resource, 'view')) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view this section.');
    }

    /**
     * Check if user can edit a specific resource.
     */
    public function editResource(User $user, Tournament $tournament, string $resource): Response
    {
        if ($tournament->userHasPermission($user, $resource, 'edit')) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to edit this section.');
    }

    /**
     * Tournament settings tab.
     */
    public function viewTournament(User $user, Tournament $tournament): Response
    {
        return $this->viewResource($user, $tournament, 'tournament');
    }

    public function editTournament(User $user, Tournament $tournament): Response
    {
        return $this->editResource($user, $tournament, 'tournament');
    }

    /**
     * Staff management tab.
     */
    public function viewStaff(User $user, Tournament $tournament): Response
    {
        return $this->viewResource($user, $tournament, 'staff');
    }

    public function editStaff(User $user, Tournament $tournament): Response
    {
        return $this->editResource($user, $tournament, 'staff');
    }

    /**
     * Players tab.
     */
    public function viewPlayers(User $user, Tournament $tournament): Response
    {
        return $this->viewResource($user, $tournament, 'players');
    }

    public function editPlayers(User $user, Tournament $tournament): Response
    {
        return $this->editResource($user, $tournament, 'players');
    }

    /**
     * Teams tab.
     */
    public function viewTeams(User $user, Tournament $tournament): Response
    {
        return $this->viewResource($user, $tournament, 'teams');
    }

    public function editTeams(User $user, Tournament $tournament): Response
    {
        return $this->editResource($user, $tournament, 'teams');
    }

    /**
     * Qualifiers tab.
     */
    public function viewQualifiers(User $user, Tournament $tournament): Response
    {
        return $this->viewResource($user, $tournament, 'qualifiers');
    }

    public function editQualifiers(User $user, Tournament $tournament): Response
    {
        return $this->editResource($user, $tournament, 'qualifiers');
    }

    /**
     * Matches tab.
     */
    public function viewMatches(User $user, Tournament $tournament): Response
    {
        return $this->viewResource($user, $tournament, 'matches');
    }

    public function editMatches(User $user, Tournament $tournament): Response
    {
        return $this->editResource($user, $tournament, 'matches');
    }

    /**
     * Bracket tab.
     */
    public function viewBracket(User $user, Tournament $tournament): Response
    {
        return $this->viewResource($user, $tournament, 'bracket');
    }

    public function editBracket(User $user, Tournament $tournament): Response
    {
        return $this->editResource($user, $tournament, 'bracket');
    }

    /**
     * Mappools tab.
     */
    public function viewMappools(User $user, Tournament $tournament): Response
    {
        return $this->viewResource($user, $tournament, 'mappools');
    }

    public function editMappools(User $user, Tournament $tournament): Response
    {
        return $this->editResource($user, $tournament, 'mappools');
    }
}
