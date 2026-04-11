<?php

namespace Modules\Auth\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Auth\Models\User;
use App\Requests\GeneralRequest;
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

    public function index(GeneralRequest $request)
    {
        $users = $this->service->get(
            $request->validated(),
            $request->query()
        );

        return ApiResponse::success($users, 'Users fetched successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Show User
    |--------------------------------------------------------------------------
    */

    public function show(Request $request)
    {
        $user = $this->resolveRouteUser($request);

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

    public function update(UpdateUserRequest $request)
    {
        try {

            DB::beginTransaction();

            $user = $this->resolveRouteUser($request);
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

    public function destroy(Request $request)
    {
        try {

            DB::beginTransaction();

            $user = $this->resolveRouteUser($request);
            $this->service->delete($user);

            DB::commit();

            return ApiResponse::success(null, 'User deleted successfully');
        } catch (\Throwable $e) {

            DB::rollBack();

            throw $e;
        }
    }

    private function resolveRouteUser(Request $request): User
    {
        $routeUser = $request->route('user');

        if ($routeUser instanceof User) {
            return $routeUser;
        }

        return User::findOrFail($routeUser);
    }
}
