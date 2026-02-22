@props([
'label' => 'Select',
'name',
'options' => [],
'selected' => [],
'multiSelect' => false
])

@php
$selected = is_array($selected) ? $selected : [$selected];
@endphp

<div x-data="{ open: false }" class="relative">
    <label class="block font-semibold mb-1">{{ $label }}</label>

    <div @click="open = !open"
        class="w-full border p-2 rounded cursor-pointer bg-white flex justify-between items-center">
        <div>
            @if(count($selected))
            {{ implode(', ', collect($options)->only($selected)->values()->toArray()) }}
            @else
            <span class="text-gray-400">Select {{ $multiSelect ? 'options' : 'an option' }}</span>
            @endif
        </div>
        <svg class="w-4 h-4 transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </div>

    <div x-show="open" @click.outside="open = false"
        class="absolute mt-1 w-full bg-white border rounded shadow z-50 max-h-60 overflow-auto">
        @foreach($options as $value => $label)
        <label class="flex items-center px-3 py-2 hover:bg-gray-100 cursor-pointer">
            <input type="{{ $multiSelect ? 'checkbox' : 'radio' }}" name="{{ $name }}{{ $multiSelect ? '[]' : '' }}"
                value="{{ $value }}" class="mr-2" {{ in_array($value, $selected) ? 'checked' : '' }}>
            <span>{{ $label }}</span>
        </label>
        @endforeach
    </div>
</div>