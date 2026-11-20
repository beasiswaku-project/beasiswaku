@props(['pendaftaran', 'daftarStatus'])

{{-- Admin view of a registration's documents with a verification form per file. --}}
<section {{ $attributes->class('overflow-hidden rounded-xl border border-gray-200 bg-white') }} aria-labelledby="dokumen-title">
    <h2 id="dokumen-title" class="border-b border-gray-200 px-5 py-4 font-semibold">Dokumen persyaratan</h2>

    <ul class="divide-y divide-gray-100">
        @forelse ($pendaftaran->dokumen as $dokumen)
            <li class="grid gap-4 px-5 py-4 text-sm lg:grid-cols-[1fr_2fr] lg:items-start">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('dokumen.unduh', $dokumen) }}" class="font-medium text-blue-600 hover:text-blue-700">{{ $dokumen->jenis_dokumen }}</a>
                        <x-dokumen.status-badge :dokumen="$dokumen" />
                    </div>
                    <p class="mt-1 text-gray-500">{{ $dokumen->opsional ? 'Dokumen tambahan' : 'Dokumen wajib' }}</p>
                </div>

                <form method="POST" action="{{ route('admin.dokumen.verifikasi', $dokumen) }}" class="grid gap-3 sm:grid-cols-[auto_1fr_auto] sm:items-start">
                    @csrf
                    @method('PATCH')

                    <label for="status-{{ $dokumen->id }}" class="sr-only">Status {{ $dokumen->jenis_dokumen }}</label>
                    <select id="status-{{ $dokumen->id }}" name="status_verifikasi" class="block rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none">
                        @foreach ($daftarStatus as $status)
                            <option value="{{ $status->value }}" @selected($dokumen->status_verifikasi === $status)>{{ $status->label() }}</option>
                        @endforeach
                    </select>

                    <label for="catatan-{{ $dokumen->id }}" class="sr-only">Catatan untuk {{ $dokumen->jenis_dokumen }}</label>
                    <input id="catatan-{{ $dokumen->id }}" name="catatan_admin" value="{{ $dokumen->catatan_admin }}" maxlength="1000" placeholder="Catatan (wajib jika ditolak)" class="block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm placeholder:text-gray-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none">

                    <x-button variant="secondary">Simpan</x-button>
                </form>
            </li>
        @empty
            <li class="px-5 py-8 text-center text-sm text-gray-500">Pendaftar belum mengunggah dokumen.</li>
        @endforelse
    </ul>

    @error('catatan_admin')
        <p class="border-t border-gray-200 px-5 py-3 text-sm text-red-600">{{ $message }}</p>
    @enderror
</section>
