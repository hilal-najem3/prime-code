<?php

namespace Modules\Modules\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreModuleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'slug' => ['required', 'string', 'unique:modules,slug'],
            'description' => ['nullable', 'string'],
            'enabled' => ['boolean'],
        ];
    }
}