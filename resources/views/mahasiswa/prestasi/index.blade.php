@php
    /** Form state restored after a failed validation, so the dialog reopens in the right mode. */
    $editingId = old('_form') === 'edit' ? old('prestasi_id') : null;
@endphp

<x-layouts.app title="Prestasi dan organisasi" :breadcrumbs="['Mahasiswa', 'Profil', 'Prestasi dan organisasi']">
    <h1 class="text-3xl font-semibold tracking-tight">Profil saya</h1>
    <p class="mt-2 text-gray-500">Catat prestasi dan pengalaman organisasi beserta buktinya untuk dinilai saat seleksi.</p>

    <x-profil.tabs class="mt-8" />

    <x-flash class="mt-6" />

    @if (! $profil)
        <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6">
            <p class="text-gray-700">Isi data diri terlebih dahulu sebelum menambah prestasi.</p>
            <a href="{{ route('mahasiswa.profil.show') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700">
                Isi data diri
                <x-icon name="arrow-right" class="size-4" />
            </a>
        </div>
    @else
        <section class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white">
            <div class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-gray-500">{{ $profil->prestasi->count() }} data tercatat</p>

                <button
                    type="button"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-600 focus:ring-4 focus:ring-blue-100 focus:outline-none"
                    data-dialog-target="prestasi-dialog"
                    data-dialog-title="Tambah prestasi"
                    data-action="{{ route('mahasiswa.prestasi.store') }}"
                    data-fields="{{ json_encode(['_form' => 'create', 'prestasi_id' => '', 'jenis' => '', 'nama_kegiatan' => '', 'tingkat' => '']) }}"
                >
                    <x-icon name="plus" class="size-4" />
                    Tambah prestasi
                </button>
            </div>

            <x-prestasi.tabel :daftar-prestasi="$profil->prestasi" editable />
        </section>

        <x-modal id="prestasi-dialog" :title="$editingId ? 'Ubah prestasi' : 'Tambah prestasi'" :data-open-on-load="$errors->any()">
            <form
                method="POST"
                action="{{ $editingId ? route('mahasiswa.prestasi.update', $editingId) : route('mahasiswa.prestasi.store') }}"
                enctype="multipart/form-data"
                class="space-y-5 px-6 py-5"
                novalidate
            >
                @csrf
                <input type="hidden" name="_method" value="PUT" @disabled(! $editingId)>
                <input type="hidden" name="_form" value="{{ $editingId ? 'edit' : 'create' }}">
                <input type="hidden" name="prestasi_id" value="{{ $editingId }}">

                <x-form.select name="jenis" label="Jenis" placeholder="Pilih jenis" required>
                    @foreach ($daftarJenis as $jenis)
                        <option value="{{ $jenis->value }}" @selected(old('jenis') === $jenis->value)>{{ $jenis->label() }}</option>
                    @endforeach
                </x-form.select>

                <x-form.field name="nama_kegiatan" label="Nama kegiatan atau organisasi" maxlength="150" placeholder="Contoh: Juara 2 Lomba Karya Tulis Ilmiah" required />

                <x-form.select name="tingkat" label="Tingkat" placeholder="Pilih tingkat" required>
                    @foreach ($daftarTingkat as $tingkat)
                        <option value="{{ $tingkat }}" @selected(old('tingkat') === $tingkat)>{{ $tingkat }}</option>
                    @endforeach
                </x-form.select>

                <x-form.file name="bukti" label="File bukti (opsional)" accept=".pdf,.jpg,.jpeg,.png" description="PDF, JPG, atau PNG, maksimal 2 MB. Saat mengubah, kosongkan jika tidak ingin mengganti bukti." />

                <div class="flex justify-end gap-3 pt-2">
                    <x-button variant="secondary" type="button" data-dialog-close>Batal</x-button>
                    <x-button>Simpan</x-button>
                </div>
            </form>
        </x-modal>
    @endif
</x-layouts.app>
