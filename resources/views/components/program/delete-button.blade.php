@props(['program', 'pendaftaranCount'])

@if ($pendaftaranCount > 0)
    <span
        class="cursor-not-allowed rounded-lg p-2 text-gray-300"
        aria-disabled="true"
        title="Tidak dapat dihapus, sudah ada {{ $pendaftaranCount }} pendaftar"
    >
        <x-icon name="trash" class="size-4" />
        <span class="sr-only">Hapus {{ $program->judul }} (tidak tersedia, sudah ada {{ $pendaftaranCount }} pendaftar)</span>
    </span>
@else
    <form
        method="POST"
        action="{{ route('admin.program.destroy', $program) }}"
        data-confirm="Hapus program &quot;{{ $program->judul }}&quot;? Tindakan ini tidak dapat dibatalkan."
        {{ $attributes }}
    >
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded-lg p-2 text-red-500 hover:bg-red-50" aria-label="Hapus {{ $program->judul }}" title="Hapus">
            <x-icon name="trash" class="size-4" />
        </button>
    </form>
@endif
