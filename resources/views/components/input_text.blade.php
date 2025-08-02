@props(['name', 'label', 'value' => '', 'type' => 'text'])

<div>
    <label for="{{ $name }}" class="block text-gray-700 text-sm font-bold mb-2">{{ $label }}</label>
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" value="{{ old($name, $value) }}"
           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
    @error($name) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>