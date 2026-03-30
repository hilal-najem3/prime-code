<?php

namespace Modules\Tenants\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTenantRequest extends FormRequest
{
    public function rules(): array
    {
        $tenantId = $this->route('tenant')->id;

        return [
            'name'   => ['required', 'string', 'max:191'],
            'slug'   => ['required', 'string', 'max:191', "unique:tenants,slug,$tenantId"],
            'status' => [
                'required',
                'in:active,suspended,trial,expired'
            ],
            'plan_id' => ['nullable', 'integer'],
            'theme'  => ['nullable', 'string', 'max:191']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
