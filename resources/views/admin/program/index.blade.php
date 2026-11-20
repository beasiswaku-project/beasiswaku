@php
    /** Whether a search or filter is active, to adjust the empty state. */
    $isFiltered = $search !== '' || array_filter($filter) !== [];
@endphp

<x-layouts.app title="Program beasiswa" :breadcrumbs="['Admin', 'Beasiswa', 'Program']">
    <h1 class="text-3xl font-semibold tracking-tight">Beasiswa</h1>
    <p class="mt-2 text-gray-500">Kelola penyedia dan program beasiswa dalam satu katalog.</p>

    <x-beasiswa.tabs class="mt-8" />

    <x-flash class="mt-6" />

    <section class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="flex flex-col gap-4 border-b border-gray-200 p-5 xl:flex-row xl:items-center xl:justify-between">
            <form method="GET" action="{{ route('admin.program.index') }}" role="search" class="flex w-full flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center xl:w-auto">
                <div class="relative w-full sm:max-w-xs">
                    <label for="q" class="sr-only">Cari program</label>
                    <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 size-4 -translate-y-1/2 text-gray-400" />
                    <input
                        id="q"
                        name="q"
                        type="search"
                        value="{{ $search }}"
                        placeholder="Cari judul program"
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

                <label for="filter-status" class="sr-only">Filter status</label>
                <select id="filter-status" name="status" class="block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none sm:w-auto">
                    <option value="">Semua status</option>
                    @foreach ($daftarStatus as $status)
                        <option value="{{ $status->value }}" @selected(($filter['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>

                <div class="flex items-center gap-3">
                    <x-button variant="secondary">Terapkan</x-button>
                    @if ($isFiltered)
                        <a href="{{ route('admin.program.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900">Reset</a>
                    @endif
                </div>
            </form>

            <button
                type="button"
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-600 focus:ring-4 focus:ring-blue-100 focus:outline-none"
                data-dialog-target="program-dialog"
            >
                <x-icon name="plus" class="size-4" />
                Tambah program
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-900">
                    <tr>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Program</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Kategori</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Jenjang</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Deadline</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Status</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Pendaftar</th>
                        <th scope="col" class="px-5 py-3.5 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($program as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4">
                                <a href="{{ route('admin.program.show', $item) }}" class="font-medium hover:text-blue-600">{{ $item->judul }}</a>
                                <p class="text-gray-500">{{ $item->penyedia->nama_penyedia }}</p>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">{{ $item->kategori->nama_kategori }}</td>
                            <td class="px-5 py-4 whitespace-nowrap">{{ $item->jenjang->nama_jenjang }}</td>
                            <td class="px-5 py-4 whitespace-nowrap text-gray-500">{{ $item->deadline?->translatedFormat('j M Y') ?? '–' }}</td>
                            <td class="px-5 py-4"><x-program.status-badge :program="$item" /></td>
                            <td class="px-5 py-4 whitespace-nowrap">{{ $item->pendaftaran_count }} / {{ $item->kuota }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.program.show', $item) }}" class="rounded-lg p-2 text-gray-700 hover:bg-gray-100" aria-label="Lihat {{ $item->judul }}" title="Lihat">
                                        <x-icon name="arrow-right" class="size-4" />
                                    </a>
                                    <x-program.delete-button :program="$item" :pendaftaran-count="$item->pendaftaran_count" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-gray-500">
                                @if ($isFiltered)
                                    Tidak ada program yang cocok dengan filter ini.
                                @else
                                    Belum ada program. Tambahkan program pertama.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($program->hasPages())
            <div class="border-t border-gray-200 px-5 py-4">
                {{ $program->links() }}
            </div>
        @endif
    </section>

    <x-modal id="program-dialog" title="Tambah program" :data-open-on-load="$errors->any()">
        <form method="POST" action="{{ route('admin.program.store') }}" class="space-y-5 px-6 py-5" novalidate>
            @csrf

            <x-program.form-fields :daftar-penyedia="$daftarPenyedia" :daftar-kategori="$daftarKategori" :daftar-jenjang="$daftarJenjang" />

            <p class="text-sm text-gray-500">Program baru disimpan sebagai draf. Buka program dari halaman detail setelah persyaratan diisi.</p>

            <div class="flex justify-end gap-3 pt-2">
                <x-button variant="secondary" type="button" data-dialog-close>Batal</x-button>
                <x-button>Simpan</x-button>
            </div>
        </form>
    </x-modal>
</x-layouts.app>
