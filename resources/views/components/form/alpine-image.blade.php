@props([
'label' => '', // Label for the field
'x_name', // Dynamic Alpine name
'x_model', // Alpine model
'multiple' => false, // Multiple files allowed
'existing' => [], // Existing images
'deletedMedia' => 'deleted_media' // Hidden input for deleted media
])

<div x-data="alpineImageUploader({{ $multiple ? 'true' : 'false' }}, {{ json_encode($existing) }}, '{{ $deletedMedia }}')"
    class="mb-4">

    @if ($label)
    <label class="block font-semibold mb-1">{{ $label }}</label>
    @endif

    <!-- Drop zone -->
    <div class="border-2 border-dashed rounded p-4 text-center cursor-pointer bg-gray-50 hover:bg-gray-100"
        @click="$refs.fileInput.click()" @dragover.prevent @drop.prevent="handleDrop($event)">
        <p class="text-gray-500">Click or drag image{{ $multiple ? 's' : '' }} to upload</p>
        <input type="file" x-ref="fileInput" :name="xNameAttr" :multiple="{{ $multiple ? 'true' : 'false' }}"
            accept="image/*" class="hidden" @change="previewSelectedFiles($event)">
    </div>

    <!-- Preview -->
    <div class="flex flex-wrap gap-4 mt-4">
        <template x-for="(file, index) in files" :key="index">
            <div class="relative">
                <img :src="file.preview" class="w-24 h-24 object-cover rounded" />
                <button @click.prevent="removeFile(index); deleteFile(file)"
                    class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">×</button>
            </div>
        </template>

        <template x-for="(file, index) in existingFiles" :key="'existing-' + index">
            <div class="relative existing-media">
                <img :src="file.url" class="w-24 h-24 object-cover rounded">
                <input type="hidden" :name="'existing_' + xNameAttr + '[]'" :value="file.id">
                <button type="button" @click="deleteFile(file); existingFiles.splice(index,1)"
                    class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center remove-existing">×</button>
            </div>
        </template>

        <input type="hidden" :name="deletedMedia" :value="deletedIds.join(',')">
    </div>
</div>

@once
@push('scripts')
<script>
    function alpineImageUploader(multiple = false, existing = [], deletedMedia = 'deleted_media') {
        return {
            files: [],
            existingFiles: existing.map(f => ({ id: f.id, url: f.url ?? f.file_path })),
            deletedIds: [],
            multiple: multiple,
            xNameAttr: '',

            init() {
                // Determine the dynamic name from x_name
                this.xNameAttr = this.$el.getAttribute('x_name') || 'file';
            },

            previewSelectedFiles(event) {
                const selectedFiles = Array.from(event.target.files);
                selectedFiles.forEach(file => {
                    const reader = new FileReader();
                    reader.onload = e => {
                        this.files.push({ file: file, preview: e.target.result });
                    };
                    reader.readAsDataURL(file);
                });
            },

            removeFile(index) {
                this.files.splice(index, 1);
            },

            deleteFile(file) {
                if (file.id) this.deletedIds.push(file.id);
            }
        }
    }
</script>
@endpush
@endonce

@push('styles')
<style>
    .remove-existing {
        cursor: pointer;
    }
</style>
@endpush