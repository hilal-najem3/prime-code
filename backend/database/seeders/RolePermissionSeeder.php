<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Permissions\Models\Permission;
use Modules\Permissions\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('slug', 'super-admin')->first();

        if (!$superAdminRole) {
            return;
        }

        $permissionIds = Permission::query()
            ->where('active', true)
            ->pluck('id')
            ->all();

        $superAdminRole->syncPermissions($permissionIds);
    }
}