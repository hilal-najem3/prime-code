<?php

namespace Modules\Permissions\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Permissions\Models\Role;
use Modules\Permissions\Services\RoleService;
use Modules\Permissions\Requests\StoreRoleRequest;
use Modules\Permissions\Requests\UpdateRoleRequest;
use App\Support\ApiResponse;

class RolesController extends Controller
{
    protected RoleService $service;

    public function __construct(RoleService $service)
    {
        $this->service = $service;
    }

    /*
    |--------------------------------------------------------------------------
    | List Roles
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $roles = $this->service->get();

        return ApiResponse::success($roles, 'Roles fetched successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Show Role
    |--------------------------------------------------------------------------
    */

    public function show(Request $request)
    {
        $role = resolve_route_model('role', Role::class, $request);

        return ApiResponse::success(
            $role->load('permissions'),
            'Role fetched successfully'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Store Role
    |--------------------------------------------------------------------------
    */

    public function store(StoreRoleRequest $request)
    {
        try {

            DB::beginTransaction();

            $role = $this->service->create($request->validated());

            DB::commit();

            return ApiResponse::success($role, 'Role created successfully');
        } catch (\Throwable $e) {

            DB::rollBack();

            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update Role
    |--------------------------------------------------------------------------
    */

    public function update(UpdateRoleRequest $request)
    {
        try {

            DB::beginTransaction();

            $role = resolve_route_model('role', Role::class, $request);
            $role = $this->service->update($role, $request->validated());

            DB::commit();

            return ApiResponse::success($role, 'Role updated successfully');
        } catch (\Throwable $e) {

            DB::rollBack();

            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Role
    |--------------------------------------------------------------------------
    */

    public function destroy(Request $request)
    {
        try {

            DB::beginTransaction();

            $role = resolve_route_model('role', Role::class, $request);
            $this->service->delete($role);

            DB::commit();

            return ApiResponse::success(null, 'Role deleted successfully');
        } catch (\Throwable $e) {

            DB::rollBack();

            throw $e;
        }
    }
}
