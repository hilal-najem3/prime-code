<?php

namespace App\Http\Requests\Products\Attributes;

use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;

class CreateAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $languages = Language::where('is_active', true)->pluck('code')->toArray();

        $translatedRules = [];
        foreach ($languages as $lang) {
            $translatedRules["name.$lang"] = ['required', 'string', 'max:255'];
        }

        $valuesTranslatedRules = [];
        foreach ($languages as $lang) {
            $valuesTranslatedRules["values.*.value.$lang"] = ['required', 'string', 'max:255'];
        }


        // color regex: supports #RGB or #RRGGBB
        $colorRule = ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'];

        $valuesRules = [
            'values' => ['sometimes', 'array'],
            'values.*.value' => ['required'],
            'values.*.color' => $colorRule,
        ];

        return array_merge([
            'type' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'is_required' => ['sometimes', 'boolean'],
            'is_filterable' => ['sometimes', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ], $translatedRules, $valuesRules, $valuesTranslatedRules);
    }

    public function messages(): array
    {
        return [
            'name.*.required' => 'The name is required for each language.',
            'values.*.value.required' => 'Each attribute value is required.',
            'values.*.color.regex' => 'The color must be a valid hex color (e.g. #ff0000).',
        ];
    }
}