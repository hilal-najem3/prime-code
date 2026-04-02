<?php

namespace Modules\Tenants\Controllers;

use Modules\Tenants\Services\TenantService;
use Modules\Tenants\Requests\CreateTenantRequest;
use App\Requests\GeneralRequest;
use Modules\Tenants\Models\Tenant;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\DB;
use Modules\Tenants\Requests\UpdateTenantRequest;
use Modules\Tenants\Services\TenantModuleService;
use Modules\Tenants\Requests\SyncTenantModulesRequest;
use Throwable;

class TenantsController
{
    public function __construct(
        protected TenantService $service,
        protected TenantModuleService $moduleService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | List Tenants
    |--------------------------------------------------------------------------
    */

    public function index(GeneralRequest $request)
    {
        $tenants = $this->service->get(
            $request->validated(),
            $request->query()
        );

        return ApiResponse::success(
            $tenants,
            'Tenants fetched successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create Tenant
    |--------------------------------------------------------------------------
    */

    public function store(CreateTenantRequest $request)
    {
        try {

            DB::beginTransaction();

            $tenant = $this->service->create(
                $request->validated()
            );

            DB::commit();

            return ApiResponse::success(
                $tenant,
                'Tenant created successfully.'
            );
        } catch (Throwable $e) {

            DB::rollBack();

            return ApiResponse::error(
                $e->getMessage()
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Show Tenant
    |--------------------------------------------------------------------------
    */

    public function show(Tenant $tenant)
    {
        return ApiResponse::success(
            $tenant,
            'Tenant retrieved successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Suspend / Activate Tenant
    |--------------------------------------------------------------------------
    */

    public function update(UpdateTenantRequest $request, Tenant $tenant)
    {
        try {

            DB::beginTransaction();

            $tenant = $this->service->update(
                $tenant,
                $request->validated()
            );

            DB::commit();

            return ApiResponse::success(
                $tenant,
                'Tenant updated successfully.'
            );
        } catch (\Throwable $e) {

            DB::rollBack();

            return ApiResponse::error(
                $e->getMessage()
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Tenant
    |--------------------------------------------------------------------------
    */

    public function destroy(Tenant $tenant)
    {
        try {

            DB::beginTransaction();

            $tenant->delete();

            DB::commit();

            return ApiResponse::success(
                null,
                'Tenant deleted successfully.'
            );
        } catch (Throwable $e) {

            DB::rollBack();

            return ApiResponse::error(
                $e->getMessage()
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Modules
    |--------------------------------------------------------------------------
    */
    public function modules(Tenant $tenant)
    {
        $modules = $this->moduleService->getModules($tenant);

        return ApiResponse::success($modules);
    }

    public function syncModules(SyncTenantModulesRequest $request, Tenant $tenant)
    {
        try {
            DB::beginTransaction();

            $modules = $this->moduleService->syncModules(
                $tenant,
                $request->validated()['modules']
            );

            DB::commit();

            return ApiResponse::success($modules, 'Modules updated');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}