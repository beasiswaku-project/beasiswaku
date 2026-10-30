@props([
    'name',
    'label',
])

@php($id = $attributes->get('id', $name))

<div {{ $attributes->only('class') }}>
    <label for="{{ $id }}" class="mb-2 block text-sm font-medium text-gray-900">{{ $label }}</label>

    <textarea
        id="{{ $id }}"
        name="{{ $name }}"
        @error($name) aria-invalid="true" aria-describedby="{{ $id }}-error" @enderror
        {{ $attributes->except(['class', 'id', 'value'])->merge(['rows' => 3])->class([
            'block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 transition',
            'focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none',
            'aria-invalid:border-red-400 aria-invalid:focus:border-red-500 aria-invalid:focus:ring-red-100',
        ]) }}
    >{{ old($name, $attributes->get('value')) }}</textarea>

    <x-form.error :name="$name" :id="$id" />
</div>
