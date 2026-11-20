@props(['pendaftaran'])

@php
    $belumDiunggah = $pendaftaran->dokumenWajibBelumDiunggah();
    $isMenerima = $pendaftaran->isMenerimaDokumen();
@endphp

{{-- The student's documents for a registration: status per file plus upload and replace forms. --}}
<section {{ $attributes->class('overflow-hidden rounded-xl border border-gray-200 bg-white') }} aria-labelledby="dokumen-title">
    <div class="border-b border-gray-200 p-5">
        <h2 id="dokumen-title" class="font-semibold">Dokumen persyaratan</h2>
        <p class="mt-1 text-sm text-gray-500">PDF, JPG, atau PNG, maksimal 2 MB per file. Dokumen yang ditolak dapat diganti hingga tahap verifikasi selesai.</p>
    </div>

    <ul class="divide-y divide-gray-100">
        @foreach ($pendaftaran->dokumen as $dokumen)
            <li class="flex flex-col gap-3 px-5 py-4 text-sm lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('dokumen.unduh', $dokumen) }}" class="font-medium hover:text-blue-600">{{ $dokumen->jenis_dokumen }}</a>
                        <x-dokumen.status-badge :dokumen="$dokumen" />
                        @if ($dokumen->opsional)
                            <span class="text-xs text-gray-500">Tambahan</span>
                        @endif
                    </div>
                    @if ($dokumen->catatan_admin)
                        <p class="mt-1 text-red-700">Catatan admin: {{ $dokumen->catatan_admin }}</p>
                    @endif
                </div>

                @if ($isMenerima && $dokumen->isDapatDiganti())
                    <div class="flex flex-wrap items-center gap-2">
                        <form method="POST" action="{{ route('mahasiswa.dokumen.update', $dokumen) }}" enctype="multipart/form-data" class="flex items-center gap-2">
                            @csrf
                            @method('PUT')
                            <label for="ganti-{{ $dokumen->id }}" class="sr-only">File pengganti {{ $dokumen->jenis_dokumen }}</label>
                            <input id="ganti-{{ $dokumen->id }}" name="file" type="file" accept=".pdf,.jpg,.jpeg,.png" required class="block w-56 text-sm file:mr-2 file:rounded-md file:border-0 file:bg-gray-100 file:px-3 file:py-1.5 file:text-sm file:font-medium">
                            <x-button variant="secondary" class="py-1.5">Ganti</x-button>
                        </form>

                        @if ($dokumen->isDapatDihapus())
                            <form method="POST" action="{{ route('mahasiswa.dokumen.destroy', $dokumen) }}" data-confirm="Hapus dokumen &quot;{{ $dokumen->jenis_dokumen }}&quot;?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg p-2 text-red-500 hover:bg-red-50" aria-label="Hapus {{ $dokumen->jenis_dokumen }}" title="Hapus">
                                    <x-icon name="trash" class="size-4" />
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </li>
        @endforeach

        @foreach ($belumDiunggah as $jenis)
            <li class="flex flex-col gap-3 px-5 py-4 text-sm lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-medium">{{ $jenis }}</span>
                    <span class="rounded-md bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700">Wajib, belum diunggah</span>
                </div>

                @if ($isMenerima)
                    <form method="POST" action="{{ route('mahasiswa.pendaftaran.dokumen.store', $pendaftaran) }}" enctype="multipart/form-data" class="flex items-center gap-2">
                        @csrf
                        <input type="hidden" name="jenis_dokumen" value="{{ $jenis }}">
                        <label for="unggah-{{ $loop->index }}" class="sr-only">File {{ $jenis }}</label>
                        <input id="unggah-{{ $loop->index }}" name="file" type="file" accept=".pdf,.jpg,.jpeg,.png" required class="block w-56 text-sm file:mr-2 file:rounded-md file:border-0 file:bg-gray-100 file:px-3 file:py-1.5 file:text-sm file:font-medium">
                        <x-button class="py-1.5">Unggah</x-button>
                    </form>
                @endif
            </li>
        @endforeach

        @if ($pendaftaran->dokumen->isEmpty() && $belumDiunggah === [])
            <li class="px-5 py-8 text-center text-sm text-gray-500">Program ini tidak mewajibkan dokumen.</li>
        @endif
    </ul>

    @if ($isMenerima)
        <form method="POST" action="{{ route('mahasiswa.pendaftaran.dokumen.store', $pendaftaran) }}" enctype="multipart/form-data" class="grid gap-4 border-t border-gray-200 p-5 sm:grid-cols-[1fr_1fr_auto] sm:items-end">
            @csrf
            <x-form.field name="jenis_dokumen" id="dokumen-tambahan-nama" label="Dokumen tambahan (opsional)" maxlength="100" placeholder="Contoh: Surat rekomendasi" />
            <x-form.file name="file" id="dokumen-tambahan-file" label="File" accept=".pdf,.jpg,.jpeg,.png" />
            <x-button variant="secondary">Unggah</x-button>
        </form>
    @endif
</section>
