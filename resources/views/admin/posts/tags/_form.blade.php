@php
$isEdit = isset($tag);
@endphp

<form method="POST" action="{{ $isEdit ? route('admin.post-tags.update', $tag->id) : route('admin.post-tags.store') }}">
    @csrf

    @if($isEdit)
    @method('PUT')
    @endif

    <x-form.input-multilang label="Tag Name" name="name" :languages="$languages"
        :value="old('name', $tag?->getTranslations('name') ?? [])" required />

    <div class="flex justify-between pt-2">
        <a href="{{ route('admin.post-tags.index') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">←
            Back</a>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            {{ $isEdit ? 'Update Tag' : 'Create Tag' }}
        </button>
    </div>
</form>