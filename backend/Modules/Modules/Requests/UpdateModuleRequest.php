<?php

namespace Modules\Modules\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateModuleRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->route('module')->id;

        return [
            'name' => ['required', 'string'],
            'slug' => ['required', 'string', "unique:modules,slug,$id"],
            'description' => ['nullable', 'string'],
            'enabled' => ['boolean'],
        ];
    }
}