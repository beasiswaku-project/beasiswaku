@php($isDapatDiubah = $pendaftaran->isDraft() && ($esai === null || $esai->isDraft()))

<x-layouts.app title="Esai motivasi" :breadcrumbs="['Mahasiswa', 'Pendaftaran', $pendaftaran->program->judul, 'Esai']">
    <a href="{{ route('mahasiswa.pendaftaran.show', $pendaftaran) }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-900">
        <x-icon name="chevron-left" class="size-4" />
        Kembali ke pendaftaran
    </a>

    <h1 class="mt-4 text-3xl font-semibold tracking-tight">Esai motivasi</h1>
    <p class="mt-2 text-gray-500">Ceritakan kondisimu dan alasan kamu membutuhkan {{ $pendaftaran->program->judul }}. Minimal {{ \App\Models\Esai::PANJANG_MINIMUM }} karakter.</p>

    <x-flash class="mt-6" />

    @if ($isDapatDiubah)
        <form
            method="POST"
            action="{{ $esai ? route('mahasiswa.esai.update', $esai) : route('mahasiswa.pendaftaran.esai.store', $pendaftaran) }}"
            class="mt-6 space-y-5 rounded-xl border border-gray-200 bg-white p-6"
            novalidate
        >
            @csrf
            @if ($esai)
                @method('PUT')
            @endif

            <x-form.field name="judul" label="Judul (opsional)" :value="$esai?->judul" maxlength="200" />
            <x-form.textarea name="isi" label="Isi esai" :value="$esai?->isi" rows="14" maxlength="10000" required />

            <div class="flex flex-wrap items-center justify-between gap-3">
                <p class="text-sm text-gray-500">Esai terkunci setelah pendaftaran dikirim.</p>
                <x-button>Simpan draf</x-button>
            </div>
        </form>

        @if ($esai)
            <form method="POST" action="{{ route('mahasiswa.esai.destroy', $esai) }}" class="mt-3 text-right" data-confirm="Hapus draf esai ini?">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700">Hapus draf esai</button>
            </form>
        @endif
    @elseif ($esai)
        <x-esai.baca :esai="$esai" class="mt-6" />
    @else
        <p class="mt-6 rounded-xl border border-gray-200 bg-white px-5 py-12 text-center text-gray-500">Pendaftaran ini dikirim tanpa esai.</p>
    @endif
</x-layouts.app>
