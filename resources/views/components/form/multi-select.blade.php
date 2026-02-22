@props([
'label' => '',
'name',
'x_name' => null,
'options' => [], // expected format: ['Attribute Name' => [id => value, ...], ...]
'selected' => [], // array of selected values: [attribute_name => value_id, ...]
'required' => false
])

<div class="mb-4" x-data="multiSelect('{{ $x_name }}', @json($options), @json($selected))">
    @if ($label)
    <label class="block font-semibold mb-2">
        {{ $label }}
        @if($required)<span class="text-red-500">*</span>@endif
    </label>
    @endif

    <template x-for="(values, attr) in attributes" :key="attr">
        <div class="mb-2">
            <p class="font-medium" x-text="attr"></p>
            <select :name="{{ $x_name ?? ('\'' . $name . '\'') }}" class="w-full border p-2 rounded"
                x-model="selected[attr]" :required="{{ $required ? 'true' : 'false' }}">
                <option value="">-- Select --</option>
                <template x-for="(val, id) in values" :key="id">
                    <option :value="id" x-text="val" :selected="selected[attr] == id"></option>
                </template>
            </select>
        </div>
    </template>

    @error($x_name)
    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<script>
    function multiSelect(name, options = {}, selected = {}) {
    return {
        name,
        attributes: options,
        selected: selected,
    }
}
</script>