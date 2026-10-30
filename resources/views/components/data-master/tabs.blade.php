@php
    /**
     * Tabs without a route are master data modules that are not built yet.
     *
     * @var array<int, array{label: string, route: string|null, active?: string}> $tabs
     */
    $tabs = [
        ['label' => 'Kategori beasiswa', 'route' => 'admin.kategori.index', 'active' => 'admin.kategori.*'],
        ['label' => 'Jenjang pendidikan', 'route' => null],
        ['label' => 'Wilayah', 'route' => null],
        ['label' => 'Prioritas 3T', 'route' => null],
    ];
@endphp

<nav aria-label="Data master" {{ $attributes->class('inline-flex max-w-full gap-1 overflow-x-auto rounded-xl bg-gray-100 p-1') }}>
    @foreach ($tabs as $tab)
        @if ($tab['route'])
            @php($isActive = request()->routeIs($tab['active'] ?? $tab['route']))
            <a
                href="{{ route($tab['route']) }}"
                @class([
                    'rounded-lg px-5 py-2 text-sm font-medium whitespace-nowrap transition',
                    'bg-white text-gray-900 shadow-sm' => $isActive,
                    'text-gray-700 hover:text-gray-900' => ! $isActive,
                ])
                @if ($isActive) aria-current="page" @endif
            >{{ $tab['label'] }}</a>
        @else
            <span class="cursor-not-allowed rounded-lg px-5 py-2 text-sm font-medium whitespace-nowrap text-gray-400" aria-disabled="true" title="Segera hadir">{{ $tab['label'] }}</span>
        @endif
    @endforeach
</nav>
