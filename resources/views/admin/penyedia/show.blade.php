<x-layouts.app :title="$penyedia->nama_penyedia" :breadcrumbs="['Admin', 'Beasiswa', 'Penyedia', $penyedia->nama_penyedia]">
    <a href="{{ route('admin.penyedia.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-900">
        <x-icon name="chevron-left" class="size-4" />
        Kembali ke daftar penyedia
    </a>

    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight">{{ $penyedia->nama_penyedia }}</h1>
            <p class="mt-2 text-gray-500">{{ $penyedia->jenis->label() }}</p>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold transition hover:bg-gray-50"
                data-dialog-target="penyedia-dialog"
            >
                <x-icon name="pencil" class="size-4" />
                Ubah
            </button>

            <x-penyedia.delete-button :penyedia="$penyedia" :programs-count="$penyedia->programs_count" />
        </div>
    </div>

    <x-flash class="mt-6" />

    <dl class="mt-8 grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <dt class="text-sm text-gray-500">Kontak</dt>
            <dd class="mt-1 font-semibold">{{ $penyedia->kontak ?? '–' }}</dd>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <dt class="text-sm text-gray-500">Alamat</dt>
            <dd class="mt-1 font-semibold">{{ $penyedia->alamat ?? '–' }}</dd>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <dt class="text-sm text-gray-500">Program beasiswa</dt>
            <dd class="mt-1 text-2xl font-semibold">{{ $penyedia->programs_count }}</dd>
        </div>
    </dl>

    <x-modal id="penyedia-dialog" title="Ubah penyedia" :data-open-on-load="$errors->any()">
        <form method="POST" action="{{ route('admin.penyedia.update', $penyedia) }}" class="space-y-5 px-6 py-5" novalidate>
            @csrf
            @method('PUT')

            <x-penyedia.form-fields :daftar-jenis="$daftarJenis" :penyedia="$penyedia" />

            <div class="flex justify-end gap-3 pt-2">
                <x-button variant="secondary" type="button" data-dialog-close>Batal</x-button>
                <x-button>Simpan</x-button>
            </div>
        </form>
    </x-modal>
</x-layouts.app>
