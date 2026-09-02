<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies the logged-in user's saved language preference (Profile
 * Settings → Language) to this request — falls back to the app default
 * (English) for guests, or if the saved value isn't one of the
 * languages actually shipped in config('locales').
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->user()?->locale;

        if ($locale && array_key_exists($locale, config('locales'))) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
