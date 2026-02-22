<?php

namespace App\Http\Requests\Users;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user')->id; // get current user ID from route parameter

        $user = User::findOrFail($userId); // Fetch the user to get their media
        $mediaIds = $user->thumbnail_id ? [$user->thumbnail_id] : []; // Get the media IDs associated with the user

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId),  // Unique except current user
            ],
            'password' => ['nullable', 'confirmed', 'min:8'], // password optional on update
            'active' => ['required', 'boolean'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id'],
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