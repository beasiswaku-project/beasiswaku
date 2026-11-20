@props(['esai'])

{{-- Read-only essay; whitespace is preserved and content is escaped. --}}
<article {{ $attributes->class('rounded-xl border border-gray-200 bg-white p-6') }}>
    <div class="flex flex-wrap items-center justify-between gap-2">
        <h2 class="text-lg font-semibold">{{ $esai->judul ?? 'Tanpa judul' }}</h2>
        <span class="text-sm text-gray-500">{{ $esai->status->label() }} · {{ $esai->jumlahKata() }} kata</span>
    </div>
    <div class="mt-4 text-sm leading-relaxed whitespace-pre-line text-gray-800">{{ $esai->isi }}</div>
</article>
