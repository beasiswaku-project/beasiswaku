<x-layouts.app :title="$program->judul" :breadcrumbs="['Mahasiswa', 'Beasiswa', $program->judul]">
    <a href="{{ route('beasiswa.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-900">
        <x-icon name="chevron-left" class="size-4" />
        Kembali ke katalog
    </a>

    <div class="mt-4">
        <div class="flex flex-wrap items-center gap-2 text-xs font-medium">
            <span class="rounded-md bg-blue-50 px-2 py-0.5 text-blue-700">{{ $program->kategori->nama_kategori }}</span>
            <span class="rounded-md bg-gray-100 px-2 py-0.5 text-gray-600">{{ $program->jenjang->nama_jenjang }}</span>
        </div>
        <h1 class="mt-3 text-3xl font-semibold tracking-tight">{{ $program->judul }}</h1>
        <p class="mt-2 text-gray-500">Diselenggarakan oleh {{ $program->penyedia->nama_penyedia }}</p>
    </div>

    <x-flash class="mt-6" />

    <div class="mt-6">
        @if ($pendaftaranSaya)
            <a href="{{ route('mahasiswa.pendaftaran.show', $pendaftaranSaya) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold transition hover:bg-gray-50">
                Lihat pendaftaranku
                <x-icon name="arrow-right" class="size-4" />
            </a>
        @else
            <form method="POST" action="{{ route('mahasiswa.pendaftaran.store') }}">
                @csrf
                <input type="hidden" name="program_id" value="{{ $program->id }}">
                <x-button>Daftar beasiswa ini</x-button>
            </form>
            <x-form.error name="program_id" id="daftar" />
        @endif
    </div>

    <dl class="mt-8 grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <dt class="text-sm text-gray-500">Periode pendaftaran</dt>
            <dd class="mt-1 font-semibold">{{ $program->tgl_buka?->translatedFormat('j M Y') ?? '–' }} – {{ $program->deadline?->translatedFormat('j M Y') ?? '–' }}</dd>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <dt class="text-sm text-gray-500">Kuota</dt>
            <dd class="mt-1 font-semibold">{{ $program->kuota }} penerima</dd>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <dt class="text-sm text-gray-500">Kontak penyedia</dt>
            <dd class="mt-1 font-semibold">{{ $program->penyedia->kontak ?? '–' }}</dd>
        </div>
    </dl>

    <section class="mt-6 rounded-xl border border-gray-200 bg-white p-5" aria-labelledby="persyaratan-title">
        <h2 id="persyaratan-title" class="font-semibold">Persyaratan</h2>

        @if ($program->persyaratan->isEmpty())
            <p class="mt-2 text-sm text-gray-500">Program ini terbuka umum tanpa syarat khusus.</p>
        @else
            <ul class="mt-4 space-y-3 text-sm">
                @foreach ($program->persyaratan as $persyaratan)
                    <li class="flex items-start gap-3">
                        @if ($persyaratan->jenis->isDokumen() || ! $profil)
                            <x-icon name="file-text" class="size-4 text-gray-400" />
                        @elseif ($persyaratan->isTerpenuhiOleh($profil))
                            <x-icon name="check" class="size-4 text-green-600" />
                        @else
                            <x-icon name="x" class="size-4 text-red-500" />
                        @endif

                        <span>
                            {{ $persyaratan->deskripsi() }}
                            <span class="text-gray-500">({{ $persyaratan->wajib ? 'wajib' : 'preferensi' }})</span>
                            @if ($profil && ! $persyaratan->jenis->isDokumen())
                                <span class="sr-only">{{ $persyaratan->isTerpenuhiOleh($profil) ? 'terpenuhi' : 'belum terpenuhi' }}</span>
                            @endif
                        </span>
                    </li>
                @endforeach
            </ul>

            @if ($profil)
                <p class="mt-4 text-sm text-gray-500">Tanda centang dihitung dari profilmu saat ini. Syarat dokumen dipenuhi dengan mengunggah berkas saat mendaftar.</p>
            @endif
        @endif
    </section>
</x-layouts.app>
