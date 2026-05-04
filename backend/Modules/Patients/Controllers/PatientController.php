<?php

namespace Modules\Patients\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Requests\GeneralRequest;
use Modules\Patients\Models\Patient;
use Modules\Patients\Services\PatientService;
use Modules\Patients\Requests\StorePatientRequest;
use Modules\Patients\Requests\UpdatePatientRequest;
use App\Support\ApiResponse;
use Throwable;

/*
|--------------------------------------------------------------------------
| Patient Controller
|--------------------------------------------------------------------------
|
| Handles HTTP layer only:
| - Delegates logic to PatientService
| - Uses FormRequest validation
| - Returns standardized API responses
|
| IMPORTANT:
| - No business logic here
| - All write operations wrapped in transactions
|
*/

class PatientController extends Controller
{
    protected PatientService $service;

    public function __construct(PatientService $service)
    {
        $this->service = $service;
    }

    /*
    |--------------------------------------------------------------------------
    | List Patients
    |--------------------------------------------------------------------------
    */

    public function index(GeneralRequest $request)
    {
        try {
            $patients = $this->service->get(
                $request->validated(),
                $request->query()
            );

            return ApiResponse::success(
                data: $patients,
                message: 'Patients fetched successfully'
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Failed to fetch patients',
                errors: $e->getMessage()
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Show Patient
    |--------------------------------------------------------------------------
    */

    public function show(Patient $patient)
    {
        try {

            $patient->load([
                'user',
                'identities.media'
            ]);

            return ApiResponse::success(
                data: $patient,
                message: 'Patient fetched successfully'
            );
        } catch (Throwable $e) {

            return ApiResponse::error(
                message: 'Failed to fetch patient',
                errors: $e->getMessage()
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Store Patient
    |--------------------------------------------------------------------------
    */

    public function store(StorePatientRequest $request)
    {
        try {

            DB::beginTransaction();

            $patient = $this->service->create($request->validated());

            DB::commit();

            return ApiResponse::success(
                data: $patient->load(['user', 'identities.media']),
                message: 'Patient created successfully'
            );
        } catch (Throwable $e) {

            DB::rollBack();

            return ApiResponse::error(
                message: 'Patient creation failed',
                errors: $e->getMessage()
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update Patient
    |--------------------------------------------------------------------------
    */

    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        try {

            DB::beginTransaction();

            $patient = $this->service->update($patient, $request->validated());

            DB::commit();

            return ApiResponse::success(
                data: $patient->load(['user', 'identities.media']),
                message: 'Patient updated successfully'
            );
        } catch (Throwable $e) {

            DB::rollBack();

            return ApiResponse::error(
                message: 'Patient update failed',
                errors: $e->getMessage()
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Patient
    |--------------------------------------------------------------------------
    */

    public function destroy(Patient $patient)
    {
        try {

            DB::beginTransaction();

            $patient->delete();

            DB::commit();

            return ApiResponse::success(
                message: 'Patient deleted successfully'
            );
        } catch (Throwable $e) {

            DB::rollBack();

            return ApiResponse::error(
                message: 'Patient deletion failed',
                errors: $e->getMessage()
            );
        }
    }
}