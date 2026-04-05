<?php

namespace Modules\Subscriptions\Services;

use Modules\Subscriptions\Models\Subscription;
use Modules\Tenants\Models\Tenant;
use Modules\Plans\Models\Plan;

class SubscriptionsService
{
    /*
    |--------------------------------------------------------------------------
    | Assign Plan to Tenant
    |--------------------------------------------------------------------------
    */
    public function assignPlan(Tenant $tenant, Plan $plan): Subscription
    {
        // deactivate existing subscription
        $tenant->subscription()?->update([
            'status' => 'expired',
        ]);

        $subscription = Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'start_date' => now(),
            'status' => 'active',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Sync Modules Automatically
        |--------------------------------------------------------------------------
        */
        $moduleIds = $plan->modules()->pluck('modules.id')->toArray();

        $tenant->modules()->sync($moduleIds);

        return $subscription->load('plan.modules');
    }
}