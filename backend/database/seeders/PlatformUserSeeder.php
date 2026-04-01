<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Models\User;
use Modules\Permissions\Models\Role;

class PlatformUserSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Create Platform Super Admin
        |--------------------------------------------------------------------------
        */

        $adminUser = User::updateOrCreate(
            ['email' => 'admin@platform.com'],
            [
                'name' => 'Platform Admin',
                'password' => Hash::make('password'),
                'enabled' => true
            ]
        );

        $role = Role::where('slug', 'super-admin')->first();

        if ($role) {
            $adminUser->roles()->syncWithoutDetaching([$role->id]);
        }
    }
}
