<?php

namespace Modules\Patients\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Modules\Patients\Models\Patient;

/*
|--------------------------------------------------------------------------
| Update Patient Request
|--------------------------------------------------------------------------
|
| Handles validation for updating patients.
|
*/

class UpdatePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // handled by middleware
    }

    public function rules(): array
    {
        $patientUserId = $this->patientUserId();

        $userEmailUnique = $patientUserId
            ? Rule::unique('users', 'email')->ignore($patientUserId)
            : Rule::unique('users', 'email');

        return [

            /*
            |--------------------------------------------------------------------------
            | Basic Info
            |--------------------------------------------------------------------------
            */

            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name'  => ['sometimes', 'required', 'string', 'max:255'],

            'gender' => ['nullable', 'in:male,female'],

            'date_of_birth' => ['nullable', 'date'],

            /*
            |--------------------------------------------------------------------------
            | Contact Info
            |--------------------------------------------------------------------------
            */

            'phone' => ['nullable', 'string', 'max:20'],
            'phone_secondary' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],

            /*
            |--------------------------------------------------------------------------
            | Address
            |--------------------------------------------------------------------------
            */

            'address' => ['nullable', 'array'],

            // Supports multiple addresses: address[0][country], address[1][...]
            'address.*' => ['nullable', 'array'],

            'address.*.country' => ['nullable', 'string', 'max:255'],
            'address.*.city' => ['nullable', 'string', 'max:255'],
            'address.*.street' => ['nullable', 'string', 'max:255'],
            'address.*.building' => ['nullable', 'string', 'max:255'],
            'address.*.floor' => ['nullable', 'string', 'max:50'],
            'address.*.notes' => ['nullable', 'string'],

            /*
            |--------------------------------------------------------------------------
            | Medical
            |--------------------------------------------------------------------------
            */

            'blood_type' => ['nullable', 'string', 'max:10', Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
            'allergies' => ['nullable', 'string'],

            /*
            |--------------------------------------------------------------------------
            | System
            |--------------------------------------------------------------------------
            */

            'status' => ['nullable', 'in:active,inactive'],
            'notes' => ['nullable', 'string'],

            /*
            |--------------------------------------------------------------------------
            | User Update (Optional)
            |--------------------------------------------------------------------------
            */

            'update_user' => ['nullable', 'boolean'],

            'user.email' => [
                'required_if:update_user,true',
                'email',
                'max:255',
                $userEmailUnique,
            ],

            'user.password' => [
                'nullable',
                'string',
                'min:6',
            ],

            'allow_login' => [
                'nullable',
                'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | Identities (Create / Update)
            |--------------------------------------------------------------------------
            */

            'identities' => ['nullable', 'array'],

            'identities.*.id' => ['nullable', 'exists:patient_identities,id'],

            'identities.*.type' => [
                'required_with:identities',
                'in:id_card,passport,driver_license'
            ],

            'identities.*.number' => ['nullable', 'string', 'max:255'],

            'identities.*.issued_at' => ['nullable', 'date'],
            'identities.*.expires_at' => ['nullable', 'date'],

            'identities.*.notes' => ['nullable', 'string'],

            /*
            |--------------------------------------------------------------------------
            | Identity Media
            |--------------------------------------------------------------------------
            */

            'identities.*.media_ids' => [
                'nullable',
                'array',
            ],

            'identities.*.media_ids.*' => [
                'integer',
                'exists:media,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Deleted Identities
            |--------------------------------------------------------------------------
            */

            'deleted_identity_ids' => ['nullable', 'array'],
            'deleted_identity_ids.*' => ['exists:patient_identities,id'],

        ];
    }

    protected function patientUserId(): ?int
    {
        $patient = $this->routePatient();

        return $patient?->user?->id;
    }

    protected function prepareForValidation()
    {
        $address = $this->address;
        $normalizedAddress = $address;

        // Backward compatible: accept a single address object and normalize to array of rows.
        if (is_array($address) && array_key_exists('country', $address)) {
            $normalizedAddress = [$address];
        }

        $updateUser = $this->has('update_user')
            ? filter_var($this->update_user, FILTER_VALIDATE_BOOLEAN)
            : null;

        $prepared = [

            'update_user' => $updateUser,

            'identities' => $this->identities ?? [],

            'address' => $normalizedAddress ?? [],

            'deleted_identity_ids' => $this->deleted_identity_ids ?? [],

        ];

        if ($this->has('first_name')) {
            $prepared['first_name'] = is_string($this->first_name)
                ? trim($this->first_name)
                : $this->first_name;
        }

        if ($this->has('last_name')) {
            $prepared['last_name'] = is_string($this->last_name)
                ? trim($this->last_name)
                : $this->last_name;
        }

        $this->merge($prepared);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $patient = $this->routePatient();

            if (!$patient) {
                return;
            }

            $firstName = $this->input('first_name', $patient->first_name);
            $lastName = $this->input('last_name', $patient->last_name);

            if (!$firstName || !$lastName) {
                return;
            }

            $exists = Patient::query()
                ->whereFullName($firstName, $lastName)
                ->whereKeyNot($patient->id)
                ->exists();

            if ($exists) {
                $validator->errors()->add(
                    'first_name',
                    'The full name has already been taken.'
                );
            }
        });
    }

    protected function routePatient(): ?Patient
    {
        $patient = $this->route('patient');

        if ($patient instanceof Patient) {
            return $patient;
        }

        return $patient ? Patient::find($patient) : null;
    }
}
