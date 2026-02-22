@php
$isEdit = isset($user);
@endphp

<form method="POST" enctype="multipart/form-data"
    action="{{ $isEdit ? route('admin.users.update', $user->id) : route('admin.users.store') }}">
    @csrf

    @if($isEdit)
    @method('PUT')
    @endif

    <x-form.input label="Name" name="name" required value="{{ old('name', $user->name ?? '') }}" />
    <x-form.input label="Email" name="email" type="email" required value="{{ old('email', $user->email ?? '') }}" />

    {{-- Thumbnail --}}
    <x-form.image label="Thumbnail" name="thumbnail" :multiple="false"
        :existing="isset($user) && $user->thumbnail ? [$user->thumbnail] : []" />

    {{-- For edit, password fields can be optional --}}
    <x-form.input label="Password" name="password" type="password" :required="!$isEdit" />
    <x-form.input label="Confirm Password" name="password_confirmation" type="password" :required="!$isEdit" />

    <x-form.select label="Status" name="active" :options="['1' => 'Active', '0' => 'Inactive']"
        :selected="old('active', $user->active ?? '1')" />

    @if($isEdit)
    <x-form.select-box label="Assign Roles" name="roles" :options="$roles->pluck('name', 'id')"
        :selected="old('roles', $user->roles->pluck('id')->toArray() ?? [])" :multiSelect="true" />
    @else
    <x-form.select-box label="Assign Roles" name="roles" :options="$roles->pluck('name', 'id')" :selected="old('roles')"
        :multiSelect="true" />
    @endif

    <div class="flex justify-between pt-2">
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">← Back</a>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            {{ $isEdit ? 'Update User' : 'Create User' }}
        </button>
    </div>
</form>