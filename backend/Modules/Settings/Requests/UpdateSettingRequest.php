<?php

namespace Modules\Settings\Requests;

use Illuminate\Foundation\Http\FormRequest;

/*
|--------------------------------------------------------------------------
| Update Setting Request
|--------------------------------------------------------------------------
|
| Handles validation for updating existing settings.
|
*/

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // key usually comes from route → optional here
            'value' => 'nullable',

            'type' => 'sometimes|in:string,boolean,number,json',

            'group' => 'nullable|string|max:255',

            'is_public' => 'nullable|boolean',
        ];
    }
}