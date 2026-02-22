<?php

namespace App\Http\Requests\Products\Categories;

use App\Models\ProductCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('product_category'); // This assumes route model binding or {product} param

        $category = ProductCategory::findorFail($id);
        $mediaIds = $category->media->where('id', '!=', $category->thumbnail_id)->pluck('id')->toArray();

        return [
            'name' => ['required', 'array'],
            'name.*' => ['required', 'string', 'max:255'],
            'parent_id' => [
                'nullable',
                'exists:product_categories,id',
                Rule::notIn([$this->route('category')]) // Prevent self-referencing
            ],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['nullable', 'array'],
            'gallery.*.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'mimetypes:image/jpeg,image/png,image/webp', 'max:2048'],
            'deleted_media' => ['nullable', 'string', function ($attribute, $value, $fail) use ($mediaIds) {
                $ids = array_filter(explode(',', $value), fn($id) => trim($id) !== '');
                foreach ($ids as $id) {
                    if (!in_array($id, $mediaIds)) {
                        return $fail("The selected media ID [$id] is invalid or not deletable.");
                    }
                }
            },],
        ];
    }
}