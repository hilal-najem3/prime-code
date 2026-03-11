<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Modules\Tenants\Models\Domain;

class TenantResolver
{
    public function handle(Request $request, Closure $next)
    {
        $domain = $request->getHost();

        $domainModel = Domain::where('domain', $domain)->first();

        if (!$domainModel) {
            abort(404, 'Tenant not found.');
        }

        $tenant = $domainModel->tenant;

        if (!$tenant || !$tenant->active) {
            abort(403, 'Tenant inactive.');
        }

        Config::set('database.connections.tenant.database', $tenant->database);

        DB::purge('tenant');
        DB::reconnect('tenant');

        DB::setDefaultConnection('tenant');

        app()->instance('tenant', $tenant);

        return $next($request);
    }
}
