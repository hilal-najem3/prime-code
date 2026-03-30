<?php

namespace Modules\Settings\Requests;

use Illuminate\Foundation\Http\FormRequest;

/*
|--------------------------------------------------------------------------
| Store Setting Request
|--------------------------------------------------------------------------
|
| Handles validation for creating new settings.
|
*/

class StoreSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization handled by middleware
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => 'required|string|max:191',

            'value' => 'nullable',

            'type' => 'required|in:string,boolean,number,json',

            'group' => 'nullable|string|max:191',

            'is_public' => 'nullable|boolean',
        ];
    }
}
