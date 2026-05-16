<?php

namespace Modules\Patients\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Modules\Patients\Models\Patient;
use Modules\Patients\Models\PatientIdentity;
use Modules\Media\Models\Media;
use Modules\Media\Services\MediaService;
use Modules\Auth\Models\User;
use Throwable;

/*
|--------------------------------------------------------------------------
| Patient Service
|--------------------------------------------------------------------------
|
| Handles all business logic related to patients:
| - Create patient (with optional user)
| - Update patient
| - Manage identities (create/update/delete)
| - Attach media to identities
|
| IMPORTANT:
| - All DB operations are transactional
| - No logic should exist in controllers
| - Events should be used ONLY for side effects
|
*/

class PatientService
{
    private const PATIENT_MEDIA_COLLECTION = 'patient';
    private const IDENTITY_MEDIA_COLLECTION = 'identity';

    public function __construct(
        protected MediaService $mediaService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Get Patients
    |--------------------------------------------------------------------------
    */

    public function get(array $filters = [], array $queryParams = [])
    {
        $perPage = $filters['per_page'] ?? null;
        $search = trim($filters['search'] ?? '');
        $sort = $filters['sort'] ?? 'id';
        $direction = strtolower($filters['direction'] ?? 'asc');

        $sort = match ($sort) {
            'name', 'full_name' => 'first_name',
            'email', 'status', 'created_at', 'phone', 'blood_type' => $sort,
            default => 'id',
        };

        $query = Patient::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('blood_type', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction);

        return $perPage
            ? $query->paginate($perPage)->appends($queryParams)
            : $query->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Get Patient by id
    |--------------------------------------------------------------------------
    */

    public function find($id): Patient
    {
        return Patient::findOrFail($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Create Patient
    |--------------------------------------------------------------------------
    */

    public function create(array $data): Patient
    {
        try {

            $this->logRequest('Patient store request received', $data);

            return DB::transaction(function () use ($data) {

                /*
                |--------------------------------------------------------------------------
                | Create User (Optional)
                |--------------------------------------------------------------------------
                */

                $user = null;

                if (!empty($data['create_user'])) {

                    $user = User::create([
                        'email'    => $data['user']['email'],
                        'password' => Hash::make($data['user']['password']),
                    ]);

                    // Assign role (assuming role system exists)
                    if (method_exists($user, 'assignRole')) {
                        $user->assignRole('patient');
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Create Patient
                |--------------------------------------------------------------------------
                */

                $patient = Patient::create([
                    'user_id'          => $user?->id,
                    'first_name'       => $data['first_name'],
                    'last_name'        => $data['last_name'],
                    'gender'           => $data['gender'] ?? null,
                    'date_of_birth'    => $data['date_of_birth'] ?? null,
                    'phone'            => $data['phone'] ?? null,
                    'phone_secondary'  => $data['phone_secondary'] ?? null,
                    'email'            => $data['email'] ?? null,
                    'address'          => $data['address'] ?? null,
                    'blood_type'       => $data['blood_type'] ?? null,
                    'allergies'        => $data['allergies'] ?? null,
                    'status'           => $data['status'] ?? 'active',
                    'notes'            => $data['notes'] ?? null,
                ]);

                if (array_key_exists('media_ids', $data)) {
                    $this->syncMedia(
                        $patient,
                        $data['media_ids'],
                        self::PATIENT_MEDIA_COLLECTION
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Identities Handling
                |--------------------------------------------------------------------------
                */

                if (!empty($data['identities'])) {

                    foreach ($data['identities'] as $identityData) {

                        $identity = $patient->identities()->create([
                            'type'       => $identityData['type'],
                            'number'     => $identityData['number'] ?? null,
                            'issued_at'  => $identityData['issued_at'] ?? null,
                            'expires_at' => $identityData['expires_at'] ?? null,
                            'notes'      => $identityData['notes'] ?? null,
                        ]);

                        if (array_key_exists('media_ids', $identityData)) {
                            $this->syncMedia(
                                $identity,
                                $identityData['media_ids'],
                                self::IDENTITY_MEDIA_COLLECTION
                            );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Attach Media
                        |--------------------------------------------------------------------------
                        */

                        if (!empty($identityData['media'])) {

                            foreach ($identityData['media'] as $file) {

                                if (!$file instanceof UploadedFile) {
                                    continue;
                                }

                                $this->mediaService->upload(
                                    $file,
                                    'patients/identities',
                                    'identity_document',
                                    $identity,
                                    auth_user()->id ?? null,
                                    'private'
                                );
                            }
                        }
                    }
                }

                return $patient->fresh(['media', 'identities.media', 'user']);
            });
        } catch (Throwable $e) {

            logger()->error('Patient creation failed', [
                'error' => $e->getMessage(),
                'data'  => $this->sanitizeLogData($data),
            ]);

            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update Patient
    |--------------------------------------------------------------------------
    */

    public function update(Patient $patient, array $data): Patient
    {
        try {

            $this->logRequest('Patient update request received', $data, [
                'patient_id' => $patient->id,
            ]);

            return DB::transaction(function () use ($patient, $data) {

                /*
                |--------------------------------------------------------------------------
                | Update Patient Core Data
                |--------------------------------------------------------------------------
                */

                $patient->update([
                    'first_name'       => $data['first_name'] ?? $patient->first_name,
                    'last_name'        => $data['last_name'] ?? $patient->last_name,
                    'gender'           => $data['gender'] ?? $patient->gender,
                    'date_of_birth'    => $data['date_of_birth'] ?? $patient->date_of_birth,
                    'phone'            => $data['phone'] ?? $patient->phone,
                    'phone_secondary'  => $data['phone_secondary'] ?? $patient->phone_secondary,
                    'email'            => $data['email'] ?? $patient->email,
                    'address'          => $data['address'] ?? $patient->address,
                    'blood_type'       => $data['blood_type'] ?? $patient->blood_type,
                    'allergies'        => $data['allergies'] ?? $patient->allergies,
                    'status'           => $data['status'] ?? $patient->status,
                    'notes'            => $data['notes'] ?? $patient->notes,
                ]);

                if (array_key_exists('media_ids', $data)) {
                    $this->syncMedia(
                        $patient,
                        $data['media_ids'],
                        self::PATIENT_MEDIA_COLLECTION
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Update User (Optional)
                |--------------------------------------------------------------------------
                */

                if (!empty($data['update_user']) && $patient->user) {

                    $patient->user->update([
                        'email' => $data['user']['email'] ?? $patient->user->email,
                        'password' => !empty($data['user']['password'])
                            ? Hash::make($data['user']['password'])
                            : $patient->user->password,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Delete Identities
                |--------------------------------------------------------------------------
                */

                if (!empty($data['deleted_identity_ids'])) {

                    $patient->identities()
                        ->whereIn('id', $data['deleted_identity_ids'])
                        ->each(function ($identity) {
                            $identity->delete();
                        });
                }

                /*
                |--------------------------------------------------------------------------
                | Create / Update Identities
                |--------------------------------------------------------------------------
                */

                if (!empty($data['identities'])) {

                    foreach ($data['identities'] as $identityData) {

                        // Update existing
                        if (!empty($identityData['id'])) {

                            $identity = $patient->identities()
                                ->where('id', $identityData['id'])
                                ->firstOrFail();

                            $identity->update([
                                'type'       => $identityData['type'],
                                'number'     => $identityData['number'] ?? null,
                                'issued_at'  => $identityData['issued_at'] ?? null,
                                'expires_at' => $identityData['expires_at'] ?? null,
                                'notes'      => $identityData['notes'] ?? null,
                            ]);
                        } else {

                            // Create new
                            $identity = $patient->identities()->create([
                                'type'       => $identityData['type'],
                                'number'     => $identityData['number'] ?? null,
                                'issued_at'  => $identityData['issued_at'] ?? null,
                                'expires_at' => $identityData['expires_at'] ?? null,
                                'notes'      => $identityData['notes'] ?? null,
                            ]);
                        }

                        if (array_key_exists('media_ids', $identityData)) {
                            $this->syncMedia(
                                $identity,
                                $identityData['media_ids'],
                                self::IDENTITY_MEDIA_COLLECTION
                            );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Attach Media (New Files Only)
                        |--------------------------------------------------------------------------
                        */

                        if (!empty($identityData['media'])) {

                            foreach ($identityData['media'] as $file) {

                                if (!$file instanceof UploadedFile) {
                                    continue;
                                }

                                $this->mediaService->upload(
                                    $file,
                                    'patients/identities',
                                    'identity_document',
                                    $identity,
                                    auth_user()->id ?? null,
                                    'private'
                                );
                            }
                        }
                    }
                }

                return $patient->fresh(['media', 'identities.media', 'user']);
            });
        } catch (Throwable $e) {

            logger()->error('Patient update failed', [
                'patient_id' => $patient->id,
                'error'      => $e->getMessage(),
                'data'       => $this->sanitizeLogData($data),
            ]);

            throw $e;
        }
    }

    protected function syncMedia($model, ?array $mediaIds, string $collection): void
    {
        $mediaIds = collect($mediaIds ?? [])
            ->filter(fn ($id) => $id !== null && $id !== '')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $model->media()
            ->when(!empty($mediaIds), fn ($query) => $query->whereNotIn('id', $mediaIds))
            ->update([
                'model_type' => null,
                'model_id' => null,
            ]);

        if (empty($mediaIds)) {
            return;
        }

        Media::whereIn('id', $mediaIds)
            ->get()
            ->each(function (Media $media) use ($model, $collection) {
                $this->mediaService->attach($media, $model, $collection);
            });
    }

    protected function logRequest(string $message, array $data, array $context = []): void
    {
        $request = request();

        logger()->info($message, array_merge([
            'user_id' => user_id(),
            'tenant_id' => tenant_id(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'data' => $this->sanitizeLogData($data),
        ], $context));
    }

    protected function sanitizeLogData(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sanitizeLogData($value);
                continue;
            }

            if (str_contains(strtolower((string) $key), 'password')) {
                $data[$key] = '[redacted]';
            }
        }

        return $data;
    }
}
