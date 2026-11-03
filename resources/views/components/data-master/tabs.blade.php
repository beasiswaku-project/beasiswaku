@php
    /**
     * Tabs without a route are master data modules that are not built yet.
     * `query` adds query-string parameters to the link; a tab with a query is only
     * active when the current request carries the same values, and a tab without
     * one yields to any sibling tab on the same route whose query does match.
     *
     * @var array<int, array{label: string, route: string|null, active?: string, query?: array<string, string>}> $tabs
     */
    $tabs = [
        ['label' => 'Kategori beasiswa', 'route' => 'admin.kategori.index', 'active' => 'admin.kategori.*'],
        ['label' => 'Jenjang pendidikan', 'route' => 'admin.jenjang.index', 'active' => 'admin.jenjang.*'],
        ['label' => 'Wilayah', 'route' => 'admin.wilayah.index', 'active' => 'admin.wilayah.*'],
        ['label' => 'Prioritas 3T', 'route' => 'admin.wilayah.index', 'query' => ['status' => '3t']],
    ];

    $matchesQuery = fn (array $tab): bool => collect($tab['query'] ?? [])
        ->every(fn (string $value, string $key): bool => request()->query($key) === $value);

    $isActive = function (array $tab) use ($tabs, $matchesQuery): bool {
        if (! request()->routeIs($tab['active'] ?? $tab['route']) || ! $matchesQuery($tab)) {
            return false;
        }

        if (isset($tab['query'])) {
            return true;
        }

        return ! collect($tabs)->contains(fn (array $other): bool => isset($other['query'])
            && request()->routeIs($other['route'])
            && $matchesQuery($other));
    };
@endphp

<nav aria-label="Data master" {{ $attributes->class('inline-flex max-w-full gap-1 overflow-x-auto rounded-xl bg-gray-100 p-1') }}>
    @foreach ($tabs as $tab)
        @if ($tab['route'])
            @php($active = $isActive($tab))
            <a
                href="{{ route($tab['route'], $tab['query'] ?? []) }}"
                @class([
                    'rounded-lg px-5 py-2 text-sm font-medium whitespace-nowrap transition',
                    'bg-white text-gray-900 shadow-sm' => $active,
                    'text-gray-700 hover:text-gray-900' => ! $active,
                ])
                @if ($active) aria-current="page" @endif
            >{{ $tab['label'] }}</a>
        @else
            <span class="cursor-not-allowed rounded-lg px-5 py-2 text-sm font-medium whitespace-nowrap text-gray-400" aria-disabled="true" title="Segera hadir">{{ $tab['label'] }}</span>
        @endif
    @endforeach
</nav>
