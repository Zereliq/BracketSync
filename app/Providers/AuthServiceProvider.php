<?php

namespace App\Providers;

use App\Models\Tournament;
use App\Policies\TournamentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     */
    protected $policies = [
        Tournament::class => TournamentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Define admin gate
        Gate::define('admin', function ($user) {
            return $user->isAdmin();
        });

        // Define gate shortcuts for tournament operations
        Gate::define('tournament.create', function ($user) {
            return (new TournamentPolicy)->create($user);
        });

        Gate::define('tournament.update', function ($user, $tournament) {
            return (new TournamentPolicy)->update($user, $tournament);
        });

        Gate::define('tournament.delete', function ($user, $tournament) {
            return (new TournamentPolicy)->delete($user, $tournament);
        });
    }
}
