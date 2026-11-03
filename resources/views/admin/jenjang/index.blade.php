@php
    /** Form state restored after a failed validation, so the dialog reopens in the right mode. */
    $editingId = old('_form') === 'edit' ? old('jenjang_id') : null;
@endphp

<x-layouts.app title="Jenjang pendidikan" :breadcrumbs="['Admin', 'Pengaturan', 'Data master']">
    <h1 class="text-3xl font-semibold tracking-tight">Data master</h1>
    <p class="mt-2 text-gray-500">Atur referensi yang digunakan di seluruh sistem {{ config('app.name') }}.</p>

    <x-data-master.tabs class="mt-8" />

    <x-flash class="mt-6" />

    <section class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" action="{{ route('admin.jenjang.index') }}" role="search" class="relative w-full sm:max-w-sm">
                <label for="q" class="sr-only">Cari jenjang</label>
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 size-4 -translate-y-1/2 text-gray-400" />
                <input
                    id="q"
                    name="q"
                    type="search"
                    value="{{ $search }}"
                    placeholder="Cari jenjang"
                    class="block w-full rounded-lg border border-gray-200 bg-white py-2.5 pr-3.5 pl-10 text-sm placeholder:text-gray-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none"
                >
            </form>

            <button
                type="button"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-600 focus:ring-4 focus:ring-blue-100 focus:outline-none"
                data-dialog-target="jenjang-dialog"
                data-dialog-title="Tambah jenjang"
                data-action="{{ route('admin.jenjang.store') }}"
                data-fields="{{ json_encode(['_form' => 'create', 'jenjang_id' => '', 'nama_jenjang' => '', 'keterangan' => '']) }}"
            >
                <x-icon name="plus" class="size-4" />
                Tambah jenjang
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-900">
                    <tr>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Nama jenjang</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Keterangan</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold whitespace-nowrap">Profil mahasiswa</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold whitespace-nowrap">Riwayat pendidikan</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold whitespace-nowrap">Jumlah program</th>
                        <th scope="col" class="px-5 py-3.5 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($jenjang as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4 font-medium whitespace-nowrap">
                                <a href="{{ route('admin.jenjang.show', $item) }}" class="hover:text-blue-600">{{ $item->nama_jenjang }}</a>
                            </td>
                            <td class="max-w-md px-5 py-4 text-gray-500">
                                <span class="line-clamp-2">{{ $item->keterangan ?? '—' }}</span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">{{ $item->mahasiswa_count }} profil</td>
                            <td class="px-5 py-4 whitespace-nowrap">{{ $item->riwayat_pendidikan_count }} riwayat</td>
                            <td class="px-5 py-4 whitespace-nowrap">{{ $item->programs_count }} program</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1">
                                    <button
                                        type="button"
                                        class="rounded-lg p-2 text-gray-700 hover:bg-gray-100"
                                        aria-label="Ubah {{ $item->nama_jenjang }}"
                                        title="Ubah"
                                        data-dialog-target="jenjang-dialog"
                                        data-dialog-title="Ubah jenjang"
                                        data-action="{{ route('admin.jenjang.update', $item) }}"
                                        data-method="PUT"
                                        data-fields="{{ json_encode(['_form' => 'edit', 'jenjang_id' => $item->id, 'nama_jenjang' => $item->nama_jenjang, 'keterangan' => $item->keterangan]) }}"
                                    >
                                        <x-icon name="pencil" class="size-4" />
                                    </button>

                                    <x-jenjang.delete-button
                                        :jenjang="$item"
                                        :usage-count="$item->mahasiswa_count + $item->riwayat_pendidikan_count + $item->programs_count"
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-500">
                                @if ($search !== '')
                                    Tidak ada jenjang yang cocok dengan "{{ $search }}".
                                @else
                                    Belum ada jenjang. Tambahkan jenjang pertama.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($jenjang->hasPages())
            <div class="border-t border-gray-200 px-5 py-4">
                {{ $jenjang->links() }}
            </div>
        @endif
    </section>

    <x-modal id="jenjang-dialog" :title="$editingId ? 'Ubah jenjang' : 'Tambah jenjang'" :data-open-on-load="$errors->any()">
        <form
            method="POST"
            action="{{ $editingId ? route('admin.jenjang.update', $editingId) : route('admin.jenjang.store') }}"
            class="space-y-5 px-6 py-5"
            novalidate
        >
            @csrf
            <input type="hidden" name="_method" value="PUT" @disabled(! $editingId)>
            <input type="hidden" name="_form" value="{{ $editingId ? 'edit' : 'create' }}">
            <input type="hidden" name="jenjang_id" value="{{ $editingId }}">

            <x-form.field name="nama_jenjang" label="Nama jenjang" maxlength="50" placeholder="Contoh: S1" required />
            <x-form.field name="keterangan" label="Keterangan" maxlength="255" placeholder="Contoh: Sarjana" />

            <div class="flex justify-end gap-3 pt-2">
                <x-button variant="secondary" type="button" data-dialog-close>Batal</x-button>
                <x-button>Simpan</x-button>
            </div>
        </form>
    </x-modal>
</x-layouts.app>
