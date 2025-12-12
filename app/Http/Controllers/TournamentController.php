<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTournamentRequest;
use App\Http\Requests\UpdateTournamentRequest;
use App\Models\MatchModel;
use App\Models\Score;
use App\Models\Tournament;
use App\Models\TournamentRoleUser;
use App\Models\User;
use App\Services\BracketGenerationService;
use App\Services\OsuApiService;
use App\Services\TournamentRoleService;
use App\Traits\LoadsTournamentMatches;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TournamentController extends Controller
{
    use AuthorizesRequests;
    use LoadsTournamentMatches;

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

        $bracketService = new BracketGenerationService;
        $canGenerate = $bracketService->canGenerateBracket($tournament);

        return view('tournaments.show', [
            'tournament' => $tournament,
            'currentTab' => 'bracket',
            'isDashboard' => true,
            'canGenerateBracket' => $canGenerate['can_generate'],
            'generationErrors' => $canGenerate['errors'],
            'needsCustomSeeding' => $bracketService->needsCustomSeeding($tournament),
        ]);
    }

    /**
     * Generate the bracket for the tournament.
     */
    public function generateBracket(Tournament $tournament, BracketGenerationService $bracketService): mixed
    {
        $this->authorize('editBracket', $tournament);

        $validation = $bracketService->canGenerateBracket($tournament);

        if (! $validation['can_generate']) {
            return back()->with('error', implode(' ', $validation['errors']));
        }

        // Check if custom seeding is needed
        if ($bracketService->needsCustomSeeding($tournament)) {
            return redirect()->route('dashboard.tournaments.bracket.seeding', $tournament)
                ->with('info', 'Please configure the seeding order before generating the bracket.');
        }

        // Generate bracket
        $success = $bracketService->generateBracket($tournament);

        if ($success) {
            return redirect()->route('dashboard.tournaments.bracket', $tournament)
                ->with('success', 'Bracket generated successfully!');
        }

        return back()->with('error', 'Failed to generate bracket. Please try again.');
    }

    /**
     * Show the custom seeding configuration page.
     */
    public function showSeeding(Tournament $tournament): mixed
    {
        $this->authorize('editBracket', $tournament);

        $isTeamTournament = $tournament->isTeamTournament();

        if ($isTeamTournament) {
            $participants = $tournament->teams()->with('members')->get();
        } else {
            $participants = $tournament->registeredPlayers()->with('user')->get();
        }

        return view('dashboard.tournaments.seeding', [
            'tournament' => $tournament,
            'participants' => $participants,
            'isTeamTournament' => $isTeamTournament,
        ]);
    }

    /**
     * Generate bracket with custom seeding order.
     */
    public function generateBracketWithSeeding(Request $request, Tournament $tournament, BracketGenerationService $bracketService): mixed
    {
        $this->authorize('editBracket', $tournament);

        $request->validate([
            'seeding_order' => 'required|array',
            'seeding_order.*' => 'required|integer',
        ]);

        $seedingOrder = $request->input('seeding_order');

        // Generate bracket with custom seeding
        $success = $bracketService->generateBracket($tournament, $seedingOrder);

        if ($success) {
            return redirect()->route('dashboard.tournaments.bracket', $tournament)
                ->with('success', 'Bracket generated successfully with custom seeding!');
        }

        return back()->with('error', 'Failed to generate bracket. Please try again.');
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
     * Display the tournament settings tab (dashboard context).
     */
    public function settings(Tournament $tournament): mixed
    {
        $response = Gate::inspect('viewMatches', $tournament);

        if ($response->denied()) {
            return redirect()
                ->route('dashboard.tournaments.index')
                ->with('error', $response->message());
        }

        $tournament->load(['mappools']);

        $rounds = $tournament->matches()
            ->select('round', 'stage')
            ->groupBy('round', 'stage')
            ->orderBy('round')
            ->get()
            ->map(function ($match) use ($tournament) {
                $stageLabel = $match->stage === 'bracket' ? '' : ucfirst($match->stage);
                $roundMatches = $tournament->matches()
                    ->where('round', $match->round)
                    ->where('stage', $match->stage ?? 'bracket')
                    ->get();

                $firstMatch = $roundMatches->first();

                return [
                    'number' => $match->round,
                    'stage' => $match->stage ?? 'bracket',
                    'name' => $stageLabel ? "$stageLabel - Round {$match->round}" : "Round {$match->round}",
                    'count' => $roundMatches->count(),
                    'best_of' => $firstMatch?->best_of,
                    'mappool_id' => $firstMatch?->mappool_id,
                ];
            });

        return view('tournaments.show', [
            'tournament' => $tournament,
            'currentTab' => 'settings',
            'rounds' => $rounds,
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

        $matchData = $this->loadTournamentMatches($tournament);

        return view('tournaments.show', [
            'tournament' => $tournament,
            'currentTab' => 'matches',
            'isDashboard' => true,
            ...$matchData,
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
            'mappools.maps' => function ($query) {
                $query->orderBy('slot');
            },
            'mappools.matches',
        ]);

        // Determine the current mappool based on upcoming/in-progress matches
        $currentMappoolId = $tournament->matches()
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->whereNotNull('mappool_id')
            ->orderBy('scheduled_at')
            ->value('mappool_id');

        // Sort mappools: current first, then by ID
        $mappools = $tournament->mappools->sortBy(function ($mappool) use ($currentMappoolId) {
            return $mappool->id === $currentMappoolId ? 0 : 1;
        })->values();

        return view('tournaments.show', [
            'tournament' => $tournament,
            'currentTab' => 'mappools',
            'mappools' => $mappools,
            'currentMappoolId' => $currentMappoolId,
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

        // Check if a Host already exists
        $hasHost = TournamentRoleUser::where('tournament_id', $tournament->id)
            ->whereHas('role', function ($query) {
                $query->where('name', 'Host');
            })
            ->exists();

        return view('dashboard.tournaments.add-staff', [
            'tournament' => $tournament,
            'roles' => $roles,
            'hasHost' => $hasHost,
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

        // Get the role being assigned
        $role = \App\Models\TournamentRole::find(request('role_id'));

        // Check if trying to add a second Host
        if ($role && $role->name === 'Host') {
            $existingHost = TournamentRoleUser::where('tournament_id', $tournament->id)
                ->whereHas('role', function ($query) {
                    $query->where('name', 'Host');
                })
                ->exists();

            if ($existingHost) {
                return back()
                    ->withInput()
                    ->with('error', 'Only one Host can be assigned to a tournament. Remove the existing Host first if you want to assign a new one.');
            }
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
        $query = request('q') ?? request('query');

        if (! $query || strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::where(function ($q) use ($query) {
            $q->where('name', 'like', '%'.$query.'%')
                ->orWhere('osu_username', 'like', '%'.$query.'%');
        })
            ->limit(10)
            ->get(['id', 'name', 'osu_username', 'avatar_url', 'country_code']);

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

    /**
     * Update match settings for all matches in specified rounds.
     */
    public function updateRoundSettings(Request $request, Tournament $tournament): mixed
    {
        $this->authorize('editMatches', $tournament);

        $request->validate([
            'rounds' => 'required|array',
            'rounds.*.best_of' => 'nullable|integer|in:1,3,5,7,9,11,13',
            'rounds.*.mappool_id' => 'nullable|exists:mappools,id',
        ]);

        foreach ($request->rounds as $roundNumber => $settings) {
            MatchModel::where('tournament_id', $tournament->id)
                ->where('round', $roundNumber)
                ->update([
                    'best_of' => $settings['best_of'] ?: null,
                    'mappool_id' => $settings['mappool_id'] ?: null,
                ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Fetch match data from osu! API.
     */
    public function fetchOsuMatch(Request $request, Tournament $tournament, OsuApiService $osuApi): mixed
    {
        $this->authorize('editMatches', $tournament);

        $request->validate([
            'match_id' => 'required|integer',
            'osu_match_id' => 'required|integer',
        ]);

        $match = MatchModel::where('id', $request->match_id)
            ->where('tournament_id', $tournament->id)
            ->with(['team1.members', 'team2.members', 'player1', 'player2', 'mappool.maps.map'])
            ->firstOrFail();

        $osuMatchData = $osuApi->getMatch($request->osu_match_id);

        if (! $osuMatchData) {
            return response()->json([
                'error' => 'Failed to fetch match data from osu! API',
                'details' => 'Could not connect to osu! API or the match ID is invalid.',
            ], 400);
        }

        $validation = $this->validateMatchParticipants($osuMatchData, $match, $tournament);

        if (! $validation['valid']) {
            return response()->json([
                'error' => 'Match Participant Validation Failed',
                'details' => $validation['message'],
                'issues' => $validation['issues'],
            ], 422);
        }

        $mappoolBeatmapIds = collect();
        if ($match->mappool) {
            $mappoolBeatmapIds = $match->mappool->maps->pluck('map.osu_beatmap_id')->filter();
        }

        $processedGames = $this->processOsuMatchGames(
            $osuMatchData['events'] ?? [],
            $mappoolBeatmapIds,
            $match,
            $tournament
        );

        if (empty($processedGames['games'])) {
            return response()->json([
                'error' => 'No valid games found',
                'details' => 'No games were found in this match, or all games were incomplete.',
                'issues' => [
                    'Games with no scores are automatically excluded',
                ],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'match' => [
                'id' => $match->id,
                'team1' => $tournament->isTeamTournament() ? $match->team1 : $match->player1,
                'team2' => $tournament->isTeamTournament() ? $match->team2 : $match->player2,
                'best_of' => $match->best_of,
            ],
            'games' => $processedGames['games'],
            'team1_score' => $processedGames['team1_score'],
            'team2_score' => $processedGames['team2_score'],
            'winner_id' => $processedGames['winner_id'],
        ]);
    }

    /**
     * Validate that participants in the osu! match match the expected tournament participants.
     */
    private function validateMatchParticipants(array $osuMatchData, MatchModel $match, Tournament $tournament): array
    {
        $gameEvents = collect($osuMatchData['events'] ?? [])
            ->filter(fn ($event) => ($event['detail']['type'] ?? null) === 'other');

        if ($gameEvents->isEmpty()) {
            return [
                'valid' => false,
                'message' => 'No games were found in this osu! match.',
                'issues' => ['The match may not have started yet, or no games have been played.'],
            ];
        }

        $allPlayerIds = collect();
        foreach ($gameEvents as $event) {
            $game = $event['game'] ?? null;
            if (! $game) {
                continue;
            }

            $scores = collect($game['scores'] ?? []);
            $playerIds = $scores->pluck('user_id')->filter();
            $allPlayerIds = $allPlayerIds->merge($playerIds);
        }

        $allPlayerIds = $allPlayerIds->unique()->values();

        if ($allPlayerIds->isEmpty()) {
            return [
                'valid' => false,
                'message' => 'No players found in the osu! match data.',
                'issues' => ['The match data may be corrupted or incomplete.'],
            ];
        }

        $issues = [];
        $isTeamTournament = $tournament->isTeamTournament();

        if ($isTeamTournament) {
            if (! $match->team1 || ! $match->team2) {
                return [
                    'valid' => false,
                    'message' => 'Match participants are not yet determined.',
                    'issues' => ['Both teams must be set before entering results.'],
                ];
            }

            $team1Members = $match->team1->members->pluck('osu_id')->toArray();
            $team2Members = $match->team2->members->pluck('osu_id')->toArray();
            $expectedPlayers = array_merge($team1Members, $team2Members);

            $team1PlayersFound = collect($allPlayerIds)->filter(fn ($id) => in_array($id, $team1Members));
            $team2PlayersFound = collect($allPlayerIds)->filter(fn ($id) => in_array($id, $team2Members));
            $unexpectedPlayers = collect($allPlayerIds)->filter(fn ($id) => ! in_array($id, $expectedPlayers));

            if ($team1PlayersFound->isEmpty()) {
                $issues[] = "No players from {$match->team1->teamname} were found in this match";
            }

            if ($team2PlayersFound->isEmpty()) {
                $issues[] = "No players from {$match->team2->teamname} were found in this match";
            }

            if ($unexpectedPlayers->isNotEmpty()) {
                $unexpectedUsernames = User::whereIn('osu_id', $unexpectedPlayers)->pluck('osu_username')->toArray();
                $issues[] = 'Found '.count($unexpectedPlayers).' unexpected player(s): '.implode(', ', $unexpectedUsernames);
            }

            if ($team1PlayersFound->isEmpty() && $team2PlayersFound->isEmpty()) {
                return [
                    'valid' => false,
                    'message' => 'None of the expected players were found in this match.',
                    'issues' => array_merge($issues, ['This appears to be a different match. Please verify the match ID.']),
                ];
            }

            if (! empty($issues)) {
                return [
                    'valid' => false,
                    'message' => 'Player mismatch detected in the osu! match.',
                    'issues' => array_merge($issues, ['Please verify this is the correct match ID.']),
                ];
            }
        } else {
            if (! $match->team1_id || ! $match->team2_id) {
                return [
                    'valid' => false,
                    'message' => 'Match participants are not yet determined.',
                    'issues' => ['Both players must be set before entering results.'],
                ];
            }

            $expectedPlayers = [$match->player1->osu_id, $match->player2->osu_id];

            $player1Found = $allPlayerIds->contains($match->player1->osu_id);
            $player2Found = $allPlayerIds->contains($match->player2->osu_id);
            $unexpectedPlayers = $allPlayerIds->filter(fn ($id) => ! in_array($id, $expectedPlayers));

            if (! $player1Found) {
                $player1Name = $match->player1->osu_username ?? $match->player1->name ?? 'Player 1';
                $issues[] = "{$player1Name} was not found in this match";
            }

            if (! $player2Found) {
                $player2Name = $match->player2->osu_username ?? $match->player2->name ?? 'Player 2';
                $issues[] = "{$player2Name} was not found in this match";
            }

            if ($unexpectedPlayers->isNotEmpty()) {
                $unexpectedUsernames = User::whereIn('osu_id', $unexpectedPlayers)->pluck('osu_username')->toArray();
                $issues[] = 'Found '.count($unexpectedPlayers).' unexpected player(s): '.implode(', ', $unexpectedUsernames);
            }

            if (! $player1Found && ! $player2Found) {
                return [
                    'valid' => false,
                    'message' => 'Neither expected player was found in this match.',
                    'issues' => array_merge($issues, ['This appears to be a different match. Please verify the match ID.']),
                ];
            }

            if (! empty($issues)) {
                return [
                    'valid' => false,
                    'message' => 'Player mismatch detected in the osu! match.',
                    'issues' => array_merge($issues, ['Please verify this is the correct match ID.']),
                ];
            }
        }

        return ['valid' => true];
    }

    /**
     * Process osu! match events to extract valid games.
     */
    private function processOsuMatchGames(array $events, $mappoolBeatmapIds, MatchModel $match, Tournament $tournament): array
    {
        $games = [];
        $gameEvents = collect($events)->filter(fn ($event) => ($event['detail']['type'] ?? null) === 'other');

        foreach ($gameEvents as $index => $event) {
            $game = $event['game'] ?? null;
            if (! $game) {
                continue;
            }

            $beatmapId = $game['beatmap']['id'] ?? null;
            if (! $beatmapId) {
                continue;
            }

            $scores = collect($game['scores'] ?? []);
            if ($scores->isEmpty()) {
                continue;
            }

            $mappoolMap = null;
            if ($match->mappool) {
                $mappoolMap = $match->mappool->maps->firstWhere('map.osu_beatmap_id', $beatmapId);
            }

            $team1Score = 0;
            $team2Score = 0;
            $processedScores = [];

            if ($tournament->isTeamTournament()) {
                $team1Members = $match->team1->members->keyBy('osu_id');
                $team2Members = $match->team2->members->keyBy('osu_id');

                foreach ($scores as $scoreData) {
                    $osuUserId = $scoreData['user_id'] ?? null;
                    $scoreValue = $scoreData['score'] ?? 0;

                    if ($team1Members->has($osuUserId)) {
                        $user = $team1Members->get($osuUserId);
                        $team1Score += $scoreValue;
                        $processedScores[] = [
                            'user_id' => $user->id,
                            'team_id' => $match->team1_id,
                            'score' => $scoreValue,
                            'accuracy' => $scoreData['accuracy'] ?? 0,
                            'combo' => $scoreData['max_combo'] ?? 0,
                            'passed' => $scoreData['passed'] ?? false,
                        ];
                    } elseif ($team2Members->has($osuUserId)) {
                        $user = $team2Members->get($osuUserId);
                        $team2Score += $scoreValue;
                        $processedScores[] = [
                            'user_id' => $user->id,
                            'team_id' => $match->team2_id,
                            'score' => $scoreValue,
                            'accuracy' => $scoreData['accuracy'] ?? 0,
                            'combo' => $scoreData['max_combo'] ?? 0,
                            'passed' => $scoreData['passed'] ?? false,
                        ];
                    }
                }
            } else {
                foreach ($scores as $scoreData) {
                    $osuUserId = $scoreData['user_id'] ?? null;
                    $scoreValue = $scoreData['score'] ?? 0;

                    if ($osuUserId == $match->player1->osu_id) {
                        $team1Score = $scoreValue;
                        $processedScores[] = [
                            'user_id' => $match->player1->id,
                            'team_id' => $match->team1_id,
                            'score' => $scoreValue,
                            'accuracy' => $scoreData['accuracy'] ?? 0,
                            'combo' => $scoreData['max_combo'] ?? 0,
                            'passed' => $scoreData['passed'] ?? false,
                        ];
                    } elseif ($osuUserId == $match->player2->osu_id) {
                        $team2Score = $scoreValue;
                        $processedScores[] = [
                            'user_id' => $match->player2->id,
                            'team_id' => $match->team2_id,
                            'score' => $scoreValue,
                            'accuracy' => $scoreData['accuracy'] ?? 0,
                            'combo' => $scoreData['max_combo'] ?? 0,
                            'passed' => $scoreData['passed'] ?? false,
                        ];
                    }
                }
            }

            $winningTeamId = $team1Score > $team2Score ? $match->team1_id : $match->team2_id;

            $gameData = [
                'order_in_match' => count($games) + 1,
                'mappool_map_id' => $mappoolMap?->id,
                'winning_team_id' => $winningTeamId,
                'team1_score' => $team1Score,
                'team2_score' => $team2Score,
                'scores' => $processedScores,
            ];

            if ($mappoolMap) {
                $gameData['map'] = $mappoolMap->map;
            } else {
                $gameData['beatmap'] = $game['beatmap'];
            }

            $games[] = $gameData;
        }

        $team1Wins = collect($games)->filter(fn ($g) => $g['winning_team_id'] === $match->team1_id)->count();
        $team2Wins = collect($games)->filter(fn ($g) => $g['winning_team_id'] === $match->team2_id)->count();

        $winnerId = null;
        if ($match->best_of) {
            $winsNeeded = ceil($match->best_of / 2);
            if ($team1Wins >= $winsNeeded) {
                $winnerId = $match->team1_id;
            } elseif ($team2Wins >= $winsNeeded) {
                $winnerId = $match->team2_id;
            }
        } else {
            $winnerId = $team1Wins > $team2Wins ? $match->team1_id : ($team2Wins > $team1Wins ? $match->team2_id : null);
        }

        return [
            'games' => $games,
            'team1_score' => $team1Wins,
            'team2_score' => $team2Wins,
            'winner_id' => $winnerId,
        ];
    }

    /**
     * Fill match result (set winner).
     */
    public function fillMatchResult(Request $request, Tournament $tournament): mixed
    {
        $this->authorize('editMatches', $tournament);

        $request->validate([
            'match_id' => 'required|exists:matches,id',
            'winner_id' => 'nullable',
            'games' => 'nullable|array',
            'games.*.mappool_map_id' => 'nullable|exists:mappool_maps,id',
            'games.*.winning_team_id' => 'required',
            'games.*.scores' => 'required|array',
            'games.*.scores.*.user_id' => 'required|exists:users,id',
            'games.*.scores.*.score' => 'required|integer',
            'games.*.scores.*.accuracy' => 'nullable|numeric',
            'games.*.scores.*.combo' => 'nullable|integer',
            'games.*.scores.*.passed' => 'required|boolean',
        ]);

        $match = MatchModel::where('id', $request->match_id)
            ->where('tournament_id', $tournament->id)
            ->firstOrFail();

        DB::transaction(function () use ($request, $match, $tournament) {
            $oldWinnerId = $match->winner_team_id;
            $newWinnerId = $request->winner_id;

            // Delete existing scores
            $match->scores()->delete();

            if ($request->has('games') && is_array($request->games)) {
                foreach ($request->games as $index => $gameData) {
                    $winningTeamId = $gameData['winning_team_id'];

                    // Create a score record for each player in this map
                    foreach ($gameData['scores'] as $scoreData) {
                        Score::create([
                            'match_id' => $match->id,
                            'map_number' => $index + 1,
                            'mappool_map_id' => $gameData['mappool_map_id'],
                            'user_id' => $scoreData['user_id'],
                            'team_id' => $scoreData['team_id'] ?? null,
                            'winning_team_id' => $winningTeamId,
                            'score' => $scoreData['score'],
                            'accuracy' => $scoreData['accuracy'] ?? null,
                            'combo' => $scoreData['combo'] ?? null,
                            'passed' => $scoreData['passed'],
                        ]);
                    }
                }
            }

            $match->update([
                'winner_team_id' => $newWinnerId,
                'status' => 'completed',
                'match_end' => now(),
            ]);

            if ($oldWinnerId && $oldWinnerId !== $newWinnerId) {
                $this->removeFromNextMatch($match, $oldWinnerId, $tournament);
            }

            $this->advanceWinnerToNextMatch($match, $tournament);
        });

        return response()->json(['success' => true]);
    }

    /**
     * Delete a map from a match.
     */
    public function deleteGame(Tournament $tournament, Score $score): mixed
    {
        $this->authorize('editMatches', $tournament);

        $match = MatchModel::where('id', $score->match_id)
            ->where('tournament_id', $tournament->id)
            ->firstOrFail();

        DB::transaction(function () use ($score, $match, $tournament) {
            $mapNumber = $score->map_number;

            // Delete all scores for this map
            $match->scores()->where('map_number', $mapNumber)->delete();

            // Renumber remaining maps
            $remainingMaps = $match->scores()
                ->select('map_number')
                ->distinct()
                ->orderBy('map_number')
                ->pluck('map_number');

            foreach ($remainingMaps as $index => $oldMapNumber) {
                if ($oldMapNumber > $mapNumber) {
                    $match->scores()->where('map_number', $oldMapNumber)->update(['map_number' => $index + 1]);
                }
            }

            // Recalculate winner
            $team1Wins = $match->scores()->where('winning_team_id', $match->team1_id)->distinct('map_number')->count('map_number');
            $team2Wins = $match->scores()->where('winning_team_id', $match->team2_id)->distinct('map_number')->count('map_number');

            $winnerId = null;
            if ($match->best_of) {
                $winsNeeded = ceil($match->best_of / 2);
                if ($team1Wins >= $winsNeeded) {
                    $winnerId = $match->team1_id;
                } elseif ($team2Wins >= $winsNeeded) {
                    $winnerId = $match->team2_id;
                }
            } else {
                $winnerId = $team1Wins > $team2Wins ? $match->team1_id : ($team2Wins > $team1Wins ? $match->team2_id : null);
            }

            $oldWinnerId = $match->winner_team_id;
            if ($oldWinnerId !== $winnerId) {
                $match->update(['winner_team_id' => $winnerId]);

                if ($oldWinnerId) {
                    $this->removeFromNextMatch($match, $oldWinnerId, $tournament);
                }

                if ($winnerId) {
                    $this->advanceWinnerToNextMatch($match, $tournament);
                }
            }
        });

        return response()->json(['success' => true]);
    }

    /**
     * Get match games for editing.
     */
    public function getMatchGames(Tournament $tournament, MatchModel $match): mixed
    {
        $this->authorize('editMatches', $tournament);

        // Verify match belongs to this tournament
        if ($match->tournament_id !== $tournament->id) {
            abort(404);
        }

        // Get distinct maps by grouping scores by map_number
        $maps = $match->scores()
            ->select('map_number', 'winning_team_id', 'mappool_map_id')
            ->distinct()
            ->orderBy('map_number')
            ->get()
            ->groupBy('map_number')
            ->map(function ($scores, $mapNumber) {
                $firstScore = $scores->first();

                return [
                    'map_number' => $mapNumber,
                    'winning_team_id' => $firstScore->winning_team_id,
                    'mappool_map_id' => $firstScore->mappool_map_id,
                ];
            })
            ->values();

        return response()->json([
            'games' => $maps,
            'winner_team_id' => $match->winner_team_id,
            'status' => $match->status,
        ]);
    }

    /**
     * Mark a match as no-show and award the win to the opposing team/player.
     */
    public function markAsNoShow(Request $request, Tournament $tournament): mixed
    {
        $this->authorize('editMatches', $tournament);

        $validated = $request->validate([
            'match_id' => 'required|exists:matches,id',
            'no_show_team_id' => 'required|integer',
            'no_show_type' => 'required|in:no_show,disqualified',
        ]);

        $match = MatchModel::where('id', $validated['match_id'])
            ->where('tournament_id', $tournament->id)
            ->firstOrFail();

        $noShowTeamId = (int) $validated['no_show_team_id'];

        // Ensure both participants exist
        if (! $match->team1_id || ! $match->team2_id) {
            return response()->json([
                'success' => false,
                'message' => 'Match does not have both participants assigned.',
            ], 422);
        }

        // Validate the no-show team is actually in this match
        if ($noShowTeamId !== $match->team1_id && $noShowTeamId !== $match->team2_id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid team/player selected. The selected participant is not in this match.',
            ], 422);
        }

        // Determine the winner (opposite of the no-show)
        $winnerId = $noShowTeamId === $match->team1_id ? $match->team2_id : $match->team1_id;

        // Ensure we have a valid winner
        if (! $winnerId) {
            return response()->json([
                'success' => false,
                'message' => 'Could not determine winner. Match participants may not be properly set.',
            ], 422);
        }

        DB::transaction(function () use ($match, $winnerId, $noShowTeamId, $validated, $tournament) {
            $oldWinnerId = $match->winner_team_id;

            // Update match status and set winner
            $match->update([
                'status' => 'no_show',
                'winner_team_id' => $winnerId,
                'no_show_team_id' => $noShowTeamId,
                'no_show_type' => $validated['no_show_type'],
            ]);

            // Remove old winner from next match if winner changed
            if ($oldWinnerId && $oldWinnerId !== $winnerId) {
                $this->removeFromNextMatch($match, $oldWinnerId, $tournament);
            }

            // Advance the new winner to next match
            $this->advanceWinnerToNextMatch($match, $tournament);
        });

        return response()->json([
            'success' => true,
            'message' => 'Match marked as no-show and winner advanced.',
        ]);
    }

    /**
     * Fill match result manually without osu! match ID.
     */
    public function fillMatchResultManual(Request $request, Tournament $tournament): mixed
    {
        $this->authorize('editMatches', $tournament);

        $validated = $request->validate([
            'match_id' => 'required|exists:matches,id',
            'games' => 'required|array|min:1',
            'games.*.mappool_map_id' => 'sometimes|nullable|exists:mappool_maps,id',
            'games.*.winning_team_id' => 'required|integer',
            'games.*.scores' => 'sometimes|nullable|array',
            'games.*.scores.*.user_id' => 'required|exists:users,id',
            'games.*.scores.*.score' => 'nullable|integer|min:0',
            'games.*.scores.*.accuracy' => 'nullable|numeric|min:0|max:100',
            'games.*.scores.*.combo' => 'nullable|integer|min:0',
            'games.*.scores.*.passed' => 'nullable|boolean',
        ]);

        $match = MatchModel::where('id', $validated['match_id'])
            ->where('tournament_id', $tournament->id)
            ->firstOrFail();

        \Log::info('Manual entry received', [
            'match_id' => $match->id,
            'team1_id' => $match->team1_id,
            'team2_id' => $match->team2_id,
            'games_count' => count($validated['games']),
            'games' => $validated['games'],
        ]);

        DB::transaction(function () use ($match, $validated, $tournament) {
            // Delete existing scores
            $match->scores()->delete();

            // Create scores for each map
            foreach ($validated['games'] as $index => $gameData) {
                $winningTeamId = (int) $gameData['winning_team_id'];

                \Log::info("Creating map {$index}", [
                    'winning_team_id' => $winningTeamId,
                    'scores' => $gameData['scores'] ?? null,
                ]);

                // If we have detailed scores, create one score per player
                if (! empty($gameData['scores'])) {
                    foreach ($gameData['scores'] as $scoreData) {
                        if (empty($scoreData['user_id'])) {
                            continue;
                        }

                        $user = User::find($scoreData['user_id']);
                        $teamId = $tournament->is_team_tournament
                            ? $user->teams()->where('tournament_id', $tournament->id)->first()?->id
                            : $scoreData['user_id'];

                        Score::create([
                            'match_id' => $match->id,
                            'map_number' => $index + 1,
                            'mappool_map_id' => $gameData['mappool_map_id'] ?? null,
                            'user_id' => $scoreData['user_id'],
                            'team_id' => $teamId,
                            'winning_team_id' => $winningTeamId,
                            'score' => $scoreData['score'] ?? null,
                            'accuracy' => $scoreData['accuracy'] ?? null,
                            'combo' => $scoreData['combo'] ?? null,
                            'passed' => $scoreData['passed'] ?? true,
                        ]);
                    }
                } else {
                    // No detailed scores - just create a single score record to track the map winner
                    Score::create([
                        'match_id' => $match->id,
                        'map_number' => $index + 1,
                        'mappool_map_id' => $gameData['mappool_map_id'] ?? null,
                        'user_id' => null,
                        'team_id' => $winningTeamId,
                        'winning_team_id' => $winningTeamId,
                        'score' => null,
                        'accuracy' => null,
                        'combo' => null,
                        'passed' => true,
                    ]);
                }
            }

            // Refresh match to get the newly created scores
            $match->refresh();

            // Calculate winner based on map wins
            $team1Wins = $match->scores()->where('winning_team_id', $match->team1_id)->distinct('map_number')->count('map_number');
            $team2Wins = $match->scores()->where('winning_team_id', $match->team2_id)->distinct('map_number')->count('map_number');

            \Log::info('Calculating winner', [
                'team1_id' => $match->team1_id,
                'team1_wins' => $team1Wins,
                'team2_id' => $match->team2_id,
                'team2_wins' => $team2Wins,
                'best_of' => $match->best_of,
            ]);

            $winnerId = null;
            if ($match->best_of) {
                $winsNeeded = ceil($match->best_of / 2);
                if ($team1Wins >= $winsNeeded) {
                    $winnerId = $match->team1_id;
                } elseif ($team2Wins >= $winsNeeded) {
                    $winnerId = $match->team2_id;
                }
            } else {
                // If no best_of, winner is whoever has more game wins
                $winnerId = $team1Wins > $team2Wins ? $match->team1_id : ($team2Wins > $team1Wins ? $match->team2_id : null);
            }

            \Log::info('Winner determined', [
                'winner_id' => $winnerId,
                'old_winner_id' => $match->winner_team_id,
            ]);

            $oldWinnerId = $match->winner_team_id;

            // Remove old winner from next match if winner changed
            if ($oldWinnerId && $oldWinnerId !== $winnerId) {
                \Log::info('Removing old winner from next match', ['old_winner_id' => $oldWinnerId]);
                $this->removeFromNextMatch($match, $oldWinnerId, $tournament);
            }

            // Update match with winner and status
            $match->update([
                'winner_team_id' => $winnerId,
                'status' => 'completed',
            ]);

            \Log::info('Match updated', [
                'winner_team_id' => $winnerId,
                'status' => 'completed',
            ]);

            // Advance winner to next match (always run if we have a winner)
            if ($winnerId) {
                \Log::info('Advancing winner to next match', ['winner_id' => $winnerId]);
                $this->advanceWinnerToNextMatch($match, $tournament);
            } else {
                \Log::warning('No winner determined, not advancing to next match');
            }
        });

        return response()->json(['success' => true]);
    }

    /**
     * Remove a participant from the next match in the bracket.
     */
    private function removeFromNextMatch(MatchModel $match, int $participantId, Tournament $tournament): void
    {
        $nextMatch = $this->getNextMatch($match, $tournament);

        if (! $nextMatch) {
            return;
        }

        if ($nextMatch->team1_id === $participantId) {
            $nextMatch->update([
                'team1_id' => null,
                'team1_seed' => null,
            ]);
        } elseif ($nextMatch->team2_id === $participantId) {
            $nextMatch->update([
                'team2_id' => null,
                'team2_seed' => null,
            ]);
        }

        if (! $nextMatch->team1_id || ! $nextMatch->team2_id) {
            $nextMatch->update(['status' => 'pending']);
        }
    }

    /**
     * Advance the winner to the next match in the bracket.
     */
    private function advanceWinnerToNextMatch(MatchModel $match, Tournament $tournament): void
    {
        if (! $match->winner_team_id) {
            return;
        }

        $nextMatch = $this->getNextMatch($match, $tournament);

        if (! $nextMatch) {
            return;
        }

        $roundMatches = MatchModel::where('tournament_id', $tournament->id)
            ->where('round', $match->round)
            ->where('stage', $match->stage ?? 'bracket')
            ->orderBy('id')
            ->get();

        $matchPosition = $roundMatches->search(function ($m) use ($match) {
            return $m->id === $match->id;
        });

        if ($matchPosition === false) {
            return;
        }

        $isOddPosition = ($matchPosition % 2) === 0;
        $winnerSeed = $match->winner_team_id === $match->team1_id ? $match->team1_seed : $match->team2_seed;

        if ($isOddPosition) {
            $nextMatch->update([
                'team1_id' => $match->winner_team_id,
                'team1_seed' => $winnerSeed,
            ]);
        } else {
            $nextMatch->update([
                'team2_id' => $match->winner_team_id,
                'team2_seed' => $winnerSeed,
            ]);
        }

        if ($nextMatch->team1_id && $nextMatch->team2_id) {
            $nextMatch->update(['status' => 'scheduled']);
        }
    }

    /**
     * Get the next match in the bracket that this match feeds into.
     */
    private function getNextMatch(MatchModel $match, Tournament $tournament): ?MatchModel
    {
        $currentRound = $match->round;
        $nextRound = $currentRound + 1;

        $roundMatches = MatchModel::where('tournament_id', $tournament->id)
            ->where('round', $currentRound)
            ->where('stage', $match->stage ?? 'bracket')
            ->orderBy('id')
            ->get();

        $matchPosition = $roundMatches->search(function ($m) use ($match) {
            return $m->id === $match->id;
        });

        if ($matchPosition === false) {
            return null;
        }

        $nextMatchPosition = (int) floor($matchPosition / 2);

        return MatchModel::where('tournament_id', $tournament->id)
            ->where('round', $nextRound)
            ->where('stage', $match->stage ?? 'bracket')
            ->orderBy('id')
            ->skip($nextMatchPosition)
            ->first();
    }
}
