<?php

namespace Modules\Auth\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Auth\Models\User;
use Modules\Auth\Services\UserService;
use Modules\Auth\Requests\StoreUserRequest;
use Modules\Auth\Requests\UpdateUserRequest;
use App\Support\ApiResponse;

class UsersController extends Controller
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /*
    |--------------------------------------------------------------------------
    | List Users
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $users = $this->service->getAll();

        return ApiResponse::success($users, 'Users fetched successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Show User
    |--------------------------------------------------------------------------
    */

    public function show(User $user)
    {
        return ApiResponse::success(
            $user->load('roles'),
            'User fetched successfully'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Store User
    |--------------------------------------------------------------------------
    */

    public function store(StoreUserRequest $request)
    {
        try {

            DB::beginTransaction();

            $user = $this->service->create($request->validated());

            DB::commit();

            return ApiResponse::success($user, 'User created successfully');
        } catch (\Throwable $e) {

            DB::rollBack();

            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update User
    |--------------------------------------------------------------------------
    */

    public function update(UpdateUserRequest $request, User $user)
    {
        try {

            DB::beginTransaction();

            $user = $this->service->update($user, $request->validated());

            DB::commit();

            return ApiResponse::success($user, 'User updated successfully');
        } catch (\Throwable $e) {

            DB::rollBack();

            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete User
    |--------------------------------------------------------------------------
    */

    public function destroy(User $user)
    {
        try {

            DB::beginTransaction();

            $this->service->delete($user);

            DB::commit();

            return ApiResponse::success(null, 'User deleted successfully');
        } catch (\Throwable $e) {

            DB::rollBack();

            throw $e;
        }
    }
}
