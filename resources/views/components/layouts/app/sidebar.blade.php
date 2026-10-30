@use('App\Enums\UserRole')

@php
    $user = auth()->user();

    /**
     * Menu items without a route are features that are not built yet.
     *
     * @var array<int, array{label: string, icon: string, route: string|null}> $menu
     */
    $menu = match ($user->role) {
        UserRole::Admin => [
            ['label' => 'Dashboard', 'icon' => 'layout-grid', 'route' => 'admin.dashboard'],
            ['label' => 'Beasiswa', 'icon' => 'graduation-cap', 'route' => null],
            ['label' => 'Seleksi', 'icon' => 'clipboard-check', 'route' => null],
            ['label' => 'Pengguna', 'icon' => 'users', 'route' => null],
            ['label' => 'Laporan', 'icon' => 'file-text', 'route' => null],
            ['label' => 'Pengaturan', 'icon' => 'settings', 'route' => null],
        ],
        UserRole::Mahasiswa => [
            ['label' => 'Dashboard', 'icon' => 'layout-grid', 'route' => 'mahasiswa.dashboard'],
            ['label' => 'Profil', 'icon' => 'user', 'route' => null],
            ['label' => 'Beasiswa', 'icon' => 'graduation-cap', 'route' => null],
            ['label' => 'Pendaftaran', 'icon' => 'clipboard-check', 'route' => null],
        ],
    };
@endphp

<aside class="sticky top-0 hidden h-screen w-64 shrink-0 flex-col border-r border-gray-200 bg-white lg:flex">
    <a href="{{ route($user->role->homeRoute()) }}" class="flex h-20 items-center gap-3 border-b border-gray-200 px-6">
        <span class="flex size-9 items-center justify-center rounded-lg bg-blue-500 text-white">
            <x-icon name="sparkles" />
        </span>
        <span class="text-lg font-semibold">{{ config('app.name') }}</span>
    </a>

    <nav class="flex-1 overflow-y-auto px-4 py-6" aria-label="Menu utama">
        <p class="px-3 text-xs font-semibold tracking-wider text-gray-500 uppercase">{{ $user->isAdmin() ? 'Admin menu' : 'Menu' }}</p>

        <ul class="mt-4 space-y-1">
            @foreach ($menu as $item)
                <li>
                    @if ($item['route'])
                        <a
                            href="{{ route($item['route']) }}"
                            @class([
                                'flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium transition',
                                'bg-blue-500 text-white' => request()->routeIs($item['route']),
                                'text-gray-700 hover:bg-gray-100' => ! request()->routeIs($item['route']),
                            ])
                            @if (request()->routeIs($item['route'])) aria-current="page" @endif
                        >
                            <x-icon :name="$item['icon']" />
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="flex cursor-not-allowed items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium text-gray-400" aria-disabled="true" title="Segera hadir">
                            <x-icon :name="$item['icon']" />
                            {{ $item['label'] }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ul>
    </nav>

    <div class="flex items-center gap-3 border-t border-gray-200 px-6 py-5">
        <span class="flex size-10 items-center justify-center rounded-full bg-blue-500 text-sm font-semibold text-white">{{ $user->initials }}</span>
        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-semibold">{{ $user->nama }}</p>
            <p class="text-xs text-gray-500">{{ $user->role->label() }}</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900" aria-label="Keluar" title="Keluar">
                <x-icon name="log-out" class="size-4" />
            </button>
        </form>
    </div>
</aside>
