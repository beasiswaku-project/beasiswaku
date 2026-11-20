@php
    /** Form state restored after a failed validation, so the dialog reopens in the right mode. */
    $editingId = old('_form') === 'edit' ? old('penyedia_id') : null;
    $isFiltered = $search !== '' || $jenis !== '';
@endphp

<x-layouts.app title="Penyedia beasiswa" :breadcrumbs="['Admin', 'Beasiswa', 'Penyedia']">
    <h1 class="text-3xl font-semibold tracking-tight">Beasiswa</h1>
    <p class="mt-2 text-gray-500">Kelola penyedia dan program beasiswa dalam satu katalog.</p>

    <x-beasiswa.tabs class="mt-8" />

    <x-flash class="mt-6" />

    <section class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="flex flex-col gap-4 border-b border-gray-200 p-5 lg:flex-row lg:items-center lg:justify-between">
            <form method="GET" action="{{ route('admin.penyedia.index') }}" role="search" class="flex w-full flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center lg:w-auto">
                <div class="relative w-full sm:max-w-xs">
                    <label for="q" class="sr-only">Cari penyedia</label>
                    <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 size-4 -translate-y-1/2 text-gray-400" />
                    <input
                        id="q"
                        name="q"
                        type="search"
                        value="{{ $search }}"
                        placeholder="Cari nama atau kontak"
                        class="block w-full rounded-lg border border-gray-200 bg-white py-2.5 pr-3.5 pl-10 text-sm placeholder:text-gray-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none"
                    >
                </div>

                <label for="filter-jenis" class="sr-only">Filter jenis penyedia</label>
                <select
                    id="filter-jenis"
                    name="jenis"
                    class="block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none sm:w-auto"
                >
                    <option value="">Semua jenis</option>
                    @foreach ($daftarJenis as $item)
                        <option value="{{ $item->value }}" @selected($jenis === $item->value)>{{ $item->label() }}</option>
                    @endforeach
                </select>

                <div class="flex items-center gap-3">
                    <x-button variant="secondary">Terapkan</x-button>
                    @if ($isFiltered)
                        <a href="{{ route('admin.penyedia.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900">Reset</a>
                    @endif
                </div>
            </form>

            <button
                type="button"
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-600 focus:ring-4 focus:ring-blue-100 focus:outline-none"
                data-dialog-target="penyedia-dialog"
                data-dialog-title="Tambah penyedia"
                data-action="{{ route('admin.penyedia.store') }}"
                data-fields="{{ json_encode(['_form' => 'create', 'penyedia_id' => '', 'nama_penyedia' => '', 'jenis' => '', 'kontak' => '', 'alamat' => '']) }}"
            >
                <x-icon name="plus" class="size-4" />
                Tambah penyedia
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-900">
                    <tr>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Nama penyedia</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Jenis</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Kontak</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Program</th>
                        <th scope="col" class="px-5 py-3.5 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($penyedia as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4 font-medium">
                                <a href="{{ route('admin.penyedia.show', $item) }}" class="hover:text-blue-600">{{ $item->nama_penyedia }}</a>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">{{ $item->jenis->label() }}</td>
                            <td class="px-5 py-4 text-gray-500">{{ $item->kontak ?? '–' }}</td>
                            <td class="px-5 py-4 whitespace-nowrap">{{ $item->programs_count }} program</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1">
                                    <button
                                        type="button"
                                        class="rounded-lg p-2 text-gray-700 hover:bg-gray-100"
                                        aria-label="Ubah {{ $item->nama_penyedia }}"
                                        title="Ubah"
                                        data-dialog-target="penyedia-dialog"
                                        data-dialog-title="Ubah penyedia"
                                        data-action="{{ route('admin.penyedia.update', $item) }}"
                                        data-method="PUT"
                                        data-fields="{{ json_encode(['_form' => 'edit', 'penyedia_id' => $item->id, 'nama_penyedia' => $item->nama_penyedia, 'jenis' => $item->jenis->value, 'kontak' => $item->kontak, 'alamat' => $item->alamat]) }}"
                                    >
                                        <x-icon name="pencil" class="size-4" />
                                    </button>

                                    <x-penyedia.delete-button :penyedia="$item" :programs-count="$item->programs_count" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-gray-500">
                                @if ($isFiltered)
                                    Tidak ada penyedia yang cocok dengan filter ini.
                                @else
                                    Belum ada penyedia. Tambahkan penyedia pertama.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($penyedia->hasPages())
            <div class="border-t border-gray-200 px-5 py-4">
                {{ $penyedia->links() }}
            </div>
        @endif
    </section>

    <x-modal id="penyedia-dialog" :title="$editingId ? 'Ubah penyedia' : 'Tambah penyedia'" :data-open-on-load="$errors->any()">
        <form
            method="POST"
            action="{{ $editingId ? route('admin.penyedia.update', $editingId) : route('admin.penyedia.store') }}"
            class="space-y-5 px-6 py-5"
            novalidate
        >
            @csrf
            <input type="hidden" name="_method" value="PUT" @disabled(! $editingId)>
            <input type="hidden" name="_form" value="{{ $editingId ? 'edit' : 'create' }}">
            <input type="hidden" name="penyedia_id" value="{{ $editingId }}">

            <x-penyedia.form-fields :daftar-jenis="$daftarJenis" />

            <div class="flex justify-end gap-3 pt-2">
                <x-button variant="secondary" type="button" data-dialog-close>Batal</x-button>
                <x-button>Simpan</x-button>
            </div>
        </form>
    </x-modal>
</x-layouts.app>
