@props(['penyedia', 'programsCount'])

@if ($programsCount > 0)
    <span
        class="cursor-not-allowed rounded-lg p-2 text-gray-300"
        aria-disabled="true"
        title="Tidak dapat dihapus, masih memiliki {{ $programsCount }} program"
    >
        <x-icon name="trash" class="size-4" />
        <span class="sr-only">Hapus {{ $penyedia->nama_penyedia }} (tidak tersedia, masih memiliki {{ $programsCount }} program)</span>
    </span>
@else
    <form
        method="POST"
        action="{{ route('admin.penyedia.destroy', $penyedia) }}"
        data-confirm="Hapus penyedia &quot;{{ $penyedia->nama_penyedia }}&quot;? Tindakan ini tidak dapat dibatalkan."
        {{ $attributes }}
    >
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded-lg p-2 text-red-500 hover:bg-red-50" aria-label="Hapus {{ $penyedia->nama_penyedia }}" title="Hapus">
            <x-icon name="trash" class="size-4" />
        </button>
    </form>
@endif
