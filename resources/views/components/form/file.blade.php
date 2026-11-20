@props([
    'name',
    'label',
    'description' => null,
])

@php($id = $attributes->get('id', $name))

<div {{ $attributes->only('class') }}>
    <label for="{{ $id }}" class="mb-2 block text-sm font-medium text-gray-900">{{ $label }}</label>

    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="file"
        @if ($description) aria-describedby="{{ $id }}-description" @endif
        @error($name) aria-invalid="true" aria-errormessage="{{ $id }}-error" @enderror
        {{ $attributes->except(['class', 'id'])->class([
            'block w-full rounded-lg border border-gray-200 bg-white text-sm text-gray-700 transition',
            'file:mr-3 file:border-0 file:bg-gray-100 file:px-3.5 file:py-2.5 file:text-sm file:font-medium file:text-gray-900 hover:file:bg-gray-200',
            'focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none',
            'aria-invalid:border-red-400',
        ]) }}
    >

    @if ($description)
        <p id="{{ $id }}-description" class="mt-1.5 text-sm text-gray-500">{{ $description }}</p>
    @endif

    <x-form.error :name="$name" :id="$id" />
</div>
