<?php

namespace Modules\Tenants\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Tenants\Services\TenantService;
use Modules\Tenants\Requests\CreateTenantRequest;
use App\Requests\GeneralRequest;
use Modules\Tenants\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Modules\Tenants\Requests\UpdateTenantRequest;
use Modules\Tenants\Services\TenantModuleService;
use Modules\Tenants\Requests\SyncTenantModulesRequest;
use Throwable;

class TenantsController extends ApiController
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

        return $this->success(
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

            return $this->success(
                $tenant,
                'Tenant created successfully.'
            );
        } catch (Throwable $e) {

            DB::rollBack();

            return $this->error(
                $e->getMessage()
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Show Tenant
    |--------------------------------------------------------------------------
    */

    public function show(string $id)
    {
        $tenant = $this->service->find($id);
        $tenant = $this->service->show($tenant);

        return $this->success(
            $tenant,
            'Tenant retrieved successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Suspend / Activate Tenant
    |--------------------------------------------------------------------------
    */

    public function update(UpdateTenantRequest $request, $id)
    {
        try {

            DB::beginTransaction();

            $tenant = $this->service->find($id);
            $tenant = $this->service->update(
                $tenant,
                $request->validated()
            );

            DB::commit();

            return $this->success(
                $tenant,
                'Tenant updated successfully.'
            );
        } catch (\Throwable $e) {

            DB::rollBack();

            return $this->error(
                $e->getMessage()
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Tenant
    |--------------------------------------------------------------------------
    */

    public function destroy(string $id)
    {
        try {

            DB::beginTransaction();

            $tenant = $this->service->find($id);
            $tenant->delete();

            DB::commit();

            return $this->success(
                null,
                'Tenant deleted successfully.'
            );
        } catch (Throwable $e) {

            DB::rollBack();

            return $this->error(
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

        return $this->success($modules);
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

            return $this->success($modules, 'Modules updated');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}