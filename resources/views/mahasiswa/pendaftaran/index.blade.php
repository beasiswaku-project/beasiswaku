<x-layouts.app title="Pendaftaran" :breadcrumbs="['Mahasiswa', 'Pendaftaran']">
    <h1 class="text-3xl font-semibold tracking-tight">Pendaftaran saya</h1>
    <p class="mt-2 text-gray-500">Pantau status beasiswa yang sedang kamu daftar.</p>

    <x-flash class="mt-6" />

    <section class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-900">
                    <tr>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Program</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Deadline</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Dikirim</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($pendaftaran as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4">
                                <a href="{{ route('mahasiswa.pendaftaran.show', $item) }}" class="font-medium hover:text-blue-600">{{ $item->program->judul }}</a>
                                <p class="text-gray-500">{{ $item->program->penyedia->nama_penyedia }}</p>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-gray-500">{{ $item->program->deadline?->translatedFormat('j M Y') ?? '–' }}</td>
                            <td class="px-5 py-4 whitespace-nowrap text-gray-500">{{ $item->tgl_kirim?->translatedFormat('j M Y') ?? 'Belum dikirim' }}</td>
                            <td class="px-5 py-4"><x-pendaftaran.status-badge :pendaftaran="$item" /></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center text-gray-500">
                                Kamu belum mendaftar beasiswa.
                                <a href="{{ route('mahasiswa.pencocokan') }}" class="font-medium text-blue-600 hover:text-blue-700">Lihat rekomendasi</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-layouts.app>
