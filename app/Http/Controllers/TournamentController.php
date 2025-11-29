<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTournamentRequest;
use App\Http\Requests\UpdateTournamentRequest;
use App\Models\Tournament;
use App\Models\TournamentRoleUser;
use App\Models\User;
use App\Services\TournamentRoleService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;

class TournamentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of tournaments created by the authenticated user.
     */
    public function index(): mixed
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

        return view('dashboard.tournaments.index', compact('staffTournaments', 'playerTournaments'));
    }

    /**
     * Show the form for creating a new tournament.
     */
    public function create(): mixed
    {
        $response = Gate::inspect('create', Tournament::class);

        if ($response->denied()) {
            return redirect()
                ->route('dashboard.tournaments.index')
                ->with('error', $response->message());
        }

        return view('dashboard.tournaments.create');
    }

    /**
     * Store a newly created tournament in storage.
     */
    public function store(StoreTournamentRequest $request): mixed
    {
        $response = Gate::inspect('create', Tournament::class);

        if ($response->denied()) {
            return redirect()
                ->route('dashboard.tournaments.index')
                ->with('error', $response->message());
        }

        $validated = $request->validated();

        // Process country_list_input into array
        if ($request->has('country_list_input') && $request->country_list_input) {
            $validated['country_list'] = array_map(
                'trim',
                array_map(
                    'strtoupper',
                    explode(',', $request->country_list_input)
                )
            );
        } else {
            $validated['country_list'] = null;
        }

        $tournament = Tournament::create([
            ...$validated,
            'created_by' => auth()->id(),
            'status' => $validated['status'] ?? 'draft',
        ]);

        // Create standard roles for the tournament
        $this->createStandardRoles($tournament);

        return redirect()
            ->route('dashboard.tournaments.show', $tournament)
            ->with('success', 'Tournament created successfully!');
    }

    /**
     * Display the tournament (main tournament tab).
     */
    public function show(Tournament $tournament): mixed
    {
        $response = Gate::inspect('viewTournament', $tournament);

        if ($response->denied()) {
            return redirect()
                ->route('dashboard.tournaments.index')
                ->with('error', $response->message());
        }

        $tournament->load('creator');

        return view('tournaments.show', [
            'tournament' => $tournament,
            'currentTab' => 'tournament',
            'isDashboard' => true,
        ]);
    }

    /**
     * Display the tournament bracket tab.
     */
    public function bracket(Tournament $tournament): mixed
    {
        $response = Gate::inspect('viewBracket', $tournament);

        if ($response->denied()) {
            return redirect()
                ->route('dashboard.tournaments.index')
                ->with('error', $response->message());
        }

        return view('tournaments.show', [
            'tournament' => $tournament,
            'currentTab' => 'bracket',
            'isDashboard' => true,
        ]);
    }

    /**
     * Publish the tournament (set status to announced).
     */
    public function publish(Tournament $tournament): mixed
    {
        if (! $tournament->canManageStaff()) {
            return redirect()
                ->route('dashboard.tournaments.show', $tournament)
                ->with('error', 'Only hosts and organizers can publish tournaments.');
        }

        if ($tournament->status === 'announced') {
            return redirect()
                ->route('dashboard.tournaments.show', $tournament)
                ->with('error', 'This tournament is already published.');
        }

        $tournament->update(['status' => 'announced']);

        return redirect()
            ->route('dashboard.tournaments.show', $tournament)
            ->with('success', 'Tournament has been published successfully!');
    }

    /**
     * Update the specified tournament in storage.
     */
    public function update(UpdateTournamentRequest $request, Tournament $tournament): mixed
    {
        $response = Gate::inspect('editTournament', $tournament);

        if ($response->denied()) {
            return redirect()
                ->route('dashboard.tournaments.index')
                ->with('error', $response->message());
        }

        $validated = $request->validated();

        // Process country_list_input into array
        if ($request->has('country_list_input') && $request->country_list_input) {
            $validated['country_list'] = array_map(
                'trim',
                array_map(
                    'strtoupper',
                    explode(',', $request->country_list_input)
                )
            );
        } else {
            $validated['country_list'] = null;
        }

        $tournament->update($validated);

        return redirect()
            ->route('dashboard.tournaments.show', $tournament)
            ->with('success', 'Tournament updated successfully!');
    }

    /**
     * Remove the specified tournament from storage.
     */
    public function destroy(Tournament $tournament): mixed
    {
        $response = Gate::inspect('delete', $tournament);

        if ($response->denied()) {
            return redirect()
                ->route('dashboard.tournaments.index')
                ->with('error', $response->message());
        }

        $tournament->delete();

        return redirect()
            ->route('dashboard.tournaments.index')
            ->with('success', 'Tournament deleted successfully.');
    }

    /**
     * Display the tournament staff tab (dashboard context).
     */
    public function staff(Tournament $tournament): mixed
    {
        $response = Gate::inspect('viewStaff', $tournament);

        if ($response->denied()) {
            return redirect()
                ->route('dashboard.tournaments.index')
                ->with('error', $response->message());
        }

        $tournament->load([
            'tournamentRoleLinks.user',
            'tournamentRoleLinks.role',
        ]);

        $staffByRole = $tournament->tournamentRoleLinks->sortBy('role.id')->groupBy('role.name');

        return view('tournaments.show', [
            'tournament' => $tournament,
            'currentTab' => 'staff',
            'staffByRole' => $staffByRole,
            'isDashboard' => true,
        ]);
    }

    /**
     * Display the tournament players tab (dashboard context).
     */
    public function players(Tournament $tournament): mixed
    {
        $response = Gate::inspect('viewPlayers', $tournament);

        if ($response->denied()) {
            return redirect()
                ->route('dashboard.tournaments.index')
                ->with('error', $response->message());
        }

        $tournament->load([
            'teams.members',
        ]);

        return view('tournaments.show', [
            'tournament' => $tournament,
            'currentTab' => 'players',
            'teams' => $tournament->teams,
            'isDashboard' => true,
        ]);
    }

    /**
     * Display the tournament matches tab (dashboard context).
     */
    public function matches(Tournament $tournament): mixed
    {
        $response = Gate::inspect('viewMatches', $tournament);

        if ($response->denied()) {
            return redirect()
                ->route('dashboard.tournaments.index')
                ->with('error', $response->message());
        }

        $tournament->load([
            'matches.teams',
            'matches.referee',
        ]);

        return view('tournaments.show', [
            'tournament' => $tournament,
            'currentTab' => 'matches',
            'matches' => $tournament->matches,
            'isDashboard' => true,
        ]);
    }

    /**
     * Display the tournament teams tab (dashboard context).
     */
    public function teams(Tournament $tournament): mixed
    {
        $response = Gate::inspect('viewTeams', $tournament);

        if ($response->denied()) {
            return redirect()
                ->route('dashboard.tournaments.index')
                ->with('error', $response->message());
        }

        $tournament->load([
            'teams.members',
            'registeredPlayers.user',
        ]);

        // Get players not on any team yet
        $playersWithoutTeam = $tournament->registeredPlayers->filter(function ($registration) use ($tournament) {
            return ! \App\Models\TeamUser::whereHas('team', function ($query) use ($tournament) {
                $query->where('tournament_id', $tournament->id);
            })->where('user_id', $registration->user_id)->exists();
        });

        return view('tournaments.show', [
            'tournament' => $tournament,
            'currentTab' => 'teams',
            'teams' => $tournament->teams,
            'playersWithoutTeam' => $playersWithoutTeam,
            'isTeamTournament' => $tournament->isTeamTournament(),
            'isDashboard' => true,
        ]);
    }

    /**
     * Display the tournament qualifiers tab (dashboard context).
     */
    public function qualifiers(Tournament $tournament): mixed
    {
        $response = Gate::inspect('viewQualifiers', $tournament);

        if ($response->denied()) {
            return redirect()
                ->route('dashboard.tournaments.index')
                ->with('error', $response->message());
        }

        return view('tournaments.show', [
            'tournament' => $tournament,
            'currentTab' => 'qualifiers',
            'isDashboard' => true,
        ]);
    }

    /**
     * Display the tournament mappools tab (dashboard context).
     */
    public function mappools(Tournament $tournament): mixed
    {
        $response = Gate::inspect('viewMappools', $tournament);

        if ($response->denied()) {
            return redirect()
                ->route('dashboard.tournaments.index')
                ->with('error', $response->message());
        }

        $tournament->load([
            'mappools.maps',
        ]);

        return view('tournaments.show', [
            'tournament' => $tournament,
            'currentTab' => 'mappools',
            'mappools' => $tournament->mappools,
            'isDashboard' => true,
        ]);
    }

    /**
     * Add a staff member to the tournament.
     */
    public function addStaff(Tournament $tournament): mixed
    {
        if (! $tournament->canManageStaff()) {
            return redirect()
                ->route('dashboard.tournaments.staff', $tournament)
                ->with('error', 'You do not have permission to manage staff for this tournament.');
        }

        $roles = $tournament->customRoles()->with('permissions')->get();

        return view('dashboard.tournaments.add-staff', [
            'tournament' => $tournament,
            'roles' => $roles,
        ]);
    }

    /**
     * Send a staff invitation to a user.
     */
    public function storeStaff(Tournament $tournament): mixed
    {
        if (! $tournament->canManageStaff()) {
            return redirect()
                ->route('dashboard.tournaments.staff', $tournament)
                ->with('error', 'You do not have permission to manage staff for this tournament.');
        }

        request()->validate([
            'osu_username' => 'required|string',
            'role_id' => 'required|exists:tournamentroles,id',
        ]);

        $user = \App\Models\User::where('name', request('osu_username'))->first();

        if (! $user) {
            return back()
                ->withInput()
                ->with('error', 'User not found. They must have logged in at least once.');
        }

        // Check if user already has this role
        $exists = TournamentRoleUser::where('tournament_id', $tournament->id)
            ->where('user_id', $user->id)
            ->where('role_id', request('role_id'))
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'This user already has this role in the tournament.');
        }

        // If inviting yourself, add directly to staff without invitation
        if ($user->id === auth()->id()) {
            TournamentRoleUser::create([
                'tournament_id' => $tournament->id,
                'user_id' => $user->id,
                'role_id' => request('role_id'),
            ]);

            return redirect()
                ->route('dashboard.tournaments.staff', $tournament)
                ->with('success', 'You have been added to the tournament staff!');
        }

        // Check if there's already a pending invitation
        $pendingInvitation = \App\Models\StaffInvitation::where('tournament_id', $tournament->id)
            ->where('user_id', $user->id)
            ->where('role_id', request('role_id'))
            ->where('status', 'pending')
            ->exists();

        if ($pendingInvitation) {
            return back()
                ->withInput()
                ->with('error', 'This user already has a pending invitation for this role.');
        }

        \App\Models\StaffInvitation::create([
            'tournament_id' => $tournament->id,
            'user_id' => $user->id,
            'role_id' => request('role_id'),
            'invited_by' => auth()->id(),
        ]);

        return redirect()
            ->route('dashboard.tournaments.staff', $tournament)
            ->with('success', 'Staff invitation sent successfully!');
    }

    /**
     * Remove a staff member from the tournament.
     */
    public function removeStaff(Tournament $tournament, TournamentRoleUser $staffMember): mixed
    {
        if (! $tournament->canManageStaff()) {
            return redirect()
                ->route('dashboard.tournaments.staff', $tournament)
                ->with('error', 'You do not have permission to manage staff for this tournament.');
        }

        if ($staffMember->tournament_id !== $tournament->id) {
            abort(404);
        }

        $staffMember->load('role');

        if ($staffMember->role->name === 'Host') {
            return redirect()
                ->route('dashboard.tournaments.staff', $tournament)
                ->with('error', 'Hosts cannot be removed from the tournament.');
        }

        if ($staffMember->role->name === 'Organizer' && ! $tournament->isHost()) {
            return redirect()
                ->route('dashboard.tournaments.staff', $tournament)
                ->with('error', 'Only hosts can remove organizers.');
        }

        $staffMember->delete();

        return redirect()
            ->route('dashboard.tournaments.staff', $tournament)
            ->with('success', 'Staff member removed successfully!');
    }

    /**
     * Create standard roles for a tournament with default permissions.
     */
    protected function createStandardRoles(Tournament $tournament): void
    {
        $standardRoles = TournamentRoleService::getStandardRoles();

        foreach ($standardRoles as $roleData) {
            $permissions = $roleData['permissions'];
            unset($roleData['permissions']);

            $role = $tournament->customRoles()->create($roleData);

            foreach ($permissions as $resource => $permission) {
                $role->permissions()->create([
                    'resource' => $resource,
                    'permission' => $permission,
                ]);
            }

            // Assign creator as Host
            if ($roleData['name'] === 'Host') {
                TournamentRoleUser::create([
                    'tournament_id' => $tournament->id,
                    'user_id' => auth()->id(),
                    'role_id' => $role->id,
                ]);
            }
        }
    }

    /**
     * Search for users by username (for autocomplete).
     */
    public function searchUsers(): mixed
    {
        $query = request('query');

        if (! $query || strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::where('name', 'like', $query.'%')
            ->orWhere('name', 'like', '%'.$query.'%')
            ->limit(10)
            ->get(['id', 'name', 'avatar_url']);

        return response()->json($users);
    }

    /**
     * Accept a staff invitation.
     */
    public function acceptStaffInvitation(\App\Models\StaffInvitation $invitation): mixed
    {
        if ($invitation->user_id !== auth()->id()) {
            return back()->with('error', 'This invitation is not for you.');
        }

        if (! $invitation->isPending()) {
            return back()->with('error', 'This invitation has already been processed.');
        }

        $invitation->accept();

        return back()->with('success', 'Staff invitation accepted! You are now part of the tournament staff.');
    }

    /**
     * Decline a staff invitation.
     */
    public function declineStaffInvitation(\App\Models\StaffInvitation $invitation): mixed
    {
        if ($invitation->user_id !== auth()->id()) {
            return back()->with('error', 'This invitation is not for you.');
        }

        if (! $invitation->isPending()) {
            return back()->with('error', 'This invitation has already been processed.');
        }

        $invitation->decline();

        return back()->with('success', 'Staff invitation declined.');
    }
}
