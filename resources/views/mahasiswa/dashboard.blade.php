<x-layouts.app title="Dashboard" :breadcrumbs="['Mahasiswa', 'Dashboard']">
    <h1 class="text-3xl font-semibold tracking-tight">Halo, {{ auth()->user()->nama }}</h1>
    <p class="mt-2 text-gray-500">Lengkapi profilmu untuk mendapatkan rekomendasi beasiswa yang sesuai.</p>
</x-layouts.app>
