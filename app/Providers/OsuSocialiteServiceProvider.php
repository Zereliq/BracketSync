<?php

namespace App\Providers;

use App\Socialite\OsuProvider;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;

class OsuSocialiteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Socialite::extend('osu', function ($app) {
            $config = $app['config']['services.osu'];

            return Socialite::buildProvider(OsuProvider::class, $config);
        });
    }
}
