<?php

namespace Modules\Patients\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        $patientId = $this->route('patient'); // assuming route model binding

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

            'address.country' => ['nullable', 'string', 'max:255'],
            'address.city' => ['nullable', 'string', 'max:255'],
            'address.street' => ['nullable', 'string', 'max:255'],
            'address.building' => ['nullable', 'string', 'max:255'],
            'address.floor' => ['nullable', 'string', 'max:50'],
            'address.notes' => ['nullable', 'string'],

            /*
            |--------------------------------------------------------------------------
            | Medical
            |--------------------------------------------------------------------------
            */

            'blood_type' => ['nullable', 'string', 'max:10'],
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
                'unique:users,email,' . optional($this->patient?->user)->id
            ],

            'user.password' => [
                'nullable',
                'string',
                'min:6',
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

            'identities.*.media' => ['nullable', 'array'],
            'identities.*.media.*' => ['file', 'max:5120'],

            /*
            |--------------------------------------------------------------------------
            | Deleted Identities
            |--------------------------------------------------------------------------
            */

            'deleted_identity_ids' => ['nullable', 'array'],
            'deleted_identity_ids.*' => ['exists:patient_identities,id'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([

            'update_user' => filter_var($this->update_user, FILTER_VALIDATE_BOOLEAN),

            'identities' => $this->identities ?? [],

            'address' => $this->address ?? [],

            'deleted_identity_ids' => $this->deleted_identity_ids ?? [],

        ]);
    }
}