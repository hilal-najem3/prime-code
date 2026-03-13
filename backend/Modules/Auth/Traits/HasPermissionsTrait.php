<?php

namespace Modules\Auth\Traits;

use Illuminate\Support\Facades\Cache;
use Modules\Permissions\Models\Permission;
use Illuminate\Support\Facades\DB;
use Modules\Permissions\Models\Role;

trait HasPermissionsTrait
{

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'users_roles'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Cached Permissions
    |--------------------------------------------------------------------------
    |
    | Permissions are cached for performance. This prevents repeated
    | database queries during authorization checks.
    |
    */

    public function getCachedPermissions(): array
    {
        $tenantId = app('tenant')->id ?? 'central';

        $cacheKey = "tenant_{$tenantId}_user_permissions_{$this->id}";

        return Cache::remember($cacheKey, 3600, function () {


            $roleIds = $this->roles->pluck('id');

            $permissionIds = DB::connection('tenant')
                ->table('roles_permissions')
                ->whereIn('role_id', $roleIds)
                ->pluck('permission_id');

            return Permission::query()
                ->whereIn('id', $permissionIds)
                ->pluck('slug')
                ->unique()
                ->values()
                ->toArray();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Permission Check
    |--------------------------------------------------------------------------
    */

    public function hasPermissionTo(string $permission): bool
    {
        $permissions = $this->getCachedPermissions();

        if (in_array('*', $permissions)) {
            return true;
        }

        if (in_array($permission, $permissions)) {
            return true;
        }

        $wildcard = explode('.', $permission)[0] . '.*';

        return in_array($wildcard, $permissions);
    }

    /*
    |--------------------------------------------------------------------------
    | Role Check
    |--------------------------------------------------------------------------
    */

    public function hasRole(string $roles): bool
    {
        $rolesArray = explode('|', $roles);

        $this->loadMissing('roles');

        return $this->roles
            ->pluck('slug')
            ->intersect($rolesArray)
            ->isNotEmpty();
    }

    /*
    |--------------------------------------------------------------------------
    | Permission Cache Reset
    |--------------------------------------------------------------------------
    */

    public function clearPermissionCache(): void
    {
        $tenantId = app('tenant')->id ?? 'central';

        Cache::forget("tenant_{$tenantId}_user_permissions_{$this->id}");
    }
}