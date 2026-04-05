<?php

namespace Modules\Plans\Controllers;

use Illuminate\Support\Facades\DB;
use Modules\Plans\Models\Plan;
use Modules\Plans\Services\PlansService;
use Modules\Plans\Requests\StorePlanRequest;
use Modules\Plans\Requests\UpdatePlanRequest;
use App\Support\ApiResponse;

class PlansController
{
    public function __construct(
        protected PlansService $service
    ) {}

    /*
    |--------------------------------------------------------------------------
    | List Plans
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        return ApiResponse::success(
            $this->service->getAll()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Store Plan
    |--------------------------------------------------------------------------
    */
    public function store(StorePlanRequest $request)
    {
        try {
            DB::beginTransaction();

            $plan = $this->service->create(
                $request->validated()
            );

            DB::commit();

            return ApiResponse::success($plan, 'Plan created');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update Plan
    |--------------------------------------------------------------------------
    */
    public function update(UpdatePlanRequest $request, Plan $plan)
    {
        try {
            DB::beginTransaction();

            $plan = $this->service->update(
                $plan,
                $request->validated()
            );

            DB::commit();

            return ApiResponse::success($plan, 'Plan updated');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Plan
    |--------------------------------------------------------------------------
    */
    public function destroy(Plan $plan)
    {
        try {
            DB::beginTransaction();

            $this->service->delete($plan);

            DB::commit();

            return ApiResponse::success(null, 'Plan deleted');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}