@php
$isEdit = isset($service);
@endphp

<form method="POST" enctype="multipart/form-data"
    action="{{ $isEdit ? route('admin.services.update', $service->id) : route('admin.services.store') }}">
    @csrf

    @if ($errors->any())
    @foreach ($errors->all() as $error)
    <x-ui.toast :message="$error" :color="'red'" />
    @endforeach
    @endif

    @if($isEdit)
    @method('PUT')
    @endif

    <x-form.input-multilang label="Service Name" name="name" :languages="$languages"
        :value="old('name', $service?->getTranslations('name') ?? [])" required />

    {{-- Link Slug --}}
    <x-form.input label="Slug" name="slug" value="{{ old('slug', $service->slug ?? '') }}" required />

    <x-form.input-multilang label="Short Description" name="short_description" :languages="$languages"
        :value="old('short_description', $service?->getTranslations('short_description') ?? [])" required />

    {{-- Description --}}
    <x-form.translatable-editor-table label="Description" name="description" :languages="$languages"
        :value="old('description', $service?->getTranslations('description') ?? [])" :required="true" />

    {{-- Thumbnail --}}
    <x-form.image label="Thumbnail" name="thumbnail" :multiple="false"
        :existing="isset($service) && $service?->thumbnail ? [$service->thumbnail] : []" />

    <x-form.price name="price" currenciesName="currency_id" :currencies="$currencies"
        :value="old('price', $service->price ?? 0)" :required="true" label="Price" />

    <x-form.select label="Active" name="active" :options="['1' => 'Active', '0' => 'Inactive']"
        :selected="old('active', $service->active ?? '1')" />

    <div class="flex justify-between pt-2">
        <a href="{{ route('admin.services.index') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">← Back</a>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            {{ $isEdit ? 'Update Service' : 'Create Service' }}
        </button>
    </div>
</form>

@push('scripts')
{{-- CKEditor --}}
<script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.editor').forEach(function (textarea) {
                CKEDITOR.replace(textarea.id, {
                    height: 300,
                    removeButtons: '',
                    allowedContent: true
                });
            });
        });
</script>
@endpush