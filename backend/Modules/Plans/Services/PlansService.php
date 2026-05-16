<?php

namespace Modules\Plans\Services;

use Modules\Plans\Models\Plan;

class PlansService
{
    /*
    |--------------------------------------------------------------------------
    | Find Plan
    |--------------------------------------------------------------------------
    */

    public function find(string $id): Plan
    {
        return Plan::findOrFail($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Get All Plans
    |--------------------------------------------------------------------------
    */
    public function getAll()
    {
        return Plan::with('modules')
            ->latest()
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Create Plan
    |--------------------------------------------------------------------------
    */
    public function create(array $data): Plan
    {
        $modules = $data['modules'] ?? [];

        unset($data['modules']);

        $plan = Plan::create($data);

        if (!empty($modules)) {
            $plan->modules()->sync($modules);
        }

        return $plan->load('modules');
    }

    /*
    |--------------------------------------------------------------------------
    | Update Plan
    |--------------------------------------------------------------------------
    */
    public function update(Plan $plan, array $data): Plan
    {
        $modules = $data['modules'] ?? null;

        unset($data['modules']);

        $plan->update($data);

        if ($modules !== null) {
            $plan->modules()->sync($modules);
        }

        return $plan->load('modules');
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Plan
    |--------------------------------------------------------------------------
    */
    public function delete(Plan $plan): void
    {
        $plan->delete();
    }
}
