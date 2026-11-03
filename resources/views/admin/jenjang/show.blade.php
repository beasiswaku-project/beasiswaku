<x-layouts.app :title="$jenjang->nama_jenjang" :breadcrumbs="['Admin', 'Data master', 'Jenjang', $jenjang->nama_jenjang]">
    <a href="{{ route('admin.jenjang.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-900">
        <x-icon name="chevron-left" class="size-4" />
        Kembali ke daftar jenjang
    </a>

    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight">{{ $jenjang->nama_jenjang }}</h1>
            <p class="mt-2 max-w-2xl text-gray-500">{{ $jenjang->keterangan ?? 'Belum ada keterangan.' }}</p>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold transition hover:bg-gray-50"
                data-dialog-target="jenjang-dialog"
            >
                <x-icon name="pencil" class="size-4" />
                Ubah
            </button>

            <x-jenjang.delete-button
                :jenjang="$jenjang"
                :usage-count="$jenjang->mahasiswa_count + $jenjang->riwayat_pendidikan_count + $jenjang->programs_count"
            />
        </div>
    </div>

    <x-flash class="mt-6" />

    <dl class="mt-8 grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <dt class="text-sm text-gray-500">Profil mahasiswa</dt>
            <dd class="mt-1 text-2xl font-semibold">{{ $jenjang->mahasiswa_count }}</dd>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <dt class="text-sm text-gray-500">Riwayat pendidikan</dt>
            <dd class="mt-1 text-2xl font-semibold">{{ $jenjang->riwayat_pendidikan_count }}</dd>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <dt class="text-sm text-gray-500">Jumlah program</dt>
            <dd class="mt-1 text-2xl font-semibold">{{ $jenjang->programs_count }}</dd>
        </div>
    </dl>

    <section class="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white">
        <h2 class="border-b border-gray-200 px-5 py-4 font-semibold">Program dengan sasaran jenjang ini</h2>

        <ul class="divide-y divide-gray-100">
            @forelse ($jenjang->programs as $program)
                <li class="flex items-center justify-between gap-4 px-5 py-4 text-sm">
                    <span class="font-medium">{{ $program->judul }}</span>
                    <span class="rounded-md bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-600 capitalize">{{ $program->status }}</span>
                </li>
            @empty
                <li class="px-5 py-10 text-center text-sm text-gray-500">Belum ada program yang menyasar jenjang ini.</li>
            @endforelse
        </ul>
    </section>

    <x-modal id="jenjang-dialog" title="Ubah jenjang" :data-open-on-load="$errors->any()">
        <form method="POST" action="{{ route('admin.jenjang.update', $jenjang) }}" class="space-y-5 px-6 py-5" novalidate>
            @csrf
            @method('PUT')

            <x-form.field name="nama_jenjang" label="Nama jenjang" :value="$jenjang->nama_jenjang" maxlength="50" required />
            <x-form.field name="keterangan" label="Keterangan" :value="$jenjang->keterangan" maxlength="255" />

            <div class="flex justify-end gap-3 pt-2">
                <x-button variant="secondary" type="button" data-dialog-close>Batal</x-button>
                <x-button>Simpan</x-button>
            </div>
        </form>
    </x-modal>
</x-layouts.app>
