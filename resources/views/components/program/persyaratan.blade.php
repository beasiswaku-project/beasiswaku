@props(['program', 'daftarJenis'])

@php
    /** Requirements are locked once anybody has registered for the program. */
    $isTerkunci = $program->pendaftaran_count > 0;
    $editingId = old('_form') === 'persyaratan-edit' ? old('persyaratan_id') : null;
    $isPersyaratanError = in_array(old('_form'), ['persyaratan-create', 'persyaratan-edit'], true) && $errors->any();
@endphp

<section {{ $attributes->class('overflow-hidden rounded-xl border border-gray-200 bg-white') }} aria-labelledby="persyaratan-title">
    <div class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 id="persyaratan-title" class="font-semibold">Persyaratan</h2>
            <p class="mt-1 text-sm text-gray-500">
                @if ($isTerkunci)
                    Persyaratan terkunci karena program sudah memiliki pendaftar.
                @else
                    Syarat wajib menggugurkan jika tidak terpenuhi; syarat preferensi hanya menurunkan persentase kesesuaian.
                @endif
            </p>
        </div>

        @unless ($isTerkunci)
            <button
                type="button"
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-600 focus:ring-4 focus:ring-blue-100 focus:outline-none"
                data-dialog-target="persyaratan-dialog"
                data-dialog-title="Tambah syarat"
                data-action="{{ route('admin.program.persyaratan.store', $program) }}"
                data-fields="{{ json_encode(['_form' => 'persyaratan-create', 'persyaratan_id' => '', 'jenis' => '', 'nilai' => '', 'wajib' => true]) }}"
            >
                <x-icon name="plus" class="size-4" />
                Tambah syarat
            </button>
        @endunless
    </div>

    <ul class="divide-y divide-gray-100">
        @forelse ($program->persyaratan as $persyaratan)
            <li class="flex items-center justify-between gap-4 px-5 py-4 text-sm">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-medium">{{ $persyaratan->deskripsi() }}</span>
                    @if ($persyaratan->wajib)
                        <span class="rounded-md bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700">Wajib</span>
                    @else
                        <span class="rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">Preferensi</span>
                    @endif
                </div>

                @unless ($isTerkunci)
                    <div class="flex items-center gap-1">
                        <button
                            type="button"
                            class="rounded-lg p-2 text-gray-700 hover:bg-gray-100"
                            aria-label="Ubah syarat {{ $persyaratan->deskripsi() }}"
                            title="Ubah"
                            data-dialog-target="persyaratan-dialog"
                            data-dialog-title="Ubah syarat"
                            data-action="{{ route('admin.persyaratan.update', $persyaratan) }}"
                            data-method="PUT"
                            data-fields="{{ json_encode(['_form' => 'persyaratan-edit', 'persyaratan_id' => $persyaratan->id, 'jenis' => $persyaratan->jenis->value, 'nilai' => $persyaratan->nilai, 'wajib' => $persyaratan->wajib]) }}"
                        >
                            <x-icon name="pencil" class="size-4" />
                        </button>

                        <form method="POST" action="{{ route('admin.persyaratan.destroy', $persyaratan) }}" data-confirm="Hapus syarat &quot;{{ $persyaratan->deskripsi() }}&quot;?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-lg p-2 text-red-500 hover:bg-red-50" aria-label="Hapus syarat {{ $persyaratan->deskripsi() }}" title="Hapus">
                                <x-icon name="trash" class="size-4" />
                            </button>
                        </form>
                    </div>
                @endunless
            </li>
        @empty
            <li class="px-5 py-10 text-center text-sm text-gray-500">Belum ada persyaratan. Program tanpa syarat akan tampil sebagai "Terbuka umum".</li>
        @endforelse
    </ul>
</section>

@unless ($isTerkunci)
    <x-modal id="persyaratan-dialog" :title="$editingId ? 'Ubah syarat' : 'Tambah syarat'" :data-open-on-load="$isPersyaratanError">
        <form
            method="POST"
            action="{{ $editingId ? route('admin.persyaratan.update', $editingId) : route('admin.program.persyaratan.store', $program) }}"
            class="space-y-5 px-6 py-5"
            novalidate
        >
            @csrf
            <input type="hidden" name="_method" value="PUT" @disabled(! $editingId)>
            <input type="hidden" name="_form" value="{{ $editingId ? 'persyaratan-edit' : 'persyaratan-create' }}">
            <input type="hidden" name="persyaratan_id" value="{{ $editingId }}">

            <x-form.select name="jenis" label="Jenis syarat" placeholder="Pilih jenis" required>
                @foreach ($daftarJenis as $jenis)
                    <option value="{{ $jenis->value }}" @selected(old('jenis') === $jenis->value)>{{ $jenis->label() }}</option>
                @endforeach
            </x-form.select>

            <div>
                <x-form.field name="nilai" label="Nilai" maxlength="100" />
                <ul class="mt-1.5 space-y-0.5 text-sm text-gray-500">
                    <li>IPK minimum: angka 0–4, contoh 3,00.</li>
                    <li>Penghasilan maksimum: rupiah per bulan, contoh 4000000.</li>
                    <li>Semester minimum: angka, contoh 3.</li>
                    <li>DTSEN: desil tertinggi yang diterima (opsional, bawaan 4).</li>
                    <li>Dokumen: nama dokumen, contoh SKTM.</li>
                    <li>KIP-K dan wilayah 3T: kosongkan.</li>
                </ul>
            </div>

            <x-form.checkbox name="wajib" label="Syarat wajib" description="Hapus centang untuk menjadikannya syarat preferensi." :checked="true" />

            <div class="flex justify-end gap-3 pt-2">
                <x-button variant="secondary" type="button" data-dialog-close>Batal</x-button>
                <x-button>Simpan</x-button>
            </div>
        </form>
    </x-modal>
@endunless
