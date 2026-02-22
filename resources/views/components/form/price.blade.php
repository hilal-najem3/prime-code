@props([
'label' => '',
'name',
'required' => false,
'value' => '',
'currencies', // this is the Currency collection
'currenciesName', // name of the select (e.g. currency_id)
])

@php
$amountValue = old($name, is_array($value) ? ($value['amount'] ?? '') : $value);
$currencyField = $currenciesName ?? 'currency_id';
$currencyValue = old($currencyField, is_array($value) ? ($value['currency_id'] ?? '') : '');
@endphp

<div class="mb-4">
    @if ($label)
    <label class="block font-semibold mb-1">
        {{ $label }}
        @if($required)
        <span class="text-red-500">*</span>
        @endif
    </label>
    @endif

    <div class="flex gap-2">
        {{-- Amount input --}}
        <div class="w-full">
            <input type="number" step="0.01" name="{{ $name }}" id="{{ $name }}" value="{{ $amountValue }}" {{ $required
                ? 'required' : '' }} {{ $attributes->merge([
            'class' => 'w-full border p-2 rounded ' . ($errors->has($name) ? 'border-red-500' : '')
            ]) }}
            >
            @error($name)
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Currency select --}}
        <div class="min-w-[100px]">
            <select name="{{ $currencyField }}" id="{{ $currencyField }}" {{ $required ? 'required' : '' }}
                class="w-full border p-2 rounded {{ $errors->has($currencyField) ? 'border-red-500' : '' }}">
                @foreach($currencies as $currency)
                <option value="{{ $currency->id }}" {{ (string) $currencyValue===(string) $currency->id ? 'selected' :
                    '' }}>
                    {{ $currency->symbol ?? $currency->code }}
                </option>
                @endforeach
            </select>

            @error($currencyField)
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>