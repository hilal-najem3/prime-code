@props(['label' => '', 'name', 'multiple' => false, 'existing' => [], 'deletedMedia' => 'deleted_media'])

<div x-data="imageUploader('{{ $name }}', {{ json_encode($multiple) }}, '{{ $deletedMedia }}')" class="mb-4">
    @if ($label)
    <label class="block font-semibold mb-1">{{ $label }}</label>
    @endif

    <!-- Drop zone -->
    <div class="border-2 border-dashed rounded p-4 text-center cursor-pointer bg-gray-50 hover:bg-gray-100"
        @click="$refs.fileInput.click()" @dragover.prevent @drop.prevent="handleDrop($event)">
        <p class="text-gray-500">Click or drag image{{ $multiple ? 's' : '' }} to upload</p>
        <input type="file" x-ref="fileInput" {{ $multiple ? 'multiple' : '' }} accept="image/*"
            name="{{ $name }}{{ $multiple ? '[]' : '' }}" class="hidden" @change="previewSelectedFiles($event)">
    </div>

    <!-- Preview -->
    <div class="flex flex-wrap gap-4 mt-4">
        <template x-for="(file, index) in files" :key="index">
            <div class="relative">
                <img :src="file.preview" class="w-24 h-24 object-cover rounded" />
                <button @click.prevent="removeFile(index);deleteFile({ id: file.id }); $el.parentElement.remove()"
                    class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">×</button>
            </div>
        </template>

        @error($name)
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror

        <input type="hidden" name="{{ $deletedMedia }}" :value="deletedIds.join(',')">

        @foreach($existing as $media)
        <div class="relative existing-media" data-id="{{ $media->id }}">
            <img src="{{ asset('storage/' . $media->file_path) }}" class="w-24 h-24 object-cover rounded">
            <input type="hidden" name="existing_{{ $name }}[]" value="{{ $media->id }}">
            <button type="button"
                class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center remove-existing"
                @click="deleteFile({ id: {{ $media->id }} }); $el.parentElement.remove()">×</button>
        </div>
        @endforeach
    </div>
</div>

@once
@push('scripts')
<script src="{{ asset('assets/admin/js/images.js') }}"></script>
@endpush
@endonce

@push('styles')
<style>
    .remove-existing {
        cursor: pointer;
    }
</style>
@endpush