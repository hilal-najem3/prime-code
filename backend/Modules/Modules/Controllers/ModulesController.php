<?php

namespace Modules\Modules\Controllers;

use Illuminate\Support\Facades\DB;
use App\Support\ApiResponse;
use Modules\Modules\Models\Module;
use Modules\Modules\Services\ModuleService;
use Modules\Modules\Requests\StoreModuleRequest;
use Modules\Modules\Requests\UpdateModuleRequest;

class ModulesController
{
    public function __construct(
        protected ModuleService $service
    ) {}

    public function index()
    {
        $modules = $this->service->getAll();

        return ApiResponse::success($modules, 'Modules fetched successfully');
    }

    public function store(StoreModuleRequest $request)
    {
        try {
            DB::beginTransaction();

            $module = $this->service->create($request->validated());

            DB::commit();

            return ApiResponse::success($module, 'Module created');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show($id)
    {
        $module = $this->service->find($id);
        return ApiResponse::success($module);
    }

    public function update(UpdateModuleRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $module = $this->service->find($id);
            $module = $this->service->update($module, $request->validated());

            DB::commit();

            return ApiResponse::success($module, 'Module updated');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $module = $this->service->find($id);
            $this->service->delete($module);

            DB::commit();

            return ApiResponse::success(null, 'Module deleted');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
