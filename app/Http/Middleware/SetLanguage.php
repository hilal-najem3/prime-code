<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Language;

class SetLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $lang = $request->query('lang')
            ?? session('locale')
            ?? $request->header('Accept-Language')
            ?? config('app.locale');

        $lang = substr($lang, 0, 2); // ensure 2-letter code only

        if (Language::where('code', $lang)->where('is_active', true)->exists()) {
            app()->setLocale($lang);
            session(['locale' => $lang]);
        } else {
            app()->setLocale(config('app.fallback_locale'));
        }

        return $next($request);
    }
}