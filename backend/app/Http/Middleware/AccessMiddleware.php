<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AccessMiddleware
{
    public function handle(Request $request, Closure $next, ...$params)
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        if ($params[0] === 'auto') {

            $permission = $request->route()->getName();

            if (!$user->hasPermissionTo($permission)) {
                abort(403);
            }

            return $next($request);
        }

        if ($params[0] === 'role') {

            $roles = explode('|', $params[1] ?? '');

            if (!$user->hasRole($roles)) {
                abort(403);
            }

            return $next($request);
        }

        abort(403);
    }
}
