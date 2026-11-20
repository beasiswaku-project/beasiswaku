<x-layouts.app :title="$program->judul" :breadcrumbs="['Admin', 'Beasiswa', 'Program', $program->judul]">
    <a href="{{ route('admin.program.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-900">
        <x-icon name="chevron-left" class="size-4" />
        Kembali ke daftar program
    </a>

    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-3xl font-semibold tracking-tight">{{ $program->judul }}</h1>
                <x-program.status-badge :program="$program" />
            </div>
            <p class="mt-2 text-gray-500">{{ $program->penyedia->nama_penyedia }} · {{ $program->kategori->nama_kategori }} · {{ $program->jenjang->nama_jenjang }}</p>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold transition hover:bg-gray-50"
                data-dialog-target="program-dialog"
            >
                <x-icon name="pencil" class="size-4" />
                Ubah
            </button>

            <x-program.delete-button :program="$program" :pendaftaran-count="$program->pendaftaran_count" />
        </div>
    </div>

    <x-flash class="mt-6" />

    <dl class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <dt class="text-sm text-gray-500">Kuota</dt>
            <dd class="mt-1 text-2xl font-semibold">{{ $program->kuota }}</dd>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <dt class="text-sm text-gray-500">Pendaftar</dt>
            <dd class="mt-1 text-2xl font-semibold">{{ $program->pendaftaran_count }}</dd>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <dt class="text-sm text-gray-500">Periode</dt>
            <dd class="mt-1 font-semibold">{{ $program->tgl_buka?->translatedFormat('j M Y') ?? '–' }} – {{ $program->deadline?->translatedFormat('j M Y') ?? '–' }}</dd>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <dt class="text-sm text-gray-500">Konfigurasi penilaian</dt>
            <dd class="mt-1 font-semibold">Versi {{ $program->konfigurasi->versi }}</dd>
        </div>
    </dl>

    <section class="mt-6 rounded-xl border border-gray-200 bg-white p-5" aria-labelledby="status-title">
        <h2 id="status-title" class="font-semibold">Status program</h2>
        <p class="mt-1 text-sm text-gray-500">Hanya program berstatus Dibuka yang tampil di katalog mahasiswa.</p>

        <form method="POST" action="{{ route('admin.program.status', $program) }}" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
            @csrf
            @method('PATCH')

            <x-form.select name="status" label="Ubah status" class="sm:w-60">
                @foreach ($daftarStatus as $status)
                    <option value="{{ $status->value }}" @selected(old('status', $program->status->value) === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </x-form.select>

            <x-button variant="secondary">Simpan status</x-button>
        </form>
    </section>

    <x-modal id="program-dialog" title="Ubah program" :data-open-on-load="$errors->hasAny(['judul', 'penyedia_id', 'kategori_id', 'jenjang_id', 'kuota', 'tgl_buka', 'deadline'])">
        <form method="POST" action="{{ route('admin.program.update', $program) }}" class="space-y-5 px-6 py-5" novalidate>
            @csrf
            @method('PUT')

            <x-program.form-fields :daftar-penyedia="$daftarPenyedia" :daftar-kategori="$daftarKategori" :daftar-jenjang="$daftarJenjang" :program="$program" />

            <div class="flex justify-end gap-3 pt-2">
                <x-button variant="secondary" type="button" data-dialog-close>Batal</x-button>
                <x-button>Simpan</x-button>
            </div>
        </form>
    </x-modal>
</x-layouts.app>
