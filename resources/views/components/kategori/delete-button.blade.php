@props(['kategori', 'programsCount'])

@if ($programsCount > 0)
    <span
        class="cursor-not-allowed rounded-lg p-2 text-gray-300"
        aria-disabled="true"
        title="Tidak dapat dihapus, dipakai {{ $programsCount }} program"
    >
        <x-icon name="trash" class="size-4" />
        <span class="sr-only">Hapus {{ $kategori->nama_kategori }} (tidak tersedia, dipakai {{ $programsCount }} program)</span>
    </span>
@else
    <form
        method="POST"
        action="{{ route('admin.kategori.destroy', $kategori) }}"
        data-confirm="Hapus kategori &quot;{{ $kategori->nama_kategori }}&quot;? Tindakan ini tidak dapat dibatalkan."
        {{ $attributes }}
    >
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded-lg p-2 text-red-500 hover:bg-red-50" aria-label="Hapus {{ $kategori->nama_kategori }}" title="Hapus">
            <x-icon name="trash" class="size-4" />
        </button>
    </form>
@endif
