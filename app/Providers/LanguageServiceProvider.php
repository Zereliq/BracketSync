<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class LanguageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share available locales with all views
        view()->share('availableLocales', config('app.available_locales', ['en']));
        view()->share('localeNames', config('app.locale_names', ['en' => 'English']));
    }
}
