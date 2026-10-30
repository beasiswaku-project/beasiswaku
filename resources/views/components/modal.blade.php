@props(['id', 'title'])

<dialog
    id="{{ $id }}"
    aria-labelledby="{{ $id }}-title"
    {{ $attributes->class('m-auto w-full max-w-lg rounded-xl bg-white p-0 shadow-xl backdrop:bg-gray-900/40') }}
>
    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
        <h2 id="{{ $id }}-title" class="text-lg font-semibold" data-dialog-title>{{ $title }}</h2>
        <button type="button" class="rounded-lg p-1.5 text-gray-500 hover:bg-gray-100" data-dialog-close aria-label="Tutup">
            <x-icon name="x" class="size-4" />
        </button>
    </div>

    {{ $slot }}
</dialog>
