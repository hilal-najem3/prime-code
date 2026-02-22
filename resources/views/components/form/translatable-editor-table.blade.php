@props([
'label' => '',
'name',
'languages' => [],
'required' => false,
'value' => [],
])

<div class="space-y-6">
    @foreach ($languages as $lang)
    <div>
        @if ($label)
        <label for="{{ $name }}_{{ $lang->code }}" class="block font-semibold mb-1">
            {{ $label }} ({{ strtoupper($lang->code) }})
            @if($required)
            <span class="text-red-500">*</span>
            @endif
        </label>
        @endif

        <textarea id="{{ $name }}_{{ $lang->code }}" name="{{ $name }}[{{ $lang->code }}]"
            class="editor w-full border rounded p-2" rows="8" {{ $required ? 'required' : ''
            }}>{{ old("{$name}.{$lang->code}", $value[$lang->code] ?? '') }}</textarea>

        @error("{$name}.{$lang->code}")
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
    @endforeach
</div>