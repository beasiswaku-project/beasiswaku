<x-layouts.app title="Rekomendasi beasiswa" :breadcrumbs="['Mahasiswa', 'Beasiswa', 'Rekomendasi']">
    <h1 class="text-3xl font-semibold tracking-tight">Rekomendasi untukmu</h1>
    <p class="mt-2 text-gray-500">Beasiswa yang dibuka untuk jenjangmu dan semua syarat wajibnya terpenuhi oleh profilmu saat ini.</p>

    <x-beasiswa.katalog-tabs class="mt-8" />

    @if (! $profil)
        <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6">
            <p class="text-gray-700">Isi profil terlebih dahulu agar kami bisa mencocokkan beasiswa untukmu.</p>
            <a href="{{ route('mahasiswa.profil.show') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700">
                Isi profil
                <x-icon name="arrow-right" class="size-4" />
            </a>
        </div>
    @elseif ($rekomendasi->isEmpty())
        <p class="mt-6 rounded-xl border border-gray-200 bg-white px-5 py-12 text-center text-gray-500">
            Belum ada beasiswa yang cocok dengan profilmu. Pastikan profil dan riwayat IPK sudah lengkap, atau lihat seluruh katalog.
        </p>
    @else
        <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($rekomendasi as $hasil)
                <x-program.kartu :program="$hasil['program']">
                    <x-slot:badge>
                        @if ($hasil['persentase'] === null)
                            <span class="rounded-md bg-gray-100 px-2 py-0.5 text-gray-600">Terbuka umum</span>
                        @else
                            <span class="rounded-md bg-green-50 px-2 py-0.5 text-green-700">{{ $hasil['persentase'] }}% sesuai</span>
                        @endif
                    </x-slot:badge>

                    @if ($hasil['persentase'] !== null)
                        <p class="mt-3 text-sm text-gray-500">{{ $hasil['terpenuhi'] }} dari {{ $hasil['total'] }} syarat terpenuhi.</p>
                    @endif
                </x-program.kartu>
            @endforeach
        </div>
    @endif
</x-layouts.app>
