<?php

namespace App\Http\Requests\Services;

use Illuminate\Foundation\Http\FormRequest;

class CreateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // You can adjust this as needed
    }

    public function rules(): array
    {
        return [
            'name' => 'required|array',
            'name.*' => 'required|string|max:255',

            'short_description' => 'nullable|array',
            'short_description.*' => 'nullable|string',

            'description' => 'nullable|array',
            'description.*' => 'nullable|string',

            'icon' => 'nullable|string|max:255',
            'slug' => 'required|string|max:255|unique:services,slug',
            'price' => 'nullable|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'active' => 'boolean',

            // 'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // max 2MB
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ];
    }
}