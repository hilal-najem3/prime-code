<?php

namespace Modules\Settings\Controllers;

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;
use Modules\Settings\Services\SettingsService;
use Modules\Settings\Requests\StoreSettingRequest;
use Modules\Settings\Requests\UpdateSettingRequest;

/*
|--------------------------------------------------------------------------
| Settings Controller
|--------------------------------------------------------------------------
|
| Handles API endpoints for managing settings.
| Business logic is delegated to SettingsService.
|
*/

class SettingsController extends ApiController
{
    protected SettingsService $service;

    public function __construct(SettingsService $service)
    {
        $this->service = $service;
    }

    /*
    |--------------------------------------------------------------------------
    | List Settings
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $settings = $this->service->all();

        return $this->success($settings, 'Settings fetched successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Get Single Setting
    |--------------------------------------------------------------------------
    */

    public function show(string $key)
    {
        $value = $this->service->get($key);

        return $this->success([
            'key' => $key,
            'value' => $value
        ], 'Setting fetched successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Store Setting
    |--------------------------------------------------------------------------
    */

    public function store(StoreSettingRequest $request)
    {
        try {

            DB::beginTransaction();

            $setting = $this->service->set(
                $request->key,
                $request->value,
                $request->type,
                $request->group,
                $request->boolean('is_public')
            );

            DB::commit();

            return $this->success($setting, 'Setting created successfully');
        } catch (\Throwable $e) {

            DB::rollBack();
            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update Setting
    |--------------------------------------------------------------------------
    */

    public function update(UpdateSettingRequest $request, string $key)
    {
        try {

            DB::beginTransaction();

            $setting = $this->service->set(
                $key,
                $request->value,
                $request->input('type', 'string'),
                $request->group,
                $request->boolean('is_public')
            );

            DB::commit();

            return $this->success($setting, 'Setting updated successfully');
        } catch (\Throwable $e) {

            DB::rollBack();
            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Setting
    |--------------------------------------------------------------------------
    */

    public function destroy(string $key)
    {
        try {

            DB::beginTransaction();

            $this->service->forget($key);

            DB::commit();

            return $this->success(null, 'Setting deleted successfully');
        } catch (\Throwable $e) {

            DB::rollBack();
            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Public Settings
    |--------------------------------------------------------------------------
    */

    public function public()
    {
        $settings = $this->service->public();

        return $this->success($settings, 'Public settings fetched');
    }
}