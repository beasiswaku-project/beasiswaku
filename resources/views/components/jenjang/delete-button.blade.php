@props(['jenjang', 'usageCount'])

@if ($usageCount > 0)
    <span
        class="cursor-not-allowed rounded-lg p-2 text-gray-300"
        aria-disabled="true"
        title="Tidak dapat dihapus, masih dipakai {{ $usageCount }} data"
    >
        <x-icon name="trash" class="size-4" />
        <span class="sr-only">Hapus {{ $jenjang->nama_jenjang }} (tidak tersedia, masih dipakai {{ $usageCount }} data)</span>
    </span>
@else
    <form
        method="POST"
        action="{{ route('admin.jenjang.destroy', $jenjang) }}"
        data-confirm="Hapus jenjang &quot;{{ $jenjang->nama_jenjang }}&quot;? Tindakan ini tidak dapat dibatalkan."
        {{ $attributes }}
    >
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded-lg p-2 text-red-500 hover:bg-red-50" aria-label="Hapus {{ $jenjang->nama_jenjang }}" title="Hapus">
            <x-icon name="trash" class="size-4" />
        </button>
    </form>
@endif
