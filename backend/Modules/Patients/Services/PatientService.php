<?php

namespace Modules\Patients\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Modules\Patients\Models\Patient;
use Modules\Patients\Models\PatientIdentity;
use Modules\Media\Models\Media;
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
    /*
    |--------------------------------------------------------------------------
    | Create Patient
    |--------------------------------------------------------------------------
    */

    public function create(array $data): Patient
    {
        try {

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

                        /*
                        |--------------------------------------------------------------------------
                        | Attach Media
                        |--------------------------------------------------------------------------
                        */

                        if (!empty($identityData['media'])) {

                            foreach ($identityData['media'] as $file) {

                                // You should replace this with your upload helper
                                $path = $file->store('patients/identities', 'public');

                                $identity->media()->create([
                                    'disk'       => 'public',
                                    'path'       => $path,
                                    'filename'   => $file->getClientOriginalName(),
                                    'extension'  => $file->getClientOriginalExtension(),
                                    'mime_type'  => $file->getMimeType(),
                                    'size'       => $file->getSize(),
                                    'collection' => 'default',
                                    'user_id'    => auth_user()->id,
                                ]);
                            }
                        }
                    }
                }

                return $patient;
            });
        } catch (Throwable $e) {

            Log::error('Patient creation failed', [
                'error' => $e->getMessage(),
                'data'  => $data,
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

                        /*
                        |--------------------------------------------------------------------------
                        | Attach Media (New Files Only)
                        |--------------------------------------------------------------------------
                        */

                        if (!empty($identityData['media'])) {

                            foreach ($identityData['media'] as $file) {

                                $path = $file->store('patients/identities', 'public');

                                $identity->media()->create([
                                    'disk'       => 'public',
                                    'path'       => $path,
                                    'filename'   => $file->getClientOriginalName(),
                                    'extension'  => $file->getClientOriginalExtension(),
                                    'mime_type'  => $file->getMimeType(),
                                    'size'       => $file->getSize(),
                                    'collection' => 'default',
                                    'user_id'    => auth_user()->id,
                                ]);
                            }
                        }
                    }
                }

                return $patient->fresh(['identities.media', 'user']);
            });
        } catch (Throwable $e) {

            Log::error('Patient update failed', [
                'patient_id' => $patient->id,
                'error'      => $e->getMessage(),
                'data'       => $data,
            ]);

            throw $e;
        }
    }
}