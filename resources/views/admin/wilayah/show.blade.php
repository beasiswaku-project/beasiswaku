<x-layouts.app :title="$wilayah->kabupaten" :breadcrumbs="['Admin', 'Data master', 'Wilayah', $wilayah->kabupaten]">
    <a href="{{ route('admin.wilayah.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-900">
        <x-icon name="chevron-left" class="size-4" />
        Kembali ke daftar wilayah
    </a>

    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-3xl font-semibold tracking-tight">{{ $wilayah->kabupaten }}</h1>
                <x-wilayah.status-badge :wilayah="$wilayah" />
            </div>
            <p class="mt-2 text-gray-500">Provinsi {{ $wilayah->provinsi }}</p>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold transition hover:bg-gray-50"
                data-dialog-target="wilayah-dialog"
            >
                <x-icon name="pencil" class="size-4" />
                Ubah
            </button>

            <x-wilayah.delete-button :wilayah="$wilayah" :mahasiswa-count="$wilayah->mahasiswa_count" />
        </div>
    </div>

    <x-flash class="mt-6" />

    <dl class="mt-8 grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <dt class="text-sm text-gray-500">Status wilayah</dt>
            <dd class="mt-1 text-2xl font-semibold">{{ $wilayah->is_3t ? 'Wilayah 3T' : 'Non-3T' }}</dd>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <dt class="text-sm text-gray-500">Skor prioritas</dt>
            <dd class="mt-1 text-2xl font-semibold tabular-nums">{{ $wilayah->skor_prioritas }} <span class="text-base font-normal text-gray-500">/ 100</span></dd>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <dt class="text-sm text-gray-500">Profil mahasiswa</dt>
            <dd class="mt-1 text-2xl font-semibold">{{ $wilayah->mahasiswa_count }}</dd>
        </div>
    </dl>

    <p class="mt-6 max-w-2xl text-sm text-gray-500">
        Skor prioritas dipakai sebagai komponen S_wilayah pada skor kebutuhan ekonomi (bobot 25%) untuk mahasiswa yang berasal dari wilayah ini.
    </p>

    <x-modal id="wilayah-dialog" title="Ubah wilayah" :data-open-on-load="$errors->any()">
        <form method="POST" action="{{ route('admin.wilayah.update', $wilayah) }}" class="space-y-5 px-6 py-5" novalidate>
            @csrf
            @method('PUT')

            <x-wilayah.form-fields :daftar-provinsi="$daftarProvinsi" :wilayah="$wilayah" />

            <div class="flex justify-end gap-3 pt-2">
                <x-button variant="secondary" type="button" data-dialog-close>Batal</x-button>
                <x-button>Simpan</x-button>
            </div>
        </form>
    </x-modal>
</x-layouts.app>
