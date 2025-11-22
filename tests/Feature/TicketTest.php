<?php

use App\Models\SiteRole;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->playerRole = SiteRole::create(['name' => 'player']);
    $this->modRole = SiteRole::create(['name' => 'mod']);
    $this->adminRole = SiteRole::create(['name' => 'admin']);
});

test('authenticated users can view tickets index', function () {
    $user = User::factory()->create(['siterole_id' => $this->playerRole->id]);

    $response = $this->actingAs($user)->get(route('dashboard.tickets.index'));

    $response->assertSuccessful();
    $response->assertViewIs('tickets.index');
});

test('players can only see their own tickets', function () {
    $user = User::factory()->create(['siterole_id' => $this->playerRole->id]);
    $otherUser = User::factory()->create(['siterole_id' => $this->playerRole->id]);

    $myTicket = Ticket::factory()->create(['user_id' => $user->id]);
    $otherTicket = Ticket::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->get(route('dashboard.tickets.index'));

    $response->assertSuccessful();
    $response->assertSee($myTicket->subject);
    $response->assertDontSee($otherTicket->subject);
});

test('admins and mods can see all tickets', function () {
    $admin = User::factory()->create(['siterole_id' => $this->adminRole->id]);
    $user1 = User::factory()->create(['siterole_id' => $this->playerRole->id]);
    $user2 = User::factory()->create(['siterole_id' => $this->playerRole->id]);

    $ticket1 = Ticket::factory()->create(['user_id' => $user1->id]);
    $ticket2 = Ticket::factory()->create(['user_id' => $user2->id]);

    $response = $this->actingAs($admin)->get(route('dashboard.tickets.index'));

    $response->assertSuccessful();
    $response->assertSee($ticket1->subject);
    $response->assertSee($ticket2->subject);
});

test('authenticated users can create tickets', function () {
    $user = User::factory()->create(['siterole_id' => $this->playerRole->id]);

    $response = $this->actingAs($user)->post(route('dashboard.tickets.store'), [
        'subject' => 'Test Support Ticket',
        'description' => 'This is a test description for the support ticket.',
        'priority' => 'medium',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('tickets', [
        'user_id' => $user->id,
        'subject' => 'Test Support Ticket',
        'status' => 'open',
        'priority' => 'medium',
    ]);
});

test('ticket creation requires subject and description', function () {
    $user = User::factory()->create(['siterole_id' => $this->playerRole->id]);

    $response = $this->actingAs($user)->post(route('dashboard.tickets.store'), [
        'priority' => 'medium',
    ]);

    $response->assertSessionHasErrors(['subject', 'description']);
});

test('users can view their own tickets', function () {
    $user = User::factory()->create(['siterole_id' => $this->playerRole->id]);
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get(route('dashboard.tickets.show', $ticket));

    $response->assertSuccessful();
    $response->assertViewIs('tickets.show');
    $response->assertSee($ticket->subject);
});

test('users cannot view other users tickets', function () {
    $user = User::factory()->create(['siterole_id' => $this->playerRole->id]);
    $otherUser = User::factory()->create(['siterole_id' => $this->playerRole->id]);
    $ticket = Ticket::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->get(route('dashboard.tickets.show', $ticket));

    $response->assertForbidden();
});

test('admins can view any ticket', function () {
    $admin = User::factory()->create(['siterole_id' => $this->adminRole->id]);
    $user = User::factory()->create(['siterole_id' => $this->playerRole->id]);
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($admin)->get(route('dashboard.tickets.show', $ticket));

    $response->assertSuccessful();
    $response->assertSee($ticket->subject);
});

test('players cannot update tickets', function () {
    $user = User::factory()->create(['siterole_id' => $this->playerRole->id]);
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->put(route('dashboard.tickets.update', $ticket), [
        'status' => 'closed',
    ]);

    $response->assertForbidden();
});

test('admins can update ticket status and priority', function () {
    $admin = User::factory()->create(['siterole_id' => $this->adminRole->id]);
    $user = User::factory()->create(['siterole_id' => $this->playerRole->id]);
    $ticket = Ticket::factory()->create([
        'user_id' => $user->id,
        'status' => 'open',
        'priority' => 'low',
    ]);

    $response = $this->actingAs($admin)->put(route('dashboard.tickets.update', $ticket), [
        'status' => 'in_progress',
        'priority' => 'high',
        'assigned_to' => $admin->id,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('tickets', [
        'id' => $ticket->id,
        'status' => 'in_progress',
        'priority' => 'high',
        'assigned_to' => $admin->id,
    ]);
});

test('resolving a ticket sets resolved_at and resolved_by', function () {
    $admin = User::factory()->create(['siterole_id' => $this->adminRole->id]);
    $user = User::factory()->create(['siterole_id' => $this->playerRole->id]);
    $ticket = Ticket::factory()->create([
        'user_id' => $user->id,
        'status' => 'open',
    ]);

    $response = $this->actingAs($admin)->put(route('dashboard.tickets.update', $ticket), [
        'status' => 'resolved',
    ]);

    $response->assertRedirect();
    $ticket->refresh();

    expect($ticket->status)->toBe('resolved');
    expect($ticket->resolved_at)->not->toBeNull();
    expect($ticket->resolved_by)->toBe($admin->id);
});

test('only admins can delete tickets', function () {
    $admin = User::factory()->create(['siterole_id' => $this->adminRole->id]);
    $user = User::factory()->create(['siterole_id' => $this->playerRole->id]);
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete(route('dashboard.tickets.destroy', $ticket));
    $response->assertForbidden();

    $response = $this->actingAs($admin)->delete(route('dashboard.tickets.destroy', $ticket));
    $response->assertRedirect();
    $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
});
