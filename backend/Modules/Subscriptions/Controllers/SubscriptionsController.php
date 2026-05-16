<?php

namespace Modules\Subscriptions\Controllers;

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;
use Modules\Tenants\Models\Tenant;
use Modules\Plans\Models\Plan;
use Modules\Subscriptions\Services\SubscriptionsService;
use Modules\Subscriptions\Requests\AssignPlanRequest;

class SubscriptionsController extends ApiController
{
    public function __construct(
        protected SubscriptionsService $service
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Assign Plan
    |--------------------------------------------------------------------------
    */
    public function assign(AssignPlanRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $tenant = Tenant::findOrFail($id);
            $plan = Plan::findOrFail($request->validated()['plan_id']);

            $subscription = $this->service->assignPlan($tenant, $plan);

            DB::commit();

            return $this->success(
                $subscription,
                'Plan assigned successfully'
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
