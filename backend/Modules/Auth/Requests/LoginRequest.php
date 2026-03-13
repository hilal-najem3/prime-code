<?php

namespace Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    */

    public function authorize(): bool
    {
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    */

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:191'],
            'password' => ['required', 'string', 'min:8']
        ];
    }
}