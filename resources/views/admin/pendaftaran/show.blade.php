<x-layouts.app :title="$pendaftaran->mahasiswa->user->nama" :breadcrumbs="['Admin', 'Seleksi', 'Pendaftaran', $pendaftaran->mahasiswa->user->nama]">
    <a href="{{ route('admin.pendaftaran.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-900">
        <x-icon name="chevron-left" class="size-4" />
        Kembali ke daftar pendaftaran
    </a>

    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-3xl font-semibold tracking-tight">{{ $pendaftaran->mahasiswa->user->nama }}</h1>
                <x-pendaftaran.status-badge :pendaftaran="$pendaftaran" />
            </div>
            <p class="mt-2 text-gray-500">{{ $pendaftaran->program->judul }} · {{ $pendaftaran->program->penyedia->nama_penyedia }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.profil.show', $pendaftaran->mahasiswa) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold transition hover:bg-gray-50">Profil</a>
            <a href="{{ route('admin.pendaftaran.prestasi', $pendaftaran) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold transition hover:bg-gray-50">Prestasi</a>
        </div>
    </div>

    <x-flash class="mt-6" />

    <x-pendaftaran.snapshot :pendaftaran="$pendaftaran" class="mt-6" />

    <section class="mt-6 rounded-xl border border-gray-200 bg-white p-5" aria-labelledby="status-title">
        <h2 id="status-title" class="font-semibold">Status pendaftaran</h2>

        <form method="POST" action="{{ route('admin.pendaftaran.status', $pendaftaran) }}" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
            @csrf
            @method('PATCH')

            <x-form.select name="status" label="Ubah status" class="sm:w-60">
                @foreach ($daftarStatus as $status)
                    <option value="{{ $status->value }}" @selected(old('status', $pendaftaran->status->value) === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </x-form.select>

            <x-button variant="secondary">Simpan status</x-button>
        </form>
    </section>
</x-layouts.app>
