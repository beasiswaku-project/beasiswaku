@props(['pendaftaran'])

@php($snapshot = $pendaftaran->snapshot_ekonomi ?? [])

{{-- Economic data and GPA frozen when the registration was submitted. --}}
<section {{ $attributes->class('rounded-xl border border-gray-200 bg-white p-6') }} aria-labelledby="snapshot-title">
    <h2 id="snapshot-title" class="font-semibold">Data saat pendaftaran dikirim</h2>
    <p class="mt-1 text-sm text-gray-500">Dikirim {{ $pendaftaran->tgl_kirim?->translatedFormat('j F Y, H:i') }}. Perubahan profil sesudahnya tidak memengaruhi seleksi.</p>

    <dl class="mt-5 grid gap-x-6 gap-y-4 text-sm sm:grid-cols-2 lg:grid-cols-3">
        <div>
            <dt class="text-gray-500">Penghasilan orang tua</dt>
            <dd class="mt-0.5 font-medium"><x-rupiah :value="$snapshot['penghasilan_ortu'] ?? 0" /> per bulan</dd>
        </div>
        <div>
            <dt class="text-gray-500">Jumlah tanggungan</dt>
            <dd class="mt-0.5 font-medium">{{ $snapshot['jml_tanggungan'] ?? '–' }} orang</dd>
        </div>
        <div>
            <dt class="text-gray-500">IPK terakhir</dt>
            <dd class="mt-0.5 font-medium tabular-nums">{{ isset($snapshot['ipk_terakhir']) ? number_format($snapshot['ipk_terakhir'], 2, ',', '.') : '–' }}</dd>
        </div>
        <div>
            <dt class="text-gray-500">KIP Kuliah</dt>
            <dd class="mt-0.5 font-medium">{{ ($snapshot['punya_kipk'] ?? false) ? 'Penerima KIP-K' : 'Bukan penerima' }}</dd>
        </div>
        <div>
            <dt class="text-gray-500">DTSEN</dt>
            <dd class="mt-0.5 font-medium">{{ ($snapshot['terdaftar_dtsen'] ?? false) ? 'Terdaftar, desil '.($snapshot['desil_dtsen'] ?? '–') : 'Tidak terdaftar' }}</dd>
        </div>
        <div>
            <dt class="text-gray-500">Wilayah asal</dt>
            <dd class="mt-0.5 font-medium">{{ $snapshot['wilayah'] ?? '–' }}{{ ($snapshot['is_3t'] ?? false) ? ' (3T)' : '' }}</dd>
        </div>
    </dl>
</section>
