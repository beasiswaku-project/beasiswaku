@props(['wilayah', 'mahasiswaCount'])

@if ($mahasiswaCount > 0)
    <span
        class="cursor-not-allowed rounded-lg p-2 text-gray-300"
        aria-disabled="true"
        title="Tidak dapat dihapus, dipakai {{ $mahasiswaCount }} profil mahasiswa"
    >
        <x-icon name="trash" class="size-4" />
        <span class="sr-only">Hapus {{ $wilayah->kabupaten }} (tidak tersedia, dipakai {{ $mahasiswaCount }} profil mahasiswa)</span>
    </span>
@else
    <form
        method="POST"
        action="{{ route('admin.wilayah.destroy', $wilayah) }}"
        data-confirm="Hapus wilayah &quot;{{ $wilayah->kabupaten }}, {{ $wilayah->provinsi }}&quot;? Tindakan ini tidak dapat dibatalkan."
        {{ $attributes }}
    >
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded-lg p-2 text-red-500 hover:bg-red-50" aria-label="Hapus {{ $wilayah->kabupaten }}" title="Hapus">
            <x-icon name="trash" class="size-4" />
        </button>
    </form>
@endif
