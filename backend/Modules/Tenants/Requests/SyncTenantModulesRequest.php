<?php

namespace Modules\Tenants\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncTenantModulesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'modules' => ['required', 'array'],
            'modules.*' => ['exists:modules,id'],
        ];
    }
}