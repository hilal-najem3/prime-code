@props([
'label' => '',
'name',
'x_name' => null,
'options' => [],
'selected' => '',
'required' => false,
])

<div class="mb-4">
    @if ($label)
    <label @if($x_name) {{-- dynamic name — skip binding for --}} @else for="{{ $name }}" @endif
        class="block font-semibold mb-1">
        {{ $label }}
        @if($required)
        <span class="text-red-500">*</span>
        @endif
    </label>
    @endif
    @if ($x_name)
    <select :id="{{ $x_name }}" :name="{{ $x_name }}" {{ $required ? 'required' : '' }} {{ $attributes->merge(['class'
        =>
        'w-full border p-2 rounded ' . ($errors->has($x_name) ? 'border-red-500' : '')]) }}
        >
        @foreach ($options as $key => $val)
        <option value="{{ $key }}" {{ old($x_name, $selected)==$key ? 'selected' : '' }}>
            {{ $val }}
        </option>
        @endforeach
    </select>

    @error($x_name)
    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
    @else
    <select id="{{ $name }}" name="{{ $name }}" {{ $required ? 'required' : '' }} {{ $attributes->merge(['class' =>
        'w-full border p-2 rounded ' . ($errors->has($name) ? 'border-red-500' : '')]) }}
        >
        @foreach ($options as $key => $val)
        <option value="{{ $key }}" {{ old($name, $selected)==$key ? 'selected' : '' }}>
            {{ $val }}
        </option>
        @endforeach
    </select>

    @error($name)
    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
    @endif

</div>