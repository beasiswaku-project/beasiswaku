@props(['daftarProvinsi', 'wilayah' => null])

{{-- Shared inputs for the create/edit region dialogs. --}}
<x-form.field
    name="provinsi"
    label="Provinsi"
    :value="$wilayah?->provinsi"
    maxlength="100"
    placeholder="Contoh: Nusa Tenggara Timur"
    list="daftar-provinsi"
    autocomplete="off"
    required
/>
<datalist id="daftar-provinsi">
    @foreach ($daftarProvinsi as $namaProvinsi)
        <option value="{{ $namaProvinsi }}"></option>
    @endforeach
</datalist>

<x-form.field name="kabupaten" label="Kabupaten/kota" :value="$wilayah?->kabupaten" maxlength="100" placeholder="Contoh: Kabupaten Sumba Barat Daya" required />

<x-form.checkbox
    name="is_3t"
    label="Wilayah 3T"
    description="Daerah tertinggal, terdepan, dan terluar."
    :checked="$wilayah?->is_3t ?? false"
/>

<div>
    <x-form.field
        name="skor_prioritas"
        label="Skor prioritas (0–100)"
        type="number"
        :value="$wilayah?->skor_prioritas"
        min="0"
        max="100"
        step="1"
        inputmode="numeric"
    />
    <p class="mt-1.5 text-sm text-gray-500">Kosongkan untuk memakai nilai awal: 100 untuk wilayah 3T, 0 untuk lainnya.</p>
</div>
