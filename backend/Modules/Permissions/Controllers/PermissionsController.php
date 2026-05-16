<?php

namespace Modules\Permissions\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Permissions\Models\Permission;
use Modules\Permissions\Services\PermissionService;

class PermissionsController extends ApiController
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

        return $this->success($permissions, 'Permissions fetched successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Show Permission
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $permission = $this->service->find($id);

        return $this->success(
            $permission->load('roles'),
            'Permission fetched successfully'
        );
    }
}
