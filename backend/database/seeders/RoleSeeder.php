<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Permissions\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::updateOrCreate([
            'name' => 'Super Admin',
            'slug' => 'super-admin'
        ]);

        Role::updateOrCreate([
            'name' => 'Admin',
            'slug' => 'admin'
        ]);

        Role::updateOrCreate([
            'name' => 'User',
            'slug' => 'user'
        ]);
    }
}