<?php

namespace Modules\Languages\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLanguageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Handle authorization via middleware (AccessMiddleware)
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],

            'slug' => [
                'required',
                'string',
                'max:10',
                'alpha_dash',
                'unique:languages,slug',
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
     * Prepare data before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_default' => $this->boolean('is_default'),
            'is_active'  => $this->boolean('is_active', true),
        ]);
    }
}
