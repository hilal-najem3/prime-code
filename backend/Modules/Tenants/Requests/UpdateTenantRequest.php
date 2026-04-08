<?php

namespace Modules\Tenants\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTenantRequest extends FormRequest
{
    public function rules(): array
    {
        $tenantId = $this->route('tenant')->id;

        return [
            'name' => ['required', 'string', 'max:191'],
            'domain' => [
                'required',
                'string',
                'max:191',
                Rule::unique('tenants', 'domain')->ignore($tenantId),
            ],
            'slug' => ['prohibited'],
            'status' => ['sometimes', 'in:active,suspended,trial,expired'],
            'plan_id' => ['nullable', 'integer'],
            'theme' => ['nullable', 'string', 'max:191'],
            'db_password' => ['nullable', 'string'],
            'db_username' => ['nullable', 'string']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
