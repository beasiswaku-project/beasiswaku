@php($isFiltered = $search !== '' || array_filter($filter) !== [])

<x-layouts.app title="Pendaftaran" :breadcrumbs="['Admin', 'Seleksi', 'Pendaftaran']">
    <h1 class="text-3xl font-semibold tracking-tight">Pendaftaran</h1>
    <p class="mt-2 text-gray-500">Semua pendaftaran yang sudah dikirim mahasiswa.</p>

    <x-flash class="mt-6" />

    <section class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="border-b border-gray-200 p-5">
            <form method="GET" action="{{ route('admin.pendaftaran.index') }}" role="search" class="flex w-full flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
                <div class="relative w-full sm:max-w-xs">
                    <label for="q" class="sr-only">Cari pendaftaran</label>
                    <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 size-4 -translate-y-1/2 text-gray-400" />
                    <input
                        id="q"
                        name="q"
                        type="search"
                        value="{{ $search }}"
                        placeholder="Cari nama atau program"
                        class="block w-full rounded-lg border border-gray-200 bg-white py-2.5 pr-3.5 pl-10 text-sm placeholder:text-gray-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none"
                    >
                </div>

                <label for="filter-program" class="sr-only">Filter program</label>
                <select id="filter-program" name="program" class="block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none sm:w-auto sm:max-w-xs">
                    <option value="">Semua program</option>
                    @foreach ($daftarProgram as $program)
                        <option value="{{ $program->id }}" @selected(($filter['program'] ?? '') == $program->id)>{{ $program->judul }}</option>
                    @endforeach
                </select>

                <label for="filter-status" class="sr-only">Filter status</label>
                <select id="filter-status" name="status" class="block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none sm:w-auto">
                    <option value="">Semua status</option>
                    @foreach ($daftarStatus as $status)
                        <option value="{{ $status->value }}" @selected(($filter['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>

                <div class="flex items-center gap-3">
                    <x-button variant="secondary">Terapkan</x-button>
                    @if ($isFiltered)
                        <a href="{{ route('admin.pendaftaran.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-900">
                    <tr>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Pendaftar</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Program</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Dikirim</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($pendaftaran as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4">
                                <a href="{{ route('admin.pendaftaran.show', $item) }}" class="font-medium hover:text-blue-600">{{ $item->mahasiswa->user->nama }}</a>
                                <p class="text-gray-500">{{ $item->mahasiswa->user->email }}</p>
                            </td>
                            <td class="px-5 py-4">{{ $item->program->judul }}</td>
                            <td class="px-5 py-4 whitespace-nowrap text-gray-500">{{ $item->tgl_kirim?->translatedFormat('j M Y, H:i') }}</td>
                            <td class="px-5 py-4"><x-pendaftaran.status-badge :pendaftaran="$item" /></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center text-gray-500">
                                {{ $isFiltered ? 'Tidak ada pendaftaran yang cocok dengan filter ini.' : 'Belum ada pendaftaran yang dikirim.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pendaftaran->hasPages())
            <div class="border-t border-gray-200 px-5 py-4">
                {{ $pendaftaran->links() }}
            </div>
        @endif
    </section>
</x-layouts.app>
