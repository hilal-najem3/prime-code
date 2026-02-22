@props([
'label' => '',
'name',
'x_name' => null,
'checked' => false, // default unchecked
'required' => false,
])

<div class="mb-4">
    @if ($label)
    <label @if($x_name) {{ $x_name }} @else for="{{ $name }}" @endif
        class="inline-flex items-center space-x-2 cursor-pointer">

        @if($x_name)
        <input type="checkbox" :id="{!! $x_name !!}" :name="{!! $x_name !!}" value="1"
            x-model="{!! $attributes->get('x-model') !!}" {{ $required ? 'required' : '' }} {{
            $attributes->merge(['class' => 'rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500'])
        }} />
        @else
        <input type="checkbox" id="{{ $name }}" name="{{ $name }}" value="1" {{ old($name, $checked) ? 'checked' : '' }}
            {{ $required ? 'required' : '' }} {{ $attributes->merge(['class' => 'rounded border-gray-300 text-indigo-600
        shadow-sm focus:ring-indigo-500']) }} />
        @endif

        <span>{{ $label }}</span>
    </label>
    @endif

    @if($x_name)
    @error($x_name)
    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
    @else
    @error($name)
    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
    @endif
</div>