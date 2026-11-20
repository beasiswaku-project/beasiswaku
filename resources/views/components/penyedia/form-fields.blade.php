@props(['daftarJenis', 'penyedia' => null])

{{-- Shared inputs for the create/edit provider dialogs. --}}
<x-form.field name="nama_penyedia" label="Nama penyedia" :value="$penyedia?->nama_penyedia" maxlength="150" placeholder="Contoh: Yayasan Beasiswa Nusantara" required />

<x-form.select name="jenis" label="Jenis penyedia" placeholder="Pilih jenis" required>
    @foreach ($daftarJenis as $jenis)
        <option value="{{ $jenis->value }}" @selected(old('jenis', $penyedia?->jenis?->value) === $jenis->value)>{{ $jenis->label() }}</option>
    @endforeach
</x-form.select>

<x-form.field name="kontak" label="Kontak (opsional)" :value="$penyedia?->kontak" maxlength="100" placeholder="Email atau nomor telepon" />

<x-form.textarea name="alamat" label="Alamat (opsional)" :value="$penyedia?->alamat" maxlength="255" />
