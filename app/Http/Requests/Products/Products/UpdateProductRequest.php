<?php

namespace App\Http\Requests\Products\Products;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Language;
use App\Models\Product;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product'); // parent product id
        $languages = Language::where('is_active', true)->pluck('code')->toArray();

        $translatedRules = [];

        $product = Product::findOrFail($productId);
        $mediaIds = $product->media->where('id', '!=', $product->thumbnail_id)->pluck('id')->toArray();

        $rules = [
            'type' => ['required', 'in:simple,classified'],

            'sku' => ['required', 'string', 'max:100', 'unique:products,sku,' . $productId],
            'price' => ['required_if:type,simple', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'gte:0'],
            'currency_id' => ['required_if:type,simple', 'integer', 'exists:currencies,id'],
            'quantity' => ['required_if:type,simple', 'integer', 'min:0'],
            'unit' => ['required_if:type,simple', 'string', 'max:50'],

            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],

            'existing_gallery_images' => ['nullable', 'array'],
            'existing_gallery_images.*' => ['integer', function ($attribute, $value, $fail) use ($mediaIds) {
                if (!in_array($value, $mediaIds)) {
                    $fail("The selected gallery image [$value] is invalid or not associated with this product.");
                }
            }],

            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:product_categories,id'],

            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:product_tags,id'],

            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
            'is_featured' => ['required', 'boolean'],

            'meta' => ['nullable', 'array'],

            'deleted_media' => ['nullable', 'string', function ($attribute, $value, $fail) use ($mediaIds) {
                $ids = array_filter(explode(',', $value), fn($id) => trim($id) !== '');
                foreach ($ids as $id) {
                    if (!in_array($id, $mediaIds)) {
                        $fail("The selected media ID [$id] is invalid or not deletable.");
                    }
                }
            }],

            // children rules
            'children' => ['nullable', 'array'],
            'children.*.id' => ['nullable', 'integer', 'exists:products,id'],
            'children.*.sku' => ['required_if:type,classified', 'string', 'max:100'],
            'children.*.price' => ['required_if:type,classified', 'numeric', 'min:0'],
            'children.*.sale_price' => ['nullable', 'numeric', 'gte:0'],
            'children.*.currency_id' => ['required_if:type,classified', 'integer', 'exists:currencies,id'],
            'children.*.quantity' => ['required_if:type,classified', 'integer', 'min:0'],
            'children.*.unit' => ['required_if:type,classified', 'string', 'max:50'],
            'children.*.is_active' => ['required_if:type,classified', 'boolean'],
            // Add under children rules
            'children.*.thumbnail' => ['nullable'],
            'children.*.attribute_values' => ['nullable', 'array'],
            'children.*.attribute_values.*' => ['integer', 'exists:attribute_values,id'],
        ];

        foreach ($languages as $lang) {
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
            $parentId = $this->route('product');

            // parent sale_price <= price
            $price = $this->input('price');
            $sale = $this->input('sale_price');
            if ($price !== null && $sale !== null && floatval($sale) > floatval($price)) {
                $v->errors()->add('sale_price', 'Parent sale_price must be less than or equal to price.');
            }

            // children checks (sku uniqueness, sale_price <= price, ownership)
            $children = $this->input('children', []);
            foreach ($children as $i => $child) {
                // sale_price <= price
                if (isset($child['price']) && isset($child['sale_price']) && $child['sale_price'] !== null) {
                    if (floatval($child['sale_price']) > floatval($child['price'])) {
                        $v->errors()->add("children.$i.sale_price", "Child #$i sale_price must be less than or equal to its price.");
                    }
                }

                // sku uniqueness: if child has id, ignore that id; otherwise ensure unique across products
                $sku = $child['sku'] ?? null;
                if ($sku) {
                    $qb = \App\Models\Product::where('sku', $sku);
                    if (!empty($child['id'])) {
                        $qb->where('id', '!=', $child['id']);
                    }
                    if ($qb->exists()) {
                        $v->errors()->add("children.$i.sku", "The SKU [$sku] is already used by another product.");
                    }
                }

                // if child has id ensure it belongs to this parent
                if (!empty($child['id'])) {
                    $existing = Product::find($child['id']);
                    if (!$existing) {
                        $v->errors()->add("children.$i.id", "Child product id {$child['id']} not found.");
                    } elseif ($existing->parent_id != $parentId) {
                        $v->errors()->add("children.$i.id", "Child product id {$child['id']} does not belong to this parent.");
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'sku.unique' => 'This SKU is already used by another product.',
            'name.*.required' => 'Name is required for all active languages.',
        ];
    }
}