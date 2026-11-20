<x-layouts.app :title="$pendaftaran->program->judul" :breadcrumbs="['Mahasiswa', 'Pendaftaran', $pendaftaran->program->judul]">
    <a href="{{ route('mahasiswa.pendaftaran.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-900">
        <x-icon name="chevron-left" class="size-4" />
        Kembali ke pendaftaran saya
    </a>

    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-3xl font-semibold tracking-tight">{{ $pendaftaran->program->judul }}</h1>
                <x-pendaftaran.status-badge :pendaftaran="$pendaftaran" />
            </div>
            <p class="mt-2 text-gray-500">{{ $pendaftaran->program->penyedia->nama_penyedia }} · Deadline {{ $pendaftaran->program->deadline?->translatedFormat('j F Y') ?? '–' }}</p>
        </div>

        @if ($pendaftaran->isDapatDibatalkan())
            <form
                method="POST"
                action="{{ route('mahasiswa.pendaftaran.destroy', $pendaftaran) }}"
                data-confirm="Batalkan pendaftaran pada &quot;{{ $pendaftaran->program->judul }}&quot;? Berkas yang sudah diunggah ikut terhapus."
            >
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-white px-4 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-50">
                    Batalkan pendaftaran
                </button>
            </form>
        @endif
    </div>

    <x-flash class="mt-6" />

    <x-dokumen.unggah-mahasiswa :pendaftaran="$pendaftaran" class="mt-6" />

    @if ($pendaftaran->isDraft())
        <section class="mt-6 rounded-xl border border-gray-200 bg-white p-6" aria-labelledby="kirim-title">
            <h2 id="kirim-title" class="font-semibold">Kirim pendaftaran</h2>
            <p class="mt-1 text-sm text-gray-500">Setelah dikirim, data ekonomi dan IPK-mu disimpan untuk seleksi dan pendaftaran tidak dapat diubah lagi.</p>

            @if ($kendala)
                <ul class="mt-4 space-y-2 text-sm">
                    @foreach ($kendala as $item)
                        <li class="flex items-center gap-2 text-red-700">
                            <x-icon name="x" class="size-4" />
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>
            @endif

            <form method="POST" action="{{ route('mahasiswa.pendaftaran.kirim', $pendaftaran) }}" class="mt-5" data-confirm="Kirim pendaftaran sekarang? Data tidak dapat diubah setelah dikirim.">
                @csrf
                <x-button :disabled="$kendala !== []">Kirim pendaftaran</x-button>
            </form>
        </section>
    @else
        <x-pendaftaran.snapshot :pendaftaran="$pendaftaran" class="mt-6" />
    @endif
</x-layouts.app>
