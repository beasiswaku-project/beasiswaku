<x-layouts.app title="Dashboard" :breadcrumbs="['Admin', 'Dashboard']">
    <h1 class="text-3xl font-semibold tracking-tight">Dashboard</h1>
    <p class="mt-2 text-gray-500">Selamat datang kembali, {{ auth()->user()->nama }}.</p>
</x-layouts.app>
