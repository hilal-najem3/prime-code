<?php

namespace Modules\Auth\Traits;

use Illuminate\Support\Facades\Cache;
use Modules\Permissions\Models\Permission;
use Illuminate\Support\Facades\DB;
use Modules\Permissions\Models\Role;

trait HasPermissionsTrait
{
    protected function permissionCacheKey(): string
    {
        $tenant = app(\Modules\Tenants\Support\TenantContext::class)->get();

        if (!$tenant) {
            return "platform_user_permissions_{$this->id}";
        }

        return "tenant_{$tenant->id}_user_permissions_{$this->id}";
    }

    protected function isPlatformSuperAdmin(): bool
    {
        if (tenant()) {
            return false;
        }

        $this->loadMissing('roles');

        return $this->roles->contains('slug', 'super-admin');
    }

    protected function getPlatformSuperAdminPermissions(): array
    {
        $cacheKey = "platform_user_permissions_{$this->id}";

        return Cache::remember($cacheKey, 3600, function () {
            return Permission::query()
                ->where('active', true)
                ->pluck('slug')
                ->unique()
                ->values()
                ->toArray();
        });
    }

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
        if ($this->isPlatformSuperAdmin()) {
            return $this->getPlatformSuperAdminPermissions();
        }

        $tenant = app(\Modules\Tenants\Support\TenantContext::class)->get();
        $tenantId = $tenant?->id ?? 'central';

        $cacheKey = "tenant_{$tenantId}_user_permissions_{$this->id}";

        return Cache::remember($cacheKey, 3600, function () use ($tenantId) {

            $permissions = collect();

            foreach ($this->roles as $role) {

                $roleCacheKey = "tenant_{$tenantId}_role_permissions_{$role->id}";

                $rolePermissions = Cache::remember($roleCacheKey, 3600, function () use ($role) {

                    // ✅ USE DEFAULT CONNECTION (tenant already set)
                    $permissionIds = DB::table('roles_permissions')
                        ->where('role_id', $role->id)
                        ->pluck('permission_id');

                    // ✅ FETCH FROM PLATFORM
                    return \Modules\Permissions\Models\Permission::query()
                        ->whereIn('id', $permissionIds)
                        ->pluck('slug')
                        ->toArray();
                });

                $permissions = $permissions->merge($rolePermissions);
            }

            return $permissions
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

        dd($permissions, $permission, $wildcard);

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
        cache()->forget($this->permissionCacheKey());
    }
}
