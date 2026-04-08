<?php

namespace Modules\Users\Services;

use Modules\Auth\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /*
    |--------------------------------------------------------------------------
    | Get Users
    |--------------------------------------------------------------------------
    */

    public function get(array $filters = [], array $queryParams = [])
    {
        $perPage = $filters['per_page'] ?? null;
        $search = trim($filters['search'] ?? '');
        $sort = $filters['sort'] ?? 'id';
        $direction = strtolower($filters['direction'] ?? 'asc');

        $query = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('enabled', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction);

        return $perPage
            ? $query->paginate($perPage)->appends($queryParams)
            : $query->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Create User
    |--------------------------------------------------------------------------
    */

    public function create(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'enabled' => $data['enabled'] ?? true
        ]);

        /*
        |--------------------------------------------------------------------------
        | Assign Roles
        |--------------------------------------------------------------------------
        */

        if (!empty($data['roles'])) {
            $user->roles()->sync($data['roles']);
        }

        $user->clearPermissionCache();

        return $user->fresh();
    }

    /*
    |--------------------------------------------------------------------------
    | Update User
    |--------------------------------------------------------------------------
    */

    public function update(User $user, array $data): User
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        /*
        |--------------------------------------------------------------------------
        | Update Roles
        |--------------------------------------------------------------------------
        */

        if (array_key_exists('roles', $data)) {
            $user->roles()->sync($data['roles']);
        }

        $user->clearPermissionCache();

        return $user->fresh();
    }
}
