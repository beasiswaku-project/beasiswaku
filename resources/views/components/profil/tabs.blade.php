@php
    /**
     * Sections of the student's own profile.
     *
     * @var array<int, array{label: string, route: string, active: string}> $tabs
     */
    $tabs = [
        ['label' => 'Data diri', 'route' => 'mahasiswa.profil.show', 'active' => 'mahasiswa.profil.*'],
        ['label' => 'Riwayat IPK', 'route' => 'mahasiswa.riwayat-pendidikan.index', 'active' => 'mahasiswa.riwayat-pendidikan.*'],
    ];
@endphp

<nav aria-label="Profil" {{ $attributes->class('inline-flex max-w-full gap-1 overflow-x-auto rounded-xl bg-gray-100 p-1') }}>
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
