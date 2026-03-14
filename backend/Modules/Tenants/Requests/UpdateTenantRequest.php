<?php

namespace Modules\Tenants\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTenantRequest extends FormRequest
{
    public function rules(): array
    {
        $tenantId = $this->route('tenant')->id;

        return [
            'name'   => ['required', 'string', 'max:255'],
            'slug'   => ['required', 'string', 'max:255', "unique:tenants,slug,$tenantId"],
            'active' => ['boolean'],
            'plan_id' => ['nullable', 'integer'],
            'theme'  => ['nullable', 'string', 'max:255']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}