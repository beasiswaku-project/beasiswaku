@props(['program'])

{{-- Catalogue card for an open program; extra content (e.g. a match label) goes in the slot. --}}
<article {{ $attributes->class('flex flex-col rounded-xl border border-gray-200 bg-white p-5') }}>
    <div class="flex flex-wrap items-center gap-2 text-xs font-medium">
        <span class="rounded-md bg-blue-50 px-2 py-0.5 text-blue-700">{{ $program->kategori->nama_kategori }}</span>
        <span class="rounded-md bg-gray-100 px-2 py-0.5 text-gray-600">{{ $program->jenjang->nama_jenjang }}</span>
        {{ $badge ?? '' }}
    </div>

    <h2 class="mt-3 text-lg font-semibold">
        <a href="{{ route('beasiswa.show', $program) }}" class="hover:text-blue-600">{{ $program->judul }}</a>
    </h2>
    <p class="mt-1 text-sm text-gray-500">{{ $program->penyedia->nama_penyedia }}</p>

    {{ $slot }}

    <dl class="mt-auto grid grid-cols-2 gap-3 pt-5 text-sm">
        <div>
            <dt class="text-gray-500">Deadline</dt>
            <dd class="font-medium">{{ $program->deadline?->translatedFormat('j M Y') ?? '–' }}</dd>
        </div>
        <div>
            <dt class="text-gray-500">Kuota</dt>
            <dd class="font-medium">{{ $program->kuota }} penerima</dd>
        </div>
    </dl>
</article>
