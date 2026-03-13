<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Models\User;
use Modules\Permissions\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Admin User
        |--------------------------------------------------------------------------
        */

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => 'password'
        ]);

        $role = Role::where('slug', 'super-admin')->first();

        $admin->roles()->attach($role->id);

        /*
        |--------------------------------------------------------------------------
        | Example Users
        |--------------------------------------------------------------------------
        */

        // User::factory(5)->create();
    }
}