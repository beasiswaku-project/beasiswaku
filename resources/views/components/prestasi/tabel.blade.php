@props(['daftarPrestasi', 'editable' => false])

{{-- Achievement table shared by the student's own page (editable) and admin pages (read-only). --}}
<div {{ $attributes->class('overflow-x-auto') }}>
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 text-gray-900">
            <tr>
                <th scope="col" class="px-5 py-3.5 font-semibold">Kegiatan</th>
                <th scope="col" class="px-5 py-3.5 font-semibold">Jenis</th>
                <th scope="col" class="px-5 py-3.5 font-semibold">Tingkat</th>
                <th scope="col" class="px-5 py-3.5 font-semibold">Bukti</th>
                @if ($editable)
                    <th scope="col" class="px-5 py-3.5 text-right font-semibold">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($daftarPrestasi as $prestasi)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4 font-medium">{{ $prestasi->nama_kegiatan }}</td>
                    <td class="px-5 py-4 whitespace-nowrap">{{ $prestasi->jenis->label() }}</td>
                    <td class="px-5 py-4 whitespace-nowrap">{{ $prestasi->tingkat ?? '–' }}</td>
                    <td class="px-5 py-4 whitespace-nowrap">
                        @if ($prestasi->bukti_path)
                            <a href="{{ route('prestasi.bukti', $prestasi) }}" class="font-medium text-blue-600 hover:text-blue-700">Unduh bukti</a>
                        @else
                            <span class="text-gray-400">Belum ada</span>
                        @endif
                    </td>
                    @if ($editable)
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1">
                                <button
                                    type="button"
                                    class="rounded-lg p-2 text-gray-700 hover:bg-gray-100"
                                    aria-label="Ubah {{ $prestasi->nama_kegiatan }}"
                                    title="Ubah"
                                    data-dialog-target="prestasi-dialog"
                                    data-dialog-title="Ubah prestasi"
                                    data-action="{{ route('mahasiswa.prestasi.update', $prestasi) }}"
                                    data-method="PUT"
                                    data-fields="{{ json_encode(['_form' => 'edit', 'prestasi_id' => $prestasi->id, 'jenis' => $prestasi->jenis->value, 'nama_kegiatan' => $prestasi->nama_kegiatan, 'tingkat' => $prestasi->tingkat]) }}"
                                >
                                    <x-icon name="pencil" class="size-4" />
                                </button>

                                <form
                                    method="POST"
                                    action="{{ route('mahasiswa.prestasi.destroy', $prestasi) }}"
                                    data-confirm="Hapus &quot;{{ $prestasi->nama_kegiatan }}&quot;? Tindakan ini tidak dapat dibatalkan."
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg p-2 text-red-500 hover:bg-red-50" aria-label="Hapus {{ $prestasi->nama_kegiatan }}" title="Hapus">
                                        <x-icon name="trash" class="size-4" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $editable ? 5 : 4 }}" class="px-5 py-12 text-center text-gray-500">Belum ada prestasi atau organisasi yang dicatat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
