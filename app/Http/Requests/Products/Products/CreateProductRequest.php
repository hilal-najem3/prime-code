<?php

namespace App\Http\Requests\Products\Products;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Language;

class CreateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $languages = Language::where('is_active', true)->pluck('code')->toArray();

        $translatedRules = [];

        $rules = [
            'type' => ['required', 'in:simple,classified'],

            // Parent-level fields (parent must have sku, translations etc.)
            'sku' => ['required_if:type,simple', 'string', 'max:100', 'unique:products,sku'],
            'price' => ['required_if:type,simple', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'gte:0'],
            'currency_id' => ['required_if:type,simple', 'integer', 'exists:currencies,id'],
            'quantity' => ['required_if:type,simple', 'integer', 'min:0'],
            'unit' => ['required_if:type,simple', 'string', 'max:50'],

            'thumbnail' => ['required', 'image', 'max:2048'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],

            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:product_categories,id'],

            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:product_tags,id'],

            'is_active' => ['required', 'boolean'],
            'is_featured' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],

            'meta' => ['nullable', 'array'],

            // Children (only required when classified)
            'children' => ['required_if:type,classified', 'array'],
            'children.*.sku' => ['required_if:type,classified', 'string', 'max:100', 'unique:products,sku'],
            'children.*.price' => ['required_if:type,classified', 'numeric', 'min:0'],
            'children.*.sale_price' => ['nullable', 'numeric', 'gte:0'],
            'children.*.currency_id' => ['required_if:type,classified', 'integer', 'exists:currencies,id'],
            'children.*.quantity' => ['required_if:type,classified', 'integer', 'min:0'],
            'children.*.unit' => ['required_if:type,classified', 'string', 'max:50'],
            'children.*.is_active' => ['required_if:type,classified', 'boolean'],

            // attribute values attached to child (array of attribute_value ids)
            'children.*.attribute_values' => ['nullable', 'array'],
            'children.*.attribute_values.*' => ['integer', 'exists:attribute_values,id'],
        ];

        foreach ($languages as $lang) {
            // parent translations are required (parent always has name)
            $translatedRules["name.$lang"] = ['required', 'string', 'max:255'];
            $translatedRules["short_description.$lang"] = ['nullable', 'string'];
            $translatedRules["description.$lang"] = ['nullable', 'string'];
            $translatedRules["details.$lang"] = ['nullable', 'string'];
            $rules["children.*.name.$lang"] = ['required_if:type,classified', 'string', 'max:255'];
        }

        return array_merge($rules, $translatedRules);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            // parent sale_price <= price check (if both provided)
            $price = $this->input('price');
            $sale = $this->input('sale_price');
            if ($price !== null && $sale !== null && floatval($sale) > floatval($price)) {
                $v->errors()->add('sale_price', 'Parent sale_price must be less than or equal to price.');
            }

            // children sale_price <= price checks
            if ($this->input('type') === 'classified') {
                $children = $this->input('children', []);
                foreach ($children as $i => $child) {
                    if (isset($child['price']) && isset($child['sale_price']) && $child['sale_price'] !== null) {
                        if (floatval($child['sale_price']) > floatval($child['price'])) {
                            $v->errors()->add("children.$i.sale_price", "Child #$i sale_price must be less than or equal to its price.");
                        }
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'sku.required' => 'The SKU is required for the parent product.',
            'sku.unique' => 'This SKU is already used.',
            'name.*.required' => 'The name is required for each language.',
            'children.required_if' => 'Children are required when product type is classified.',
        ];
    }
}