<x-layouts.app :title="$profil->user->nama" :breadcrumbs="['Admin', 'Pengguna', 'Profil mahasiswa', $profil->user->nama]">
    <a href="{{ route('admin.profil.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-900">
        <x-icon name="chevron-left" class="size-4" />
        Kembali ke daftar profil
    </a>

    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-3xl font-semibold tracking-tight">{{ $profil->user->nama }}</h1>
                <x-profil.status-akun-badge :user="$profil->user" />
            </div>
            <p class="mt-2 text-gray-500">{{ $profil->user->email }} · {{ $profil->user->no_telepon }}</p>
        </div>

        @if ($profil->user->is_active)
            <form
                method="POST"
                action="{{ route('admin.profil.nonaktif', $profil) }}"
                data-confirm="Nonaktifkan akun &quot;{{ $profil->user->nama }}&quot;? Mahasiswa tidak dapat masuk lagi, tetapi datanya tetap tersimpan."
            >
                @csrf
                @method('PATCH')
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-white px-4 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-50">
                    Nonaktifkan akun
                </button>
            </form>
        @endif
    </div>

    <x-flash class="mt-6" />

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <dl class="grid gap-x-6 gap-y-5 rounded-xl border border-gray-200 bg-white p-6 sm:grid-cols-2 lg:col-span-2">
            <div>
                <dt class="text-sm text-gray-500">NIK</dt>
                <dd class="mt-1 font-medium tabular-nums">{{ $profil->user->nik ?? '–' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Kampus</dt>
                <dd class="mt-1 font-medium">{{ $profil->kampus ?? '–' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Jenjang</dt>
                <dd class="mt-1 font-medium">{{ $profil->jenjang->nama_jenjang }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Wilayah asal</dt>
                <dd class="mt-1 flex flex-wrap items-center gap-2 font-medium">
                    {{ $profil->wilayah->kabupaten }}, {{ $profil->wilayah->provinsi }}
                    <x-wilayah.status-badge :wilayah="$profil->wilayah" />
                </dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Penghasilan orang tua per bulan</dt>
                <dd class="mt-1 font-medium"><x-rupiah :value="$profil->penghasilan_ortu" /></dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Jumlah tanggungan</dt>
                <dd class="mt-1 font-medium">{{ $profil->jml_tanggungan }} orang</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">KIP Kuliah</dt>
                <dd class="mt-1 font-medium">{{ $profil->punya_kipk ? 'Penerima KIP-K' : 'Bukan penerima' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">DTSEN</dt>
                <dd class="mt-1 font-medium">{{ $profil->terdaftar_dtsen ? "Terdaftar, desil {$profil->desil_dtsen}" : 'Tidak terdaftar' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-sm text-gray-500">Kondisi khusus</dt>
                <dd class="mt-1 font-medium">{{ $profil->kondisi_khusus ?? '–' }}</dd>
            </div>
        </dl>

        <x-profil.kelengkapan :profil="$profil" class="self-start" />
    </div>
</x-layouts.app>
