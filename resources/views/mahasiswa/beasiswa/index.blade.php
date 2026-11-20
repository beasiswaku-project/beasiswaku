@php($isFiltered = $search !== '' || array_filter($filter) !== [])

<x-layouts.app title="Katalog beasiswa" :breadcrumbs="['Mahasiswa', 'Beasiswa']">
    <h1 class="text-3xl font-semibold tracking-tight">Katalog beasiswa</h1>
    <p class="mt-2 text-gray-500">Semua program beasiswa yang sedang dibuka.</p>

    <x-beasiswa.katalog-tabs class="mt-8" />

    <form method="GET" action="{{ route('beasiswa.index') }}" role="search" class="mt-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
        <div class="relative w-full sm:max-w-xs">
            <label for="q" class="sr-only">Cari beasiswa</label>
            <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 size-4 -translate-y-1/2 text-gray-400" />
            <input
                id="q"
                name="q"
                type="search"
                value="{{ $search }}"
                placeholder="Cari judul beasiswa"
                class="block w-full rounded-lg border border-gray-200 bg-white py-2.5 pr-3.5 pl-10 text-sm placeholder:text-gray-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none"
            >
        </div>

        <label for="filter-kategori" class="sr-only">Filter kategori</label>
        <select id="filter-kategori" name="kategori" class="block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none sm:w-auto">
            <option value="">Semua kategori</option>
            @foreach ($daftarKategori as $kategori)
                <option value="{{ $kategori->id }}" @selected(($filter['kategori'] ?? '') == $kategori->id)>{{ $kategori->nama_kategori }}</option>
            @endforeach
        </select>

        <label for="filter-jenjang" class="sr-only">Filter jenjang</label>
        <select id="filter-jenjang" name="jenjang" class="block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none sm:w-auto">
            <option value="">Semua jenjang</option>
            @foreach ($daftarJenjang as $jenjang)
                <option value="{{ $jenjang->id }}" @selected(($filter['jenjang'] ?? '') == $jenjang->id)>{{ $jenjang->nama_jenjang }}</option>
            @endforeach
        </select>

        <div class="flex items-center gap-3">
            <x-button variant="secondary">Terapkan</x-button>
            @if ($isFiltered)
                <a href="{{ route('beasiswa.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900">Reset</a>
            @endif
        </div>
    </form>

    @if ($program->isEmpty())
        <p class="mt-6 rounded-xl border border-gray-200 bg-white px-5 py-12 text-center text-gray-500">
            {{ $isFiltered ? 'Tidak ada beasiswa yang cocok dengan filter ini.' : 'Belum ada beasiswa yang sedang dibuka.' }}
        </p>
    @else
        <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($program as $item)
                <x-program.kartu :program="$item" />
            @endforeach
        </div>

        @if ($program->hasPages())
            <div class="mt-6">{{ $program->links() }}</div>
        @endif
    @endif
</x-layouts.app>
