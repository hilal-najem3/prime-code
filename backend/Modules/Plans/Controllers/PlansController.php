<?php

namespace Modules\Plans\Controllers;

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;
use Modules\Plans\Models\Plan;
use Modules\Plans\Services\PlansService;
use Modules\Plans\Requests\StorePlanRequest;
use Modules\Plans\Requests\UpdatePlanRequest;

class PlansController extends ApiController
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
        return $this->success(
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

            return $this->success($plan, 'Plan created');
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
    public function update(UpdatePlanRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $plan = $this->service->find($id);
            $plan = $this->service->update(
                $plan,
                $request->validated()
            );

            DB::commit();

            return $this->success($plan, 'Plan updated');
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
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $plan = $this->service->find($id);
            $this->service->delete($plan);

            DB::commit();

            return $this->success(null, 'Plan deleted');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
