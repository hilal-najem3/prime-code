<?php

namespace Modules\Permissions\Controllers;

use Illuminate\Routing\Controller;
use Modules\Permissions\Models\Permission;
use Modules\Permissions\Services\PermissionService;
use App\Support\ApiResponse;

class PermissionsController extends Controller
{
    protected PermissionService $service;

    public function __construct(PermissionService $service)
    {
        $this->service = $service;
    }

    /*
    |--------------------------------------------------------------------------
    | List Permissions
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $permissions = $this->service->get();

        return ApiResponse::success($permissions, 'Permissions fetched successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Show Permission
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $permission = $this->service->find($id);

        return ApiResponse::success(
            $permission->load('roles'),
            'Permission fetched successfully'
        );
    }
}
