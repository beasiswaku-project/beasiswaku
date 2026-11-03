@props([
    'name',
    'label',
    'description' => null,
    'checked' => false,
])

@php($id = $attributes->get('id', $name))

<div {{ $attributes->only('class') }}>
    <div class="flex items-start gap-3">
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="checkbox"
            value="1"
            @checked(old($name, $checked))
            @if ($description) aria-describedby="{{ $id }}-description" @endif
            {{ $attributes->except(['class', 'id'])->class('mt-0.5 size-4 rounded border-gray-300 text-blue-500 focus:ring-blue-500') }}
        >
        <div>
            <label for="{{ $id }}" class="block text-sm font-medium text-gray-900">{{ $label }}</label>
            @if ($description)
                <p id="{{ $id }}-description" class="mt-0.5 text-sm text-gray-500">{{ $description }}</p>
            @endif
        </div>
    </div>

    <x-form.error :name="$name" :id="$id" />
</div>
