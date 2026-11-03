@php
    /** Form state restored after a failed validation, so the dialog reopens in the right mode. */
    $editingId = old('_form') === 'edit' ? old('wilayah_id') : null;
    $isFiltered = $search !== '' || $provinsi !== '' || in_array($status, ['3t', 'non_3t'], true);
@endphp

<x-layouts.app title="Wilayah" :breadcrumbs="['Admin', 'Pengaturan', 'Data master']">
    <h1 class="text-3xl font-semibold tracking-tight">Data master</h1>
    <p class="mt-2 text-gray-500">Atur referensi yang digunakan di seluruh sistem {{ config('app.name') }}.</p>

    <x-data-master.tabs class="mt-8" />

    <x-flash class="mt-6" />

    <section class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="flex flex-col gap-4 border-b border-gray-200 p-5 lg:flex-row lg:items-center lg:justify-between">
            <form method="GET" action="{{ route('admin.wilayah.index') }}" role="search" class="flex w-full flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center lg:w-auto">
                <div class="relative w-full sm:max-w-xs">
                    <label for="q" class="sr-only">Cari wilayah</label>
                    <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 size-4 -translate-y-1/2 text-gray-400" />
                    <input
                        id="q"
                        name="q"
                        type="search"
                        value="{{ $search }}"
                        placeholder="Cari kabupaten/kota"
                        class="block w-full rounded-lg border border-gray-200 bg-white py-2.5 pr-3.5 pl-10 text-sm placeholder:text-gray-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none"
                    >
                </div>

                <label for="filter-provinsi" class="sr-only">Filter provinsi</label>
                <select
                    id="filter-provinsi"
                    name="provinsi"
                    class="block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none sm:w-auto"
                >
                    <option value="">Semua provinsi</option>
                    @foreach ($daftarProvinsi as $namaProvinsi)
                        <option value="{{ $namaProvinsi }}" @selected($provinsi === $namaProvinsi)>{{ $namaProvinsi }}</option>
                    @endforeach
                </select>

                <label for="filter-status" class="sr-only">Filter status 3T</label>
                <select
                    id="filter-status"
                    name="status"
                    class="block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none sm:w-auto"
                >
                    <option value="">Semua status</option>
                    <option value="3t" @selected($status === '3t')>Wilayah 3T</option>
                    <option value="non_3t" @selected($status === 'non_3t')>Non-3T</option>
                </select>

                <div class="flex items-center gap-3">
                    <x-button variant="secondary">Terapkan</x-button>
                    @if ($isFiltered)
                        <a href="{{ route('admin.wilayah.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900">Reset</a>
                    @endif
                </div>
            </form>

            <button
                type="button"
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-600 focus:ring-4 focus:ring-blue-100 focus:outline-none"
                data-dialog-target="wilayah-dialog"
                data-dialog-title="Tambah wilayah"
                data-action="{{ route('admin.wilayah.store') }}"
                data-fields="{{ json_encode(['_form' => 'create', 'wilayah_id' => '', 'provinsi' => $provinsi, 'kabupaten' => '', 'is_3t' => false, 'skor_prioritas' => '']) }}"
            >
                <x-icon name="plus" class="size-4" />
                Tambah wilayah
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-900">
                    <tr>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Kabupaten/kota</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Provinsi</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Status</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold whitespace-nowrap">Skor prioritas</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold whitespace-nowrap">Profil mahasiswa</th>
                        <th scope="col" class="px-5 py-3.5 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($wilayah as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4 font-medium whitespace-nowrap">
                                <a href="{{ route('admin.wilayah.show', $item) }}" class="hover:text-blue-600">{{ $item->kabupaten }}</a>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-gray-500">{{ $item->provinsi }}</td>
                            <td class="px-5 py-4"><x-wilayah.status-badge :wilayah="$item" /></td>
                            <td class="px-5 py-4 whitespace-nowrap tabular-nums">{{ $item->skor_prioritas }}</td>
                            <td class="px-5 py-4 whitespace-nowrap">{{ $item->mahasiswa_count }} profil</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1">
                                    <button
                                        type="button"
                                        class="rounded-lg p-2 text-gray-700 hover:bg-gray-100"
                                        aria-label="Ubah {{ $item->kabupaten }}"
                                        title="Ubah"
                                        data-dialog-target="wilayah-dialog"
                                        data-dialog-title="Ubah wilayah"
                                        data-action="{{ route('admin.wilayah.update', $item) }}"
                                        data-method="PUT"
                                        data-fields="{{ json_encode(['_form' => 'edit', 'wilayah_id' => $item->id, 'provinsi' => $item->provinsi, 'kabupaten' => $item->kabupaten, 'is_3t' => $item->is_3t, 'skor_prioritas' => $item->skor_prioritas]) }}"
                                    >
                                        <x-icon name="pencil" class="size-4" />
                                    </button>

                                    <x-wilayah.delete-button :wilayah="$item" :mahasiswa-count="$item->mahasiswa_count" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-500">
                                @if ($isFiltered)
                                    Tidak ada wilayah yang cocok dengan filter ini.
                                @else
                                    Belum ada wilayah. Tambahkan wilayah pertama.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($wilayah->hasPages())
            <div class="border-t border-gray-200 px-5 py-4">
                {{ $wilayah->links() }}
            </div>
        @endif
    </section>

    <x-modal id="wilayah-dialog" :title="$editingId ? 'Ubah wilayah' : 'Tambah wilayah'" :data-open-on-load="$errors->any()">
        <form
            method="POST"
            action="{{ $editingId ? route('admin.wilayah.update', $editingId) : route('admin.wilayah.store') }}"
            class="space-y-5 px-6 py-5"
            novalidate
        >
            @csrf
            <input type="hidden" name="_method" value="PUT" @disabled(! $editingId)>
            <input type="hidden" name="_form" value="{{ $editingId ? 'edit' : 'create' }}">
            <input type="hidden" name="wilayah_id" value="{{ $editingId }}">

            <x-wilayah.form-fields :daftar-provinsi="$daftarProvinsi" />

            <div class="flex justify-end gap-3 pt-2">
                <x-button variant="secondary" type="button" data-dialog-close>Batal</x-button>
                <x-button>Simpan</x-button>
            </div>
        </form>
    </x-modal>
</x-layouts.app>
