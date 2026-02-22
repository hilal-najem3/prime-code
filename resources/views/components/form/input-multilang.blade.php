@props([
'label' => '',
'name', // main name (string) if used in product
'x_name' => null, // optional: dynamic JS name for children
'x_model' => null, // optional: Alpine property binding
'languages',
'required' => false,
'value' => [],
])

<div class="mb-4 overflow-x-auto">
    <div class="min-w-full inline-flex space-x-4">
        @foreach($languages as $lang)
        @php
        $locale = is_array($lang) ? $lang['code'] : $lang->code;
        $languageName = is_array($lang) ? $lang['name'] : $lang->name;

        // Determine final name for input
        $inputName = $x_name
        ? str_replace('{locale}', $locale, $x_name)
        : $name . '[' . $locale . ']';
        @endphp

        <div class="min-w-[250px]">
            @if($label)
            <label class="block font-semibold mb-1">
                {{ $label }} ({{ strtoupper($locale) }} - {{ $languageName }})
                @if($required)
                <span class="text-red-500">*</span>
                @endif
            </label>
            @endif

            @if ($x_name)
            <input type="text" :name="`${{!! str_replace('{locale}', $locale, $x_name) !!}}`"
                x-model="{{ $x_model }}['{{ $locale }}']" {{ $required ? 'required' : '' }}
                class="mb-2 w-full border p-2 rounded">
            @else
            <input type="text" name="{{ $inputName }}" value="{{ old($inputName, $value[$locale] ?? '') }}" {{ $required
                ? 'required' : '' }} class="mb-2 w-full border p-2 rounded">
            @endif

            @error($inputName)
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        @endforeach
    </div>
</div>