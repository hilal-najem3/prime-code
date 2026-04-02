<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Modules\Models\Module;
use App\Support\ApiResponse;

class ModuleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $moduleSlug)
    {
        $tenant = app('currentTenant'); // or however you store tenant

        if (!$tenant) {
            return ApiResponse::error('Tenant not resolved', 404);
        }

        // Check if module exists and is enabled
        $module = Module::where('slug', $moduleSlug)
            ->where('enabled', true)
            ->first();

        if (!$module) {
            return ApiResponse::error('Module not found or disabled', 404);
        }

        // Check if tenant has this module
        $hasModule = $tenant->modules()
            ->where('modules.id', $module->id)
            ->exists();

        if (!$hasModule) {
            return ApiResponse::error('Module not enabled for this tenant', 403);
        }

        return $next($request);
    }
}