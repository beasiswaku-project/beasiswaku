@props(['daftarPenyedia', 'daftarKategori', 'daftarJenjang', 'program' => null])

{{-- Shared inputs for the create/edit program dialogs. --}}
<x-form.field name="judul" label="Judul program" :value="$program?->judul" maxlength="200" placeholder="Contoh: Beasiswa Prestasi Nusantara 2027" required />

<x-form.select name="penyedia_id" label="Penyedia" placeholder="Pilih penyedia" required>
    @foreach ($daftarPenyedia as $penyedia)
        <option value="{{ $penyedia->id }}" @selected(old('penyedia_id', $program?->penyedia_id) == $penyedia->id)>{{ $penyedia->nama_penyedia }}</option>
    @endforeach
</x-form.select>

<div class="grid gap-5 sm:grid-cols-2">
    <x-form.select name="kategori_id" label="Kategori" placeholder="Pilih kategori" required>
        @foreach ($daftarKategori as $kategori)
            <option value="{{ $kategori->id }}" @selected(old('kategori_id', $program?->kategori_id) == $kategori->id)>{{ $kategori->nama_kategori }}</option>
        @endforeach
    </x-form.select>

    <x-form.select name="jenjang_id" label="Jenjang sasaran" placeholder="Pilih jenjang" required>
        @foreach ($daftarJenjang as $jenjang)
            <option value="{{ $jenjang->id }}" @selected(old('jenjang_id', $program?->jenjang_id) == $jenjang->id)>{{ $jenjang->nama_jenjang }}</option>
        @endforeach
    </x-form.select>
</div>

<div class="grid gap-5 sm:grid-cols-3">
    <x-form.field name="kuota" label="Kuota penerima" type="number" :value="$program?->kuota" min="1" step="1" inputmode="numeric" required />
    <x-form.field name="tgl_buka" label="Tanggal buka" type="date" :value="$program?->tgl_buka?->toDateString()" required />
    <x-form.field name="deadline" label="Deadline" type="date" :value="$program?->deadline?->toDateString()" required />
</div>
