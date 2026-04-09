<?php

namespace Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required'],
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/'
            ],
            'password_confirmation' => ['required', 'same:password'],
        ];
    }

    protected function passedValidation()
    {
        if (!Hash::check(
            $this->current_password,
            auth_user()->password
        )) {
            throw ValidationException::withMessages([
                'current_password' => ['Current password is incorrect.']
            ]);
        }
    }
}
