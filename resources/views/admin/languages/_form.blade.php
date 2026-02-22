@props(['language' => null])

@php
$isEdit = isset($language);
@endphp

<form method="POST"
    action="{{ $language ? route('admin.languages.update', $language->id) : route('admin.languages.store') }}">
    @csrf
    @if($language)
    @method('PUT')
    @endif

    @if(session('error'))
    <x-ui.toast :message="session('error')" />
    @endif

    @if ($errors->any())
    @foreach ($errors->all() as $error)
    <x-ui.toast :message="$error" :color="'red'" />
    @endforeach
    @endif

    <x-form.input label="Language Code" name="code" value="{{ old('code', $language?->code) }}" required />

    <x-form.input label="Language Name" name="name" value="{{ old('name', $language?->name) }}" required />

    <x-form.checkbox name="is_active" label="Active" :checked="old('is_active', $language?->is_active ?? true)" />

    <div class="flex justify-between pt-2">
        <a href="{{ route('admin.languages.index') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">←
            Back</a>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            {{ $isEdit ? 'Update Language' : 'Create Language' }}
        </button>
    </div>
</form>