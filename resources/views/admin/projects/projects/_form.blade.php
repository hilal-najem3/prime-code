@php
$isEdit = isset($project);
@endphp

<form method="POST"
    action="{{ $isEdit ? route('admin.projects.update', $project->id) : route('admin.projects.store') }}"
    enctype="multipart/form-data">

    @if ($errors->any())
    @foreach ($errors->all() as $error)
    <x-ui.toast :message="$error" :color="'red'" />
    @endforeach
    @endif

    @csrf
    @if($isEdit)
    @method('PUT')
    @endif

    {{-- Link Slug --}}
    <x-form.input label="Link" name="link" value="{{ old('link', $project->link ?? '') }}" required />

    {{-- Title --}}
    <x-form.input-multilang label="Title" name="title" :languages="$languages"
        :value="old('title', $project?->getTranslations('title') ?? [])" required />

    {{-- Links --}}
    <x-form.links label="Project Links" :value="$project->links ?? []" />

    {{-- Sort Order --}}
    <x-form.sort-order :value="$project->sort_order ?? 0" />

    {{-- Content --}}
    <x-form.translatable-editor-table label="Content" name="content" :languages="$languages"
        :value="old('content', isset($project) ? $project->getTranslations('content') : [])" :required="true" />

    {{-- Thumbnail --}}
    <x-form.image label="Thumbnail" name="thumbnail" :multiple="false"
        :existing="isset($project) ? [$project->thumbnail] : []" />

    {{-- Gallery Images --}}
    <x-form.image label="Gallery Images" name="gallery[]" :multiple="true"
        :existing="isset($project) ? $project->media : []" />

    {{-- Hidden input for keeping track of attached gallery images --}}
    @if($isEdit)
    @foreach($project->media->where('collection_name', 'gallery') as $image)
    <input type="hidden" name="existing_gallery_images[]" value="{{ $image->id }}">
    @endforeach
    @endif

    {{-- Categories --}}
    @if($isEdit)
    <x-form.select-box label="Categories" name="categories" :options="$categories->pluck('name', 'id')"
        :selected="old('categories', $project?->categories->pluck('id')->toArray() ?? [])" :multiSelect="true" />
    @else
    <x-form.select-box label="Categories" name="categories" :options="$categories->pluck('name', 'id')"
        :selected="old('categories')" :multiSelect="true" />
    @endif

    {{-- Tags --}}
    @if($isEdit)
    <x-form.select-box label="Tags" name="tags" :options="$tags->pluck('name', 'id')"
        :selected="old('tags', $project?->tags->pluck('id')->toArray() ?? [])" :multiSelect="true" />
    @else
    <x-form.select-box label="Tags" name="tags" :options="$tags->pluck('name', 'id')" :selected="old('tags')"
        :multiSelect="true" />
    @endif

    {{-- Status --}}
    <x-form.select label="Status" name="is_active" :options="['1' => 'Active', '0' => 'Inactive']"
        :selected="old('is_active', $project->is_active ?? '1')" />

    {{-- Featured --}}
    <x-form.select label="Featured" name="is_featured" :options="['1' => 'Featured', '0' => 'Not Featured']"
        :selected="old('is_featured', $project->is_featured ?? '1')" />

    <div class="flex justify-between pt-4">
        <a href="{{ route('admin.projects.index') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">← Back</a>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            {{ $isEdit ? 'Update Project' : 'Create Project' }}
        </button>
    </div>
</form>

@push('scripts')
{{-- jQuery --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

{{-- Image Preview Script --}}
<script>
    function readURL(input, container, isMultiple = false) {
            if (input.files) {
                $(container).html('');
                Array.from(input.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = e => {
                        $(container).append(`<img src="${e.target.result}" class="w-24 h-24 object-cover rounded mr-2 mb-2" />`);
                    };
                    reader.readAsDataURL(file);
                });
            }
        }

        $(document).ready(function() {
            $(document).on('change', '[data-preview-target]', function() {
                const previewContainer = $(this).data('preview-target');
                const isMultiple = $(this).prop('multiple');
                readURL(this, previewContainer, isMultiple);
            });
        });
</script>

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
{{-- <script src="https://cdn.ckeditor.com/4.25.1/standard/ckeditor.js"></script>
<script>
    function waitForCKEditor(callback) {
        if (typeof CKEDITOR !== 'undefined') {
            callback();
        } else {
            setTimeout(function () {
                waitForCKEditor(callback);
            }, 50);
        }
    }

    function initEditors() {
        document.querySelectorAll('textarea.editor').forEach(function (textarea, index) {
            // Ensure the textarea has an id
            if (!textarea.id) {
                textarea.id = 'editor_' + index;
            }

            // Avoid double initialization
            if (!CKEDITOR.instances[textarea.id]) {
                CKEDITOR.replace(textarea.id, {
                    height: 300,
                    removeButtons: '',
                    allowedContent: true
                });
            }
        });
    }

    // Wait for DOM and CKEditor to be ready
    document.addEventListener('DOMContentLoaded', function () {
        waitForCKEditor(initEditors);
    });

    // Optional: Re-run if CKEditor editors may be added later (e.g. Livewire)
    document.addEventListener('ckeditor:refresh', initEditors);
</script> --}}
@endpush

@push('styles')
<style>
    .form-image-preview img {
        border: 1px solid #ccc;
        padding: 2px;
    }
</style>
@endpush