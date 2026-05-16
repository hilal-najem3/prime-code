<?php

namespace Modules\Plans\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'slug' => ['required', 'string', 'unique:plans,slug'],
            'price' => ['required', 'numeric'],

            'description' => ['nullable', 'string'],

            'modules' => ['nullable', 'array'],
            'modules.*' => ['uuid', 'exists:modules,id'],
        ];
    }
}
