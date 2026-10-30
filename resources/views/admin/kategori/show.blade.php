<x-layouts.app :title="$kategori->nama_kategori" :breadcrumbs="['Admin', 'Data master', 'Kategori', $kategori->nama_kategori]">
    <a href="{{ route('admin.kategori.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-900">
        <x-icon name="chevron-left" class="size-4" />
        Kembali ke daftar kategori
    </a>

    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight">{{ $kategori->nama_kategori }}</h1>
            <p class="mt-2 max-w-2xl text-gray-500">{{ $kategori->deskripsi ?? 'Belum ada deskripsi.' }}</p>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold transition hover:bg-gray-50"
                data-dialog-target="kategori-dialog"
            >
                <x-icon name="pencil" class="size-4" />
                Ubah
            </button>

            <x-kategori.delete-button :kategori="$kategori" :programs-count="$kategori->programs->count()" />
        </div>
    </div>

    <x-flash class="mt-6" />

    <dl class="mt-8 grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <dt class="text-sm text-gray-500">Jumlah program</dt>
            <dd class="mt-1 text-2xl font-semibold">{{ $kategori->programs->count() }}</dd>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <dt class="text-sm text-gray-500">Dibuat</dt>
            <dd class="mt-1 text-lg font-semibold">{{ $kategori->created_at?->translatedFormat('j F Y') }}</dd>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <dt class="text-sm text-gray-500">Diperbarui</dt>
            <dd class="mt-1 text-lg font-semibold">{{ $kategori->updated_at?->translatedFormat('j F Y, H:i') }}</dd>
        </div>
    </dl>

    <section class="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white">
        <h2 class="border-b border-gray-200 px-5 py-4 font-semibold">Program dalam kategori ini</h2>

        <ul class="divide-y divide-gray-100">
            @forelse ($kategori->programs as $program)
                <li class="flex items-center justify-between gap-4 px-5 py-4 text-sm">
                    <span class="font-medium">{{ $program->judul }}</span>
                    <span class="rounded-md bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-600 capitalize">{{ $program->status }}</span>
                </li>
            @empty
                <li class="px-5 py-10 text-center text-sm text-gray-500">Belum ada program yang memakai kategori ini.</li>
            @endforelse
        </ul>
    </section>

    <x-modal id="kategori-dialog" title="Ubah kategori" :data-open-on-load="$errors->any()">
        <form method="POST" action="{{ route('admin.kategori.update', $kategori) }}" class="space-y-5 px-6 py-5" novalidate>
            @csrf
            @method('PUT')

            <x-form.field name="nama_kategori" label="Nama kategori" :value="$kategori->nama_kategori" maxlength="100" required />
            <x-form.textarea name="deskripsi" label="Deskripsi" :value="$kategori->deskripsi" maxlength="1000" />

            <div class="flex justify-end gap-3 pt-2">
                <x-button variant="secondary" type="button" data-dialog-close>Batal</x-button>
                <x-button>Simpan</x-button>
            </div>
        </form>
    </x-modal>
</x-layouts.app>
