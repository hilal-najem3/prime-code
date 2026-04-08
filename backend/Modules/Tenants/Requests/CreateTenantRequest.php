<?php

namespace Modules\Tenants\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTenantRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'slug' => [
                'required',
                'string',
                'max:191',
                Rule::unique('tenants', 'slug')->whereNull('deleted_at'),
            ],
            'domain' => ['required', 'string', 'max:191'],
            'db_username' => ['required', 'string'],
            'db_password' => ['nullable', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
