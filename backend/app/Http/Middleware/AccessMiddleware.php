<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AccessMiddleware
{
    public function handle(Request $request, Closure $next, ...$params)
    {
        /*
        |--------------------------------------------------------------------------
        | Ensure User Is Authenticated
        |--------------------------------------------------------------------------
        */

        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized.');
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Middleware Parameters
        |--------------------------------------------------------------------------
        */

        if (empty($params)) {
            abort(500, 'AccessMiddleware requires parameters.');
        }

        $mode = $params[0];

        /*
        |--------------------------------------------------------------------------
        | Automatic Permission Check (route name)
        |--------------------------------------------------------------------------
        */

        if ($mode === 'auto') {
            $permission = $request->route()?->getName();

            if (!$permission) {
                throw new \RuntimeException(
                    'All protected routes must have a name.'
                );
            }

            if (!$user->hasPermissionTo($permission)) {
                abort(403, 'Forbidden.');
            }

            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Role Check
        |--------------------------------------------------------------------------
        */

        if ($mode === 'role') {

            $roles = $params[1] ?? '';

            if (!$user->hasRole($roles)) {
                abort(403, 'Forbidden.');
            }

            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Explicit Permission Check
        |--------------------------------------------------------------------------
        */

        if ($mode === 'permission') {

            $permission = $params[1] ?? null;

            if (!$permission) {
                abort(500, 'Permission not specified.');
            }

            if (!$user->hasPermissionTo($permission)) {
                abort(403, 'Forbidden.');
            }

            return $next($request);
        }

        abort(403, 'Forbidden.');
    }
}
