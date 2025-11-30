<?php

use App\Http\Controllers\Admin\TournamentAdminController;
use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\Auth\OsuLoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\PublicTournamentController;
use App\Http\Controllers\QualifiersController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamManagementController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\TournamentInvitationsController;
use App\Http\Controllers\TournamentPlayersController;
use App\Http\Controllers\TournamentQualifiersController;
use App\Http\Controllers\UserSettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('homepage');

// Osu! authentication routes
Route::get('/auth/osu/redirect', [OsuLoginController::class, 'redirectToProvider'])->name('auth.osu.redirect');
Route::get('/auth/osu/callback', [OsuLoginController::class, 'handleProviderCallback'])->name('auth.osu.callback');
Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/')->with('success', 'You have been logged out successfully.');
})->name('logout');

// User settings routes
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/settings', [UserSettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [UserSettingsController::class, 'update'])->name('settings.update');
});

// Public tournament routes
Route::prefix('tournaments')->name('tournaments.')->group(function () {
    Route::get('/', [PublicTournamentController::class, 'index'])->name('index');
    Route::post('/{tournament}/like', [PublicTournamentController::class, 'like'])->name('like');
    Route::delete('/{tournament}/like', [PublicTournamentController::class, 'unlike'])->name('unlike');
    Route::get('/{tournament}', [PublicTournamentController::class, 'show'])->name('show');
    Route::get('/{tournament}/staff', [PublicTournamentController::class, 'staff'])->name('staff');
    Route::get('/{tournament}/players', [TournamentPlayersController::class, 'showPublic'])->name('players');
    Route::post('/{tournament}/players/signup', [TournamentPlayersController::class, 'signupPublic'])->name('players.signup');
    Route::delete('/{tournament}/players/signup', [TournamentPlayersController::class, 'withdrawPublic'])->name('players.withdraw');
    Route::post('/{tournament}/invitations/{invitation}/accept', [TournamentInvitationsController::class, 'accept'])->name('invitations.accept');
    Route::post('/{tournament}/invitations/{invitation}/decline', [TournamentInvitationsController::class, 'decline'])->name('invitations.decline');
    Route::get('/{tournament}/teams', [PublicTournamentController::class, 'teams'])->name('teams');
    Route::get('/{tournament}/qualifiers', [PublicTournamentController::class, 'qualifiers'])->name('qualifiers');
    Route::post('/{tournament}/qualifiers/reserve/{slot}', [QualifiersController::class, 'reserve'])->name('qualifiers.reserve');
    Route::post('/{tournament}/qualifiers/suggest', [QualifiersController::class, 'suggest'])->name('qualifiers.suggest');
    Route::delete('/{tournament}/qualifiers/cancel/{reservation}', [QualifiersController::class, 'cancel'])->name('qualifiers.cancel');
    Route::get('/{tournament}/matches', [PublicTournamentController::class, 'matches'])->name('matches');
    Route::get('/{tournament}/bracket', [PublicTournamentController::class, 'bracket'])->name('bracket');
    Route::get('/{tournament}/mappools', [PublicTournamentController::class, 'mappools'])->name('mappools');
});

// Dashboard routes (protected by auth middleware)
Route::middleware(['web', 'auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    // Tournament resource routes
    Route::resource('tournaments', TournamentController::class)->except(['show']);

    // User search endpoint
    Route::get('/users/search', [TournamentController::class, 'searchUsers'])->name('users.search');

    // Tournament tab routes (dashboard context)
    Route::prefix('tournaments/{tournament}')->name('tournaments.')->group(function () {
        Route::get('/', [TournamentController::class, 'show'])->name('show');
        Route::get('/bracket', [TournamentController::class, 'bracket'])->name('bracket');
        Route::post('/bracket/generate', [TournamentController::class, 'generateBracket'])->name('bracket.generate');
        Route::get('/bracket/seeding', [TournamentController::class, 'showSeeding'])->name('bracket.seeding');
        Route::post('/bracket/seeding', [TournamentController::class, 'generateBracketWithSeeding'])->name('bracket.seeding.generate');
        Route::post('/publish', [TournamentController::class, 'publish'])->name('publish');
        Route::get('/staff', [TournamentController::class, 'staff'])->name('staff');
        Route::get('/staff/add', [TournamentController::class, 'addStaff'])->name('staff.add');
        Route::post('/staff', [TournamentController::class, 'storeStaff'])->name('staff.store');
        Route::delete('/staff/{staffMember}', [TournamentController::class, 'removeStaff'])->name('staff.remove');
        Route::get('/roles', [\App\Http\Controllers\TournamentRoleController::class, 'index'])->name('roles.index');
        Route::put('/roles/update-all', [\App\Http\Controllers\TournamentRoleController::class, 'updateAll'])->name('roles.update-all');
        Route::post('/roles/create-custom', [\App\Http\Controllers\TournamentRoleController::class, 'storeCustom'])->name('roles.create-custom');
        Route::get('/roles/create', [\App\Http\Controllers\TournamentRoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [\App\Http\Controllers\TournamentRoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/edit', [\App\Http\Controllers\TournamentRoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [\App\Http\Controllers\TournamentRoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [\App\Http\Controllers\TournamentRoleController::class, 'destroy'])->name('roles.destroy');
        Route::get('/players', [TournamentPlayersController::class, 'showDashboard'])->name('players');
        Route::delete('/players/{player}', [TournamentPlayersController::class, 'removePlayer'])->name('players.remove');
        Route::post('/invitations', [TournamentInvitationsController::class, 'store'])->name('invitations.store');
        Route::get('/teams', [TournamentController::class, 'teams'])->name('teams');
        Route::get('/qualifiers', [TournamentController::class, 'qualifiers'])->name('qualifiers');
        Route::post('/qualifiers/settings', [TournamentQualifiersController::class, 'updateSettings'])->name('qualifiers.settings.update');
        Route::post('/qualifiers/slots', [TournamentQualifiersController::class, 'storeSlot'])->name('qualifiers.slots.store');
        Route::patch('/qualifiers/slots/{slot}', [TournamentQualifiersController::class, 'updateSlot'])->name('qualifiers.slots.update');
        Route::delete('/qualifiers/slots/{slot}', [TournamentQualifiersController::class, 'destroySlot'])->name('qualifiers.slots.destroy');
        Route::post('/qualifiers/suggestions/{reservation}/accept', [TournamentQualifiersController::class, 'acceptSuggestion'])->name('qualifiers.suggestions.accept');
        Route::post('/qualifiers/suggestions/{reservation}/referee-accept', [TournamentQualifiersController::class, 'refereeAcceptSuggestion'])->name('qualifiers.suggestions.referee-accept');
        Route::post('/qualifiers/suggestions/{reservation}/deny', [TournamentQualifiersController::class, 'denySuggestion'])->name('qualifiers.suggestions.deny');
        Route::get('/qualifiers/search-users', [TournamentQualifiersController::class, 'searchUsers'])->name('qualifiers.search-users');
        Route::get('/matches', [TournamentController::class, 'matches'])->name('matches');
        Route::get('/mappools', [TournamentController::class, 'mappools'])->name('mappools');
        Route::get('/mappools/create', [\App\Http\Controllers\MappoolController::class, 'create'])->name('mappools.create');
        Route::post('/mappools', [\App\Http\Controllers\MappoolController::class, 'store'])->name('mappools.store');
        Route::get('/mappools/{mappool}/edit', [\App\Http\Controllers\MappoolController::class, 'edit'])->name('mappools.edit');
        Route::post('/mappools/{mappool}/maps', [\App\Http\Controllers\MappoolController::class, 'addMap'])->name('mappools.maps.add');
        Route::delete('/mappools/{mappool}/maps/{mappoolMap}', [\App\Http\Controllers\MappoolController::class, 'removeMap'])->name('mappools.maps.remove');
        Route::delete('/mappools/{mappool}', [\App\Http\Controllers\MappoolController::class, 'destroy'])->name('mappools.destroy');
    });

    Route::resource('matches', MatchController::class)->only(['index', 'show']);
    Route::resource('teams', TeamController::class)->only(['index', 'show']);

    // Team Management routes
    Route::prefix('tournaments/{tournament}')->name('tournaments.')->group(function () {
        Route::post('/teams', [TeamManagementController::class, 'store'])->name('teams.store');
    });

    Route::prefix('teams')->name('teams.')->group(function () {
        Route::post('/{team}/invite', [TeamManagementController::class, 'invite'])->name('invite');
        Route::delete('/{team}', [TeamManagementController::class, 'destroy'])->name('destroy');
        Route::delete('/{team}/members/{member}', [TeamManagementController::class, 'removeMember'])->name('members.remove');
    });

    // Team Invitations routes
    Route::prefix('invitations')->name('invitations.')->group(function () {
        Route::post('/{invitation}/accept', [TeamManagementController::class, 'acceptInvitation'])->name('accept');
        Route::post('/{invitation}/decline', [TeamManagementController::class, 'declineInvitation'])->name('decline');
    });

    // Staff Invitations routes
    Route::prefix('staff-invitations')->name('staff-invitations.')->group(function () {
        Route::post('/{invitation}/accept', [TournamentController::class, 'acceptStaffInvitation'])->name('accept');
        Route::post('/{invitation}/decline', [TournamentController::class, 'declineStaffInvitation'])->name('decline');
    });

    // Support Tickets routes
    Route::resource('tickets', TicketController::class);
    Route::post('/tickets/{ticket}/replies', [TicketController::class, 'storeReply'])->name('tickets.replies.store');
});

// Admin routes (protected by auth and admin gate)
Route::middleware(['web', 'auth', 'can:admin'])->prefix('dashboard/admin')->name('dashboard.admin.')->group(function () {
    Route::get('/users', [UserRoleController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/role', [UserRoleController::class, 'update'])->name('users.role.update');
    Route::get('/tournaments', [TournamentAdminController::class, 'index'])->name('tournaments.index');

    // Queue Management
    Route::get('/queue', [\App\Http\Controllers\Admin\QueueManagementController::class, 'index'])->name('queue.index');
    Route::post('/queue/pause', [\App\Http\Controllers\Admin\QueueManagementController::class, 'pause'])->name('queue.pause');
    Route::post('/queue/resume', [\App\Http\Controllers\Admin\QueueManagementController::class, 'resume'])->name('queue.resume');
    Route::post('/queue/restart', [\App\Http\Controllers\Admin\QueueManagementController::class, 'restart'])->name('queue.restart');
    Route::post('/queue/work', [\App\Http\Controllers\Admin\QueueManagementController::class, 'work'])->name('queue.work');
    Route::post('/queue/retry/{id}', [\App\Http\Controllers\Admin\QueueManagementController::class, 'retry'])->name('queue.retry');
    Route::post('/queue/retry-all', [\App\Http\Controllers\Admin\QueueManagementController::class, 'retryAll'])->name('queue.retry-all');
    Route::delete('/queue/jobs/{id}', [\App\Http\Controllers\Admin\QueueManagementController::class, 'deleteJob'])->name('queue.delete');
    Route::delete('/queue/failed/{id}', [\App\Http\Controllers\Admin\QueueManagementController::class, 'deleteFailed'])->name('queue.delete-failed');
    Route::post('/queue/flush', [\App\Http\Controllers\Admin\QueueManagementController::class, 'flush'])->name('queue.flush');
    Route::post('/queue/clear', [\App\Http\Controllers\Admin\QueueManagementController::class, 'clear'])->name('queue.clear');
});
