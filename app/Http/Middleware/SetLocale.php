<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locales = Config::get('locales.locales', ['en']); // from config/locales.php
        \Carbon\Carbon::setLocale(app()->getLocale());
        $locale = $request->segment(1); // first part of URL (e.g. "ar")

        if (in_array($locale, $locales)) {
            App::setLocale($locale);
        } else {
            // fallback to English
            App::setLocale('en');
        }

        return $next($request);
    }
}