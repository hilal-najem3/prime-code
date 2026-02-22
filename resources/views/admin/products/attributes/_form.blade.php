@props(['attribute' => null])

@php
$isEdit = isset($attribute);
@endphp

<form method="POST"
    action="{{ $attribute ? route('admin.attributes.update', $attribute->id) : route('admin.attributes.store') }}">
    @csrf
    @if($attribute)
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

    {{-- Name (Multilang) --}}
    <x-form.input-multilang label="Name" name="name" :languages="$languages"
        :value="old('name', $attribute?->getTranslations('name') ?? [])" required />

    @php
    $types = [
    'text' => 'text',
    'color' => 'color'
    ];
    @endphp

    {{-- Type --}}
    <x-form.select label="Type" name="type" :options="$types" :selected="old('type', $attribute->type ?? 'text')"
        :required="true" />

    <x-form.checkbox name="is_required" label="Required"
        :checked="old('is_required', $attribute?->is_active ?? true)" />

    <x-form.checkbox name="is_filterable" label="Filterable"
        :checked="old('is_filterable', $attribute?->is_active ?? true)" />

    <x-form.sort-order name="sort_order" label="Sort Order"
        :value="old('sort_order', $attribute?->sort_order ?? true)" />

    {{-- Attribute Values --}}
    <div class="mt-6 mb-3">
        <h3 class="text-lg font-semibold mb-2">Attribute Values</h3>
        <div id="values-container">
            @if(isset($attribute))
            @foreach($attribute->values as $index => $value)
            <div class="value-row flex gap-2 mb-2 items-center">
                <input type="hidden" name="values[{{ $index }}][id]" value="{{ $value->id }}">
                <x-form.input-multilang label="Value" name="values[{{ $index }}][value]" :languages="$languages"
                    :value="old('values.' . $index . '.value') ?? ($value?->getTranslations('value') ?? [])" required />

                <input type="color" name="values[{{ $index }}][color]" value="{{ $value->color }}"
                    class="border rounded p-1 w-16">

                <button type="button"
                    class="remove-value bg-red-500 text-white px-2 rounded hover:bg-red-600">×</button>
            </div>
            @endforeach
            @endif
        </div>

        <button type="button" id="add-value" class="bg-blue-500 text-white px-3 py-1 rounded mt-2 hover:bg-blue-600">
            + Add Value
        </button>
    </div>

    <div class="flex justify-between pt-2">
        <a href="{{ route('admin.attributes.index') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">←
            Back</a>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            {{ $isEdit ? 'Update Attribute' : 'Create Attribute' }}
        </button>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('values-container');
    const addBtn = document.getElementById('add-value');

    addBtn.addEventListener('click', function () {
        const index = container.querySelectorAll('.value-row').length;
        const row = document.createElement('div');
        row.classList.add('value-row', 'flex', 'gap-2', 'mb-2', 'items-center');
        let inputsHtml = '';
        languages.forEach(lang => {
        inputsHtml += `
        <div class="min-w-[250px]">
            <label class="block font-semibold mb-1">
                Value (${lang.code.toUpperCase()} - ${lang.name})
                <span class="text-red-500">*</span>
            </label>
            <input type="text" name="values[${index}][value][${lang.code}]" class="border rounded p-2 w-full"
                placeholder="Value name (${lang.name})">
        </div>
        `;
        });
        row.innerHTML = `
        ${inputsHtml}
        <input type="color" name="values[${index}][color]" value="#000000" class="border rounded p-1 w-16">
        <button type="button" class="remove-value bg-red-500 text-white px-2 rounded hover:bg-red-600">×</button>
        `;
        container.appendChild(row);
    });

    container.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-value')) {
            e.target.closest('.value-row').remove();
        }
    });
});
</script>
@endpush