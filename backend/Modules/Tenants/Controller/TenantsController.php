<?php

namespace Modules\Tenants\Controllers;

use Modules\Tenants\Services\TenantService;
use Modules\Tenants\Requests\CreateTenantRequest;
use Modules\Tenants\Models\Tenant;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\DB;
use Modules\Tenants\Requests\UpdateTenantRequest;
use Throwable;

class TenantsController
{
    public function __construct(
        protected TenantService $service
    ) {}

    /*
    |--------------------------------------------------------------------------
    | List Tenants
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $tenants = Tenant::query()->paginate();

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

            $tenant->update($request->validated());

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
}