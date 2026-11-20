@php
    /**
     * Sections of the student's scholarship menu.
     *
     * @var array<int, array{label: string, route: string, active: string}> $tabs
     */
    $tabs = [
        ['label' => 'Katalog', 'route' => 'beasiswa.index', 'active' => 'beasiswa.*'],
        ['label' => 'Rekomendasi untukmu', 'route' => 'mahasiswa.pencocokan', 'active' => 'mahasiswa.pencocokan'],
    ];
@endphp

<nav aria-label="Beasiswa" {{ $attributes->class('inline-flex max-w-full gap-1 overflow-x-auto rounded-xl bg-gray-100 p-1') }}>
    @foreach ($tabs as $tab)
        @php($active = request()->routeIs($tab['active']))
        <a
            href="{{ route($tab['route']) }}"
            @class([
                'rounded-lg px-5 py-2 text-sm font-medium whitespace-nowrap transition',
                'bg-white text-gray-900 shadow-sm' => $active,
                'text-gray-700 hover:text-gray-900' => ! $active,
            ])
            @if ($active) aria-current="page" @endif
        >{{ $tab['label'] }}</a>
    @endforeach
</nav>
