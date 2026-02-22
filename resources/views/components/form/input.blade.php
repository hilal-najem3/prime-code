@props([
'label' => '',
'name' => '',
'type' => 'text',
'x_name' => null,
'x_model' => null,
'required' => false,
'value' => '',
])

@php
$hasError = $name && $errors->has($name);
$inputClasses = 'w-full border p-2 rounded ' . ($hasError ? 'border-red-500' : '');
@endphp

<div class="mb-4">
    {{-- Label --}}
    @if ($label)
    <label @if($x_name) {{-- dynamic name — skip binding for --}} @else for="{{ $name }}" @endif
        class="block font-semibold mb-1">
        {{ $label }}
        @if($required)
        <span class="text-red-500">*</span>
        @endif
    </label>
    @endif

    {{-- Input field --}}
    @if ($x_name)
    {{-- Dynamic (Alpine) input --}}
    <input type="{{ $type }}" :id="{!! $x_name !!}" :name="{!! $x_name !!}" @if($x_model) x-model="{!! $x_model !!}"
        @endif {{ $required ? 'required' : '' }} {{ $attributes->merge(['class' => $inputClasses]) }}
    >
    @else
    {{-- Static (server) input --}}
    <input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $value) }}" @if($x_model)
        x-model="{{ $x_model }}" @endif {{ $required ? 'required' : '' }} {{ $attributes->merge(['class' =>
    $inputClasses]) }}
    >
    @endif

    {{-- Validation message --}}
    @if ($name)
    @error($name)
    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
    @endif
</div>