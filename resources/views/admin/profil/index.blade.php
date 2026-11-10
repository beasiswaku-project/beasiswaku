@php($isFiltered = $search !== '' || in_array($status, ['aktif', 'nonaktif'], true))

<x-layouts.app title="Profil mahasiswa" :breadcrumbs="['Admin', 'Pengguna', 'Profil mahasiswa']">
    <h1 class="text-3xl font-semibold tracking-tight">Profil mahasiswa</h1>
    <p class="mt-2 text-gray-500">Lihat data akademik dan ekonomi mahasiswa yang terdaftar.</p>

    <x-flash class="mt-6" />

    <section class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="border-b border-gray-200 p-5">
            <form method="GET" action="{{ route('admin.profil.index') }}" role="search" class="flex w-full flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
                <div class="relative w-full sm:max-w-xs">
                    <label for="q" class="sr-only">Cari mahasiswa</label>
                    <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 size-4 -translate-y-1/2 text-gray-400" />
                    <input
                        id="q"
                        name="q"
                        type="search"
                        value="{{ $search }}"
                        placeholder="Cari nama, email, atau kampus"
                        class="block w-full rounded-lg border border-gray-200 bg-white py-2.5 pr-3.5 pl-10 text-sm placeholder:text-gray-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none"
                    >
                </div>

                <label for="filter-status" class="sr-only">Filter status akun</label>
                <select
                    id="filter-status"
                    name="status"
                    class="block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none sm:w-auto"
                >
                    <option value="">Semua status</option>
                    <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected($status === 'nonaktif')>Nonaktif</option>
                </select>

                <div class="flex items-center gap-3">
                    <x-button variant="secondary">Terapkan</x-button>
                    @if ($isFiltered)
                        <a href="{{ route('admin.profil.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-900">
                    <tr>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Nama</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Kampus</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Jenjang</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Wilayah</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Kelengkapan</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($profil as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4">
                                <a href="{{ route('admin.profil.show', $item) }}" class="font-medium hover:text-blue-600">{{ $item->user->nama }}</a>
                                <p class="text-gray-500">{{ $item->user->email }}</p>
                            </td>
                            <td class="px-5 py-4 text-gray-500">{{ $item->kampus ?? '–' }}</td>
                            <td class="px-5 py-4 whitespace-nowrap">{{ $item->jenjang->nama_jenjang }}</td>
                            <td class="px-5 py-4">
                                <span class="whitespace-nowrap">{{ $item->wilayah->kabupaten }}</span>
                                <x-wilayah.status-badge :wilayah="$item->wilayah" class="ml-1" />
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap tabular-nums">{{ $item->persentaseKelengkapan() }}%</td>
                            <td class="px-5 py-4"><x-profil.status-akun-badge :user="$item->user" /></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-500">
                                @if ($isFiltered)
                                    Tidak ada profil yang cocok dengan filter ini.
                                @else
                                    Belum ada mahasiswa yang mengisi profil.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($profil->hasPages())
            <div class="border-t border-gray-200 px-5 py-4">
                {{ $profil->links() }}
            </div>
        @endif
    </section>
</x-layouts.app>
