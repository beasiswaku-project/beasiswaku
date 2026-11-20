<x-layouts.app title="Prestasi pendaftar" :breadcrumbs="['Admin', 'Seleksi', 'Pendaftaran', $pendaftaran->mahasiswa->user->nama, 'Prestasi']">
    <a href="{{ route('admin.pendaftaran.show', $pendaftaran) }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-900">
        <x-icon name="chevron-left" class="size-4" />
        Kembali ke detail pendaftaran
    </a>

    <h1 class="mt-4 text-3xl font-semibold tracking-tight">Prestasi {{ $pendaftaran->mahasiswa->user->nama }}</h1>
    <p class="mt-2 text-gray-500">Bahan penilaian komponen prestasi/organisasi untuk {{ $pendaftaran->program->judul }}.</p>

    <section class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white">
        <x-prestasi.tabel :daftar-prestasi="$pendaftaran->mahasiswa->prestasi" />
    </section>
</x-layouts.app>
