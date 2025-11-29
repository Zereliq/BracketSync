<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Priority: URL parameter > Session > User preference > Browser language > Default
        $locale = $request->get('lang')
            ?? session('locale')
            ?? (auth()->check() ? auth()->user()->locale : null)
            ?? $request->getPreferredLanguage(config('app.available_locales', ['en', 'nl']))
            ?? config('app.locale', 'en');

        // Validate the locale is available
        $availableLocales = config('app.available_locales', ['en', 'nl']);
        if (! in_array($locale, $availableLocales)) {
            $locale = config('app.locale', 'en');
        }

        // Set the application locale
        app()->setLocale($locale);

        // Store in session for persistence
        session(['locale' => $locale]);

        return $next($request);
    }
}
