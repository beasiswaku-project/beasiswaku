@php
    /** Form state restored after a failed validation, so the dialog reopens in the right mode. */
    $editingId = old('_form') === 'edit' ? old('riwayat_id') : null;
    $ipkTerakhir = $profil?->ipkTerakhir();
@endphp

<x-layouts.app title="Riwayat IPK" :breadcrumbs="['Mahasiswa', 'Profil', 'Riwayat IPK']">
    <h1 class="text-3xl font-semibold tracking-tight">Profil saya</h1>
    <p class="mt-2 text-gray-500">IPK terakhir dipakai untuk mencocokkan syarat beasiswa dan disimpan saat kamu mendaftar.</p>

    <x-profil.tabs class="mt-8" />

    <x-flash class="mt-6" />

    @if (! $profil)
        <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6">
            <p class="text-gray-700">Isi data diri terlebih dahulu sebelum mencatat riwayat IPK.</p>
            <a href="{{ route('mahasiswa.profil.show') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700">
                Isi data diri
                <x-icon name="arrow-right" class="size-4" />
            </a>
        </div>
    @else
        <section class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white">
            <div class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-500">IPK terakhir</p>
                    <p class="text-2xl font-semibold tabular-nums">{{ $ipkTerakhir === null ? '–' : number_format($ipkTerakhir, 2, ',', '.') }}</p>
                </div>

                <button
                    type="button"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-600 focus:ring-4 focus:ring-blue-100 focus:outline-none"
                    data-dialog-target="riwayat-dialog"
                    data-dialog-title="Tambah IPK"
                    data-action="{{ route('mahasiswa.riwayat-pendidikan.store') }}"
                    data-fields="{{ json_encode(['_form' => 'create', 'riwayat_id' => '', 'jenjang_id' => $profil->jenjang_id, 'semester' => '', 'ipk' => '']) }}"
                >
                    <x-icon name="plus" class="size-4" />
                    Tambah IPK
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-900">
                        <tr>
                            <th scope="col" class="px-5 py-3.5 font-semibold">Jenjang</th>
                            <th scope="col" class="px-5 py-3.5 font-semibold">Semester</th>
                            <th scope="col" class="px-5 py-3.5 font-semibold">IPK</th>
                            <th scope="col" class="px-5 py-3.5 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($profil->riwayatPendidikan as $riwayat)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-4 whitespace-nowrap">{{ $riwayat->jenjang->nama_jenjang }}</td>
                                <td class="px-5 py-4 whitespace-nowrap">Semester {{ $riwayat->semester }}</td>
                                <td class="px-5 py-4 font-medium tabular-nums">{{ number_format((float) $riwayat->ipk, 2, ',', '.') }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <button
                                            type="button"
                                            class="rounded-lg p-2 text-gray-700 hover:bg-gray-100"
                                            aria-label="Ubah IPK semester {{ $riwayat->semester }}"
                                            title="Ubah"
                                            data-dialog-target="riwayat-dialog"
                                            data-dialog-title="Ubah IPK"
                                            data-action="{{ route('mahasiswa.riwayat-pendidikan.update', $riwayat) }}"
                                            data-method="PUT"
                                            data-fields="{{ json_encode(['_form' => 'edit', 'riwayat_id' => $riwayat->id, 'jenjang_id' => $riwayat->jenjang_id, 'semester' => $riwayat->semester, 'ipk' => $riwayat->ipk]) }}"
                                        >
                                            <x-icon name="pencil" class="size-4" />
                                        </button>

                                        <form
                                            method="POST"
                                            action="{{ route('mahasiswa.riwayat-pendidikan.destroy', $riwayat) }}"
                                            data-confirm="Hapus IPK semester {{ $riwayat->semester }}? Tindakan ini tidak dapat dibatalkan."
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg p-2 text-red-500 hover:bg-red-50" aria-label="Hapus IPK semester {{ $riwayat->semester }}" title="Hapus">
                                                <x-icon name="trash" class="size-4" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center text-gray-500">Belum ada riwayat IPK. Tambahkan IPK semester terakhirmu.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <x-modal id="riwayat-dialog" :title="$editingId ? 'Ubah IPK' : 'Tambah IPK'" :data-open-on-load="$errors->any()">
            <form
                method="POST"
                action="{{ $editingId ? route('mahasiswa.riwayat-pendidikan.update', $editingId) : route('mahasiswa.riwayat-pendidikan.store') }}"
                class="space-y-5 px-6 py-5"
                novalidate
            >
                @csrf
                <input type="hidden" name="_method" value="PUT" @disabled(! $editingId)>
                <input type="hidden" name="_form" value="{{ $editingId ? 'edit' : 'create' }}">
                <input type="hidden" name="riwayat_id" value="{{ $editingId }}">

                <x-form.select name="jenjang_id" label="Jenjang" placeholder="Pilih jenjang" required>
                    @foreach ($daftarJenjang as $jenjang)
                        <option value="{{ $jenjang->id }}" @selected(old('jenjang_id', $profil->jenjang_id) == $jenjang->id)>{{ $jenjang->nama_jenjang }}</option>
                    @endforeach
                </x-form.select>

                <div class="grid gap-5 sm:grid-cols-2">
                    <x-form.field name="semester" label="Semester" type="number" min="1" max="14" step="1" inputmode="numeric" required />
                    <x-form.field name="ipk" label="IPK (0,00–4,00)" inputmode="decimal" placeholder="3,50" required />
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <x-button variant="secondary" type="button" data-dialog-close>Batal</x-button>
                    <x-button>Simpan</x-button>
                </div>
            </form>
        </x-modal>
    @endif
</x-layouts.app>
