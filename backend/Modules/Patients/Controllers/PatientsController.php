<?php

namespace Modules\Patients\Controllers;

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;
use App\Requests\GeneralRequest;
use Modules\Patients\Models\Patient;
use Modules\Patients\Services\PatientService;
use Modules\Patients\Requests\StorePatientRequest;
use Modules\Patients\Requests\UpdatePatientRequest;
use Modules\Patients\Resources\PatientResource;
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

class PatientsController extends ApiController
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

            return $this->success(
                data: PatientResource::collection($patients),
                message: 'Patients fetched successfully'
            );
        } catch (Throwable $e) {
            return $this->error(
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

    public function show($patient)
    {
        try {

            $patient = $this->service->find($patient);

            $patient->load([
                'user',
                'media',
                'identities.media'
            ]);

            return $this->success(
                data: PatientResource::make($patient),
                message: 'Patient fetched successfully'
            );
        } catch (Throwable $e) {

            return $this->error(
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

            return $this->success(
                data: PatientResource::make($patient),
                message: 'Patient created successfully'
            );
        } catch (Throwable $e) {

            DB::rollBack();

            return $this->error(
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

    public function update(UpdatePatientRequest $request, $id)
    {
        try {

            DB::beginTransaction();

            $patient = $this->service->find($id);
            $patient = $this->service->update($patient, $request->validated());

            DB::commit();

            return $this->success(
                data: PatientResource::make($patient),
                message: 'Patient updated successfully'
            );
        } catch (Throwable $e) {

            DB::rollBack();

            return $this->error(
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

    public function destroy($id)
    {
        try {

            DB::beginTransaction();

            $patient = $this->service->find($id);
            $patient->delete();

            DB::commit();

            return $this->success(
                message: 'Patient deleted successfully'
            );
        } catch (Throwable $e) {

            DB::rollBack();

            return $this->error(
                message: 'Patient deletion failed',
                errors: $e->getMessage()
            );
        }
    }
}
