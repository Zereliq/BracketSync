<?php

use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('displays the tournament index page', function () {
    $response = get(route('tournaments.index'));

    $response->assertSuccessful();
    $response->assertSee('All Tournaments');
});

it('only shows published and ongoing tournaments', function () {
    $draft = Tournament::factory()->create(['status' => 'draft', 'name' => 'Draft Tournament']);
    $published = Tournament::factory()->create(['status' => 'published', 'name' => 'Published Tournament']);
    $ongoing = Tournament::factory()->create(['status' => 'ongoing', 'name' => 'Ongoing Tournament']);
    $finished = Tournament::factory()->create(['status' => 'finished', 'name' => 'Finished Tournament']);

    $response = get(route('tournaments.index'));

    $response->assertSee('Published Tournament');
    $response->assertSee('Ongoing Tournament');
    $response->assertDontSee('Draft Tournament');
    $response->assertDontSee('Finished Tournament');
});

it('filters tournaments by mode', function () {
    Tournament::factory()->create(['status' => 'published', 'mode' => 'standard', 'name' => 'Standard Tournament']);
    Tournament::factory()->create(['status' => 'published', 'mode' => 'taiko', 'name' => 'Taiko Tournament']);

    $response = get(route('tournaments.index', ['mode' => 'standard']));

    $response->assertSee('Standard Tournament');
    $response->assertDontSee('Taiko Tournament');
});

it('filters tournaments by status', function () {
    Tournament::factory()->create(['status' => 'published', 'name' => 'Published Tournament']);
    Tournament::factory()->create(['status' => 'ongoing', 'name' => 'Ongoing Tournament']);

    $response = get(route('tournaments.index', ['status' => 'ongoing']));

    $response->assertSee('Ongoing Tournament');
    $response->assertDontSee('Published Tournament');
});

it('allows authenticated users to like a tournament', function () {
    $user = User::factory()->create();
    $tournament = Tournament::factory()->create(['status' => 'published']);

    $response = actingAs($user)->postJson(route('tournaments.like', $tournament));

    $response->assertSuccessful();
    expect($user->likedTournaments)->toHaveCount(1);
    expect($user->likedTournaments->first()->id)->toBe($tournament->id);
});

it('prevents unauthenticated users from liking a tournament', function () {
    $tournament = Tournament::factory()->create(['status' => 'published']);

    $response = $this->postJson(route('tournaments.like', $tournament));

    $response->assertUnauthorized();
});

it('allows authenticated users to unlike a tournament', function () {
    $user = User::factory()->create();
    $tournament = Tournament::factory()->create(['status' => 'published']);

    $user->likedTournaments()->attach($tournament->id);

    $response = actingAs($user)->deleteJson(route('tournaments.unlike', $tournament));

    $response->assertSuccessful();
    expect($user->likedTournaments()->count())->toBe(0);
});

it('prevents liking the same tournament twice', function () {
    $user = User::factory()->create();
    $tournament = Tournament::factory()->create(['status' => 'published']);

    $user->likedTournaments()->attach($tournament->id);

    $response = actingAs($user)->postJson(route('tournaments.like', $tournament));

    $response->assertStatus(400);
});

it('sorts tournaments by likes count by default', function () {
    $tournament1 = Tournament::factory()->create(['status' => 'published', 'name' => 'Tournament 1']);
    $tournament2 = Tournament::factory()->create(['status' => 'published', 'name' => 'Tournament 2']);
    $tournament3 = Tournament::factory()->create(['status' => 'published', 'name' => 'Tournament 3']);

    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $tournament2->likes()->attach([$user1->id, $user2->id]);
    $tournament3->likes()->attach($user1->id);

    $response = get(route('tournaments.index'));

    $tournaments = $response->viewData('tournaments');

    expect($tournaments->first()->name)->toBe('Tournament 2');
    expect($tournaments->last()->name)->toBe('Tournament 1');
});

it('filters by my tournaments for authenticated users', function () {
    $user = User::factory()->create();
    $myTournament = Tournament::factory()->create(['status' => 'published', 'name' => 'My Tournament']);
    $otherTournament = Tournament::factory()->create(['status' => 'published', 'name' => 'Other Tournament']);

    $myTournament->tournamentRoleLinks()->create([
        'user_id' => $user->id,
        'role_id' => 1,
    ]);

    $response = actingAs($user)->get(route('tournaments.index', ['my_tournaments' => 'true']));

    $response->assertSee('My Tournament');
    $response->assertDontSee('Other Tournament');
});

it('filters by liked tournaments for authenticated users', function () {
    $user = User::factory()->create();
    $likedTournament = Tournament::factory()->create(['status' => 'published', 'name' => 'Liked Tournament']);
    $otherTournament = Tournament::factory()->create(['status' => 'published', 'name' => 'Other Tournament']);

    $user->likedTournaments()->attach($likedTournament->id);

    $response = actingAs($user)->get(route('tournaments.index', ['liked' => 'true']));

    $response->assertSee('Liked Tournament');
    $response->assertDontSee('Other Tournament');
});
