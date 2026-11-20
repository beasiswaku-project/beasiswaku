<x-layouts.app title="Esai pendaftar" :breadcrumbs="['Admin', 'Seleksi', 'Pendaftaran', $pendaftaran->mahasiswa->user->nama, 'Esai']">
    <a href="{{ route('admin.pendaftaran.show', $pendaftaran) }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-900">
        <x-icon name="chevron-left" class="size-4" />
        Kembali ke detail pendaftaran
    </a>

    <h1 class="mt-4 text-3xl font-semibold tracking-tight">Esai {{ $pendaftaran->mahasiswa->user->nama }}</h1>
    <p class="mt-2 text-gray-500">Bahan penilaian komponen esai untuk {{ $pendaftaran->program->judul }}.</p>

    @if ($pendaftaran->esai)
        <x-esai.baca :esai="$pendaftaran->esai" class="mt-6" />
    @else
        <p class="mt-6 rounded-xl border border-gray-200 bg-white px-5 py-12 text-center text-gray-500">Pendaftar tidak menulis esai.</p>
    @endif
</x-layouts.app>
