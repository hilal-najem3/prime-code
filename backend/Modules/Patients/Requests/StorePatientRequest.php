<?php

namespace Modules\Patients\Requests;

use Illuminate\Foundation\Http\FormRequest;

/*
|--------------------------------------------------------------------------
| Store Patient Request
|--------------------------------------------------------------------------
|
| Handles validation for creating patients.
|
*/

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // handled by middleware
    }

    public function rules(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Basic Info
            |--------------------------------------------------------------------------
            */

            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],

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
            | Medical Basics
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
            | User Account (Optional)
            |--------------------------------------------------------------------------
            */

            'create_user' => ['nullable', 'boolean'],

            'user.email' => [
                'required_if:create_user,true',
                'email',
                'max:255',
                'unique:users,email'
            ],

            'user.password' => [
                'required_if:create_user,true',
                'string',
                'min:6',
            ],

            /*
            |--------------------------------------------------------------------------
            | Identities
            |--------------------------------------------------------------------------
            */

            'identities' => ['nullable', 'array'],

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
            | Media (per identity)
            |--------------------------------------------------------------------------
            */

            'identities.*.media' => ['nullable', 'array'],

            'identities.*.media.*' => ['file', 'max:5120'], // 5MB
        ];
    }

    protected function prepareForValidation()
    {
        $address = $this->address;
        $normalizedAddress = $address;

        // Backward compatible: accept a single address object and normalize to array of rows.
        if (is_array($address) && array_key_exists('country', $address)) {
            $normalizedAddress = [$address];
        }

        $this->merge([

            'create_user' => filter_var($this->create_user, FILTER_VALIDATE_BOOLEAN),

            'identities' => $this->identities ?? [],

            'address' => $normalizedAddress ?? [],

        ]);
    }
}