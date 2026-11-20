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
</x-layouts.app>
