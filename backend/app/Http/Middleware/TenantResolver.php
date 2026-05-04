<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Tenants\Models\Domain;
use Modules\Tenants\Support\TenantContext;

class TenantResolver
{
    public function handle(Request $request, Closure $next)
    {
        // Add the logs only if we are in local env
        if (app()->environment('local')) {
            Log::info("Incoming request: {$request->method()} {$request->fullUrl()}", [
                'headers' => $request->headers->all(),
                'ip' => $request->ip(),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
            ]);
        }

        if ($request->isMethod('OPTIONS')) {
            return response()->noContent();
        }

        $domain = $request->getHost();

        /*
        |--------------------------------------------------------------------------
        | Normalize Admin Subdomain
        |--------------------------------------------------------------------------
        */

        if (str_starts_with($domain, 'admin.')) {
            $domain = substr($domain, 6); // remove "admin."
        }

        // Log incoming request for debugging
        if (app()->environment('local')) {
            Log::info("Incoming request for domain: {$domain}", [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'headers' => $request->headers->all(),
                'ip' => $request->ip(),
            ]);
        }
        
        /*
        |--------------------------------------------------------------------------
        | Platform Domain Bypass
        |--------------------------------------------------------------------------
        */

        if (in_array($domain, config('app.platform_domains', []))) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Resolve Tenant
        |--------------------------------------------------------------------------
        */

        $domainModel = cache()->remember(
            "tenant_domain_{$domain}",
            3600,
            fn() => Domain::with('tenant')
                ->where('domain', $domain)
                ->first()
        );

        if (!$domainModel || !$domainModel->tenant) {
            abort(404, 'Tenant not found.');
        }

        $tenant = $domainModel->tenant;

        if (!$tenant->isActive()) {
            abort(403, 'Tenant account is not active.');
        }

        /*
        |--------------------------------------------------------------------------
        | Configure Tenant Database
        |--------------------------------------------------------------------------
        */
        tenant_connect($tenant);

        if (app()->environment('local')) {
            Log::info("Tenant resolved: {$tenant->name} ({$tenant->id}) for domain: {$domain}");
            // Log full request details for debugging
            Log::debug('Request details', [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'headers' => $request->headers->all(),
                'ip' => $request->ip(),
            ]);
        }
        
        app(TenantContext::class)->set($tenant);

        return $next($request);
    }

    /*
    |--------------------------------------------------------------------------
    | Reset Tenant Context
    |--------------------------------------------------------------------------
    */

    public function terminate($request, $response)
    {
        DB::setDefaultConnection(config('database.default'));

        app(TenantContext::class)->clear();
    }
}
