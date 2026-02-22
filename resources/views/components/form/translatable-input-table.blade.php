@props([
'label' => '',
'name',
'languages',
'required' => false,
'value' => [],
'type' => 'text'
])

<div class="overflow-x-auto">
    <table class="min-w-full table-auto border-collapse border border-gray-300">
        <thead class="bg-gray-100">
            <tr>
                <th class="border border-gray-300 px-4 py-2 text-left">{{ $label }}</th>
                @foreach($languages as $lang)
                <th class="border border-gray-300 px-4 py-2 text-left">
                    {{ strtoupper($lang->code) }}
                    @if($required)
                    <span class="text-red-500">*</span>
                    @endif
                </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border border-gray-300 px-4 py-2 font-semibold">{{ ucfirst($name) }}</td>
                @foreach($languages as $lang)
                <td class="border border-gray-300 px-2 py-2">
                    @if($type === 'textarea')
                    <textarea name="{{ $name }}[{{ $lang->code }}]" rows="3"
                        class="w-full border rounded p-2 {{ $errors->has(" {$name}.{$lang->code}") ? 'border-red-500' : '' }}"
                                {{ $required ? 'required' : '' }}
                            >{{ old("{$name}.{$lang->code}", $value[$lang->code] ?? '') }}</textarea>
                    @else
                    <input type="{{ $type }}" name="{{ $name }}[{{ $lang->code }}]" value="{{ old("
                        {$name}.{$lang->code}", $value[$lang->code] ?? '') }}"
                    class="w-full border rounded p-2 {{ $errors->has("{$name}.{$lang->code}") ? 'border-red-500' : ''
                    }}"
                    {{ $required ? 'required' : '' }}
                    >
                    @endif

                    @error("{$name}.{$lang->code}")
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </td>
                @endforeach
            </tr>
        </tbody>
    </table>
</div>