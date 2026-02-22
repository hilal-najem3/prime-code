@php
$isEdit = isset($category);
@endphp

<form method="POST"
    action="{{ $isEdit ? route('admin.product-categories.update', $category->id) : route('admin.product-categories.store') }}"
    enctype="multipart/form-data">
    @csrf

    @if($isEdit)
    @method('PUT')
    @endif

    <x-form.input-multilang label="Category Name" name="name" :languages="$languages"
        :value="old('name', $category?->getTranslations('name') ?? [])" required />

    <x-form.select label="Parent Category" name="parent_id"
        :options="[ '' => '— None —' ] + $categories->pluck('name', 'id')->map(fn($name) => is_array($name) ? reset($name) : $name)->toArray()"
        :selected="old('parent_id', $category->parent_id ?? '')" />

    {{-- Thumbnail --}}
    <x-form.image label="Thumbnail" name="thumbnail" :multiple="false"
        :existing="isset($category) ? [$category->thumbnail] : []" />

    {{-- Gallery Images --}}
    <x-form.image label="Gallery Images" name="gallery[]" :multiple="true"
        :existing="isset($category) ? $category->media : []" />

    {{-- Status --}}
    <x-form.select label="Status" name="is_active" :options="['1' => 'Active', '0' => 'Inactive']"
        :selected="old('is_active', $category->is_active ?? '1')" />

    <div class="flex justify-between pt-2">
        <a href="{{ route('admin.post-categories.index') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">←
            Back</a>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            {{ $isEdit ? 'Update Category' : 'Create Category' }}
        </button>
    </div>
</form>