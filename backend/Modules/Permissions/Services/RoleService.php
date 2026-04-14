<?php

namespace Modules\Permissions\Services;

use Modules\Permissions\Models\Role;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class RoleService
{
    /*
    |--------------------------------------------------------------------------
    | Clear Users Permission Cache For Role
    |--------------------------------------------------------------------------
    */

    protected function clearUsersPermissionCache(Role $role): void
    {
        $role->users()->get()->each(function ($user) {
            $user->clearPermissionCache();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Get All Roles
    |--------------------------------------------------------------------------
    */

    public function get()
    {
        return Role::query()
            ->latest()
            ->get()->map(function ($role) {
                $role->permissions = $role->permissions; // trigger accessor
                return $role;
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Create Role
    |--------------------------------------------------------------------------
    */

    public function create(array $data): Role
    {
        /*
        |--------------------------------------------------------------------------
        | Extract Roles
        |--------------------------------------------------------------------------
        */

        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        /*
        |--------------------------------------------------------------------------
        | Create Role
        |--------------------------------------------------------------------------
        |--------------------------------------------------------------------------
        */

        $role = Role::create([
            ...$data
        ]);

        /*
        |--------------------------------------------------------------------------
        | Sync Roles
        |--------------------------------------------------------------------------
        */

        if (!empty($permissions)) {
            $role->syncPermissions($permissions);
        }

        $role->permissions = $role->permissions; // trigger accessor
        return $role;
    }

    /*
    |--------------------------------------------------------------------------
    | Update Role
    |--------------------------------------------------------------------------
    */

    public function update(Role $role, array $data): Role
    {
        /*
        |--------------------------------------------------------------------------
        | Extract Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = $data['permissions'] ?? null;
        unset($data['permissions']);

        /*
        |--------------------------------------------------------------------------
        | Update Role
        |--------------------------------------------------------------------------
        */

        $role->update($data);

        /*
        |--------------------------------------------------------------------------
        | Sync Permissions (IMPORTANT)
        |--------------------------------------------------------------------------
        */

        if (is_array($permissions)) {
            $role->syncPermissions($permissions);
            $this->clearUsersPermissionCache($role);
        }

        $role->permissions = $role->permissions; // trigger accessor
        return $role;
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Role
    |--------------------------------------------------------------------------
    */

    public function delete(Role $role): void
    {
        /*
        |--------------------------------------------------------------------------
        | Prevent Delete super admin role
        |--------------------------------------------------------------------------
        */

        if ($role->id == 1) {
            throw ValidationException::withMessages([
                'role' => ['You cannot delete this role.']
            ]);
        }

        $users = $role->users()->get();

        /*
        |--------------------------------------------------------------------------
        | Detach Roles (optional but clean)
        |--------------------------------------------------------------------------
        */

        $role->users()->detach();

        /*
        |--------------------------------------------------------------------------
        | Delete (Soft Delete)
        |--------------------------------------------------------------------------
        */

        DB::table('roles_permissions')
            ->where('role_id', $role->id)
            ->delete();

        $role->delete();

        $users->each(function ($user) {
            $user->clearPermissionCache();
        });
    }
}
