@props([
'label' => 'Links',
'name' => 'links',
'value' => [],
'required' => false,
])

@php
$links = old($name, $value ?? []);
if (!is_array($links)) {
$links = json_decode($links, true) ?? [];
}
@endphp

<div class="mb-4">
    @if ($label)
    <label for="{{ $name }}" class="block font-semibold mb-1">
        {{ $label }}
        @if($required)
        <span class="text-red-500">*</span>
        @endif
    </label>
    @endif

    <div x-data="{ links: [] }" x-init="
        links = {{ json_encode($links) }};
    " class="space-y-2">
        <template x-for="(link, index) in links" :key="index">
            <div class="flex items-center space-x-2">
                <input type="url" :name="`{{ $name }}[${index}]`" x-model="links[index]"
                    class="form-input rounded w-full p-2" placeholder="https://example.com" />
                <button type="button" @click="links.splice(index, 1)"
                    class="text-red-500 hover:text-red-700">&times;</button>
            </div>
        </template>

        <button type="button" @click="links.push('')" class="text-sm text-blue-600 hover:underline">
            + Add Link
        </button>
    </div>
</div>