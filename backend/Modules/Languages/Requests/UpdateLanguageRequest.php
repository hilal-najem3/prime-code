<?php

namespace Modules\Languages\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLanguageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        $languageId = $this->route('language')?->id ?? $this->route('language');

        return [
            'name' => ['required', 'string', 'max:100'],

            'slug' => [
                'required',
                'string',
                'max:10',
                'alpha_dash',
                Rule::unique('languages', 'slug')->ignore($languageId),
            ],

            'direction' => [
                'required',
                'in:ltr,rtl',
            ],

            'is_default' => ['sometimes', 'boolean'],
            'is_active'  => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Normalize input.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_default' => $this->boolean('is_default'),
            'is_active'  => $this->boolean('is_active'),
        ]);
    }
}
