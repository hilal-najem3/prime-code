<?php

namespace Modules\Tenants\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTenantRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'slug' => ['required', 'string', 'max:191', 'unique:tenants,slug'],
            'domain' => ['required', 'string', 'max:191']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
