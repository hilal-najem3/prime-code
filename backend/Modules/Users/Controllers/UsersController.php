<?php

namespace Modules\Users\Controllers;

use Modules\Users\Services\UserService;
use Modules\Users\Requests\CreateUserRequest;
use Modules\Users\Requests\UpdateUserRequest;
use App\Requests\GeneralRequest;
use Modules\Auth\Models\User;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class UsersController
{
    public function __construct(
        protected UserService $service
    ) {}

    public function index(GeneralRequest $request)
    {
        $users = $this->service->get(
            $request->validated(),
            $request->query()
        );

        return ApiResponse::success($users, 'Users fetched successfully.');
    }

    public function store(CreateUserRequest $request)
    {
        try {

            DB::beginTransaction();

            $user = $this->service->create($request->validated());

            DB::commit();

            return ApiResponse::success($user, 'User created successfully.');
        } catch (Throwable $e) {

            DB::rollBack();

            return ApiResponse::error($e->getMessage());
        }
    }

    public function show(User $user)
    {
        return ApiResponse::success($user, 'User retrieved.');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        try {

            DB::beginTransaction();

            $user = $this->service->update($user, $request->validated());

            DB::commit();

            return ApiResponse::success($user, 'User updated.');
        } catch (Throwable $e) {

            DB::rollBack();

            return ApiResponse::error($e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        try {

            DB::beginTransaction();
            $user->clearPermissionCache();
            $user->delete();

            DB::commit();

            return ApiResponse::success(null, 'User deleted.');
        } catch (Throwable $e) {

            DB::rollBack();

            return ApiResponse::error($e->getMessage());
        }
    }
}
