<?php

namespace App\Http\Requests\Products\Attributes;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Language;
use App\Models\Attribute;

class UpdateAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Get attribute id (route binding may provide model or id)
        $routeAttr = $this->route('attribute');
        $attributeId = is_object($routeAttr) ? $routeAttr->id : $routeAttr;

        $languages = Language::where('is_active', true)->pluck('code')->toArray();

        $translatedRules = [];
        foreach ($languages as $lang) {
            $translatedRules["name.$lang"] = ['required', 'string', 'max:255'];
        }

        $valuesTranslatedRules = [];
        foreach ($languages as $lang) {
            $valuesTranslatedRules["values.*.value.$lang"] = ['required', 'string', 'max:255'];
        }

        $attribute = Attribute::findOrFail($attributeId);

        // color regex: supports #RGB or #RRGGBB
        $colorRule = ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'];

        $valuesRules = [
            'values' => ['sometimes', 'array'],
            // if provided, ensure the id exists and belongs to this attribute
            'values.*.id' => ['sometimes', 'integer', "exists:attribute_values,id,attribute_id,{$attributeId}"],
            'values.*.value' => ['required'],
            'values.*.color' => $colorRule,
        ];

        return array_merge([
            'type' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'is_required' => ['sometimes', 'boolean'],
            'is_filterable' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ], $translatedRules, $valuesRules, $valuesTranslatedRules);
    }

    public function messages(): array
    {
        return [
            'name.*.required' => 'Name is required for all active languages.',
            'values.*.value.required' => 'Each attribute value is required.',
            'values.*.color.regex' => 'The color must be a valid hex color (e.g. #ff0000).',
            'values.*.id.exists' => 'One of the provided value IDs is invalid for this attribute.',
        ];
    }
}