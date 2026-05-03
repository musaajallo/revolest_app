@props([
    'name',
    'label',
    'type' => 'text',
    'required' => false,
    'placeholder' => null,
    'value' => null,
    'options' => null,
    'rows' => 3,
])

@php
    $value = $value ?? old($name);
    $hasError = $errors->has($name);
    $baseClass = 'w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-[#1c4736] focus:border-transparent ' . ($hasError ? 'border-red-500' : 'border-gray-300');
@endphp

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}@if($required) <span class="text-red-500">*</span>@endif
    </label>

    @if($type === 'textarea')
        <textarea name="{{ $name }}" rows="{{ $rows }}" {{ $required ? 'required' : '' }} placeholder="{{ $placeholder }}"
                  class="{{ $baseClass }}">{{ $value }}</textarea>
    @elseif($type === 'select')
        <select name="{{ $name }}" {{ $required ? 'required' : '' }} class="{{ $baseClass }}">
            <option value="">— Select —</option>
            @foreach(($options ?? []) as $optValue => $optLabel)
                <option value="{{ $optValue }}" @selected((string) $value === (string) $optValue)>{{ $optLabel }}</option>
            @endforeach
        </select>
    @elseif($type === 'yes_no')
        <div class="flex gap-6 mt-2">
            <label class="inline-flex items-center gap-2">
                <input type="radio" name="{{ $name }}" value="1" @checked($value === '1' || $value === 1 || $value === true)
                       class="text-[#1c4736] focus:ring-[#1c4736]">
                <span>Yes</span>
            </label>
            <label class="inline-flex items-center gap-2">
                <input type="radio" name="{{ $name }}" value="0" @checked($value === '0' || $value === 0 || $value === false)
                       class="text-[#1c4736] focus:ring-[#1c4736]">
                <span>No</span>
            </label>
        </div>
    @elseif($type === 'checkboxes')
        <div class="grid grid-cols-2 gap-2 mt-2">
            @foreach(($options ?? []) as $optValue => $optLabel)
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="{{ $name }}[]" value="{{ $optValue }}"
                           @checked(in_array($optValue, (array) old($name, [])))
                           class="rounded text-[#1c4736] focus:ring-[#1c4736]">
                    <span class="text-sm">{{ $optLabel }}</span>
                </label>
            @endforeach
        </div>
    @else
        <input type="{{ $type }}" name="{{ $name }}" {{ $required ? 'required' : '' }}
               value="{{ $value }}" placeholder="{{ $placeholder }}"
               class="{{ $baseClass }}">
    @endif

    @error($name)
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
