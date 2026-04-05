<?php

namespace Modules\Plans\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanRequest extends FormRequest
{
    public function rules(): array
    {
        $planId = $this->route('plan')->id;

        return [
            'name' => ['required', 'string'],
            'slug' => ['required', 'string', "unique:plans,slug,{$planId}"],
            'price' => ['required', 'numeric'],

            'description' => ['nullable', 'string'],

            'modules' => ['nullable', 'array'],
            'modules.*' => ['exists:modules,id'],
        ];
    }
}