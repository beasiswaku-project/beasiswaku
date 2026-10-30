@props([
    'name',
    'label',
    'type' => 'text',
])

@php
    $id = $attributes->get('id', $name);
    $isPassword = $type === 'password';
@endphp

<div {{ $attributes->only('class') }}>
    <label for="{{ $id }}" class="mb-2 block text-sm font-medium text-gray-900">{{ $label }}</label>

    <div class="relative">
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="{{ $type }}"
            @unless ($isPassword) value="{{ old($name, $attributes->get('value')) }}" @endunless
            @error($name) aria-invalid="true" aria-describedby="{{ $id }}-error" @enderror
            {{ $attributes->except(['class', 'id', 'value'])->class([
                'block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 transition',
                'focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none',
                'aria-invalid:border-red-400 aria-invalid:focus:border-red-500 aria-invalid:focus:ring-red-100',
                'pr-11' => $isPassword,
            ]) }}
        >

        @if ($isPassword)
            <button
                type="button"
                class="absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-500 hover:text-gray-700"
                data-password-toggle="{{ $id }}"
                aria-label="Tampilkan kata sandi"
                aria-pressed="false"
            >
                <x-icon name="eye" class="size-4" data-icon="show" />
                <x-icon name="eye-off" class="hidden size-4" data-icon="hide" />
            </button>
        @endif
    </div>

    <x-form.error :name="$name" :id="$id" />
</div>
