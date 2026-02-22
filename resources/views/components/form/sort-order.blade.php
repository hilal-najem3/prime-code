@props([
'label' => 'Sort Order',
'name' => 'sort_order',
'value' => 0,
'required' => false,
])

@php
$val = old($name, $value ?? 0);
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
    <input type="number" name="{{ $name }}" id="{{ $name }}" value="{{ $val }}" class="form-input rounded w-full p-2"
        min="0" />
</div>