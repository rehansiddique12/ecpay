<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        // Get the language from session or fallback to app default
        $locale = Session::get('locale', config('app.locale'));

        // Set the locale for the application
        App::setLocale($locale);

        return $next($request);
    }
}
