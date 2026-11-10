@props(['profil'])

@php($persentase = $profil?->persentaseKelengkapan() ?? 0)

{{-- Profile completeness indicator; registration requires a complete profile. --}}
<section {{ $attributes->class('rounded-xl border border-gray-200 bg-white p-5') }} aria-labelledby="kelengkapan-title">
    <div class="flex items-center justify-between gap-4">
        <h2 id="kelengkapan-title" class="font-semibold">Kelengkapan profil</h2>
        <span class="text-sm font-semibold tabular-nums">{{ $persentase }}%</span>
    </div>

    <div class="mt-3 h-2 overflow-hidden rounded-full bg-gray-100" role="progressbar" aria-valuenow="{{ $persentase }}" aria-valuemin="0" aria-valuemax="100" aria-label="Kelengkapan profil">
        <div @class(['h-full rounded-full', 'bg-green-500' => $persentase === 100, 'bg-blue-500' => $persentase < 100]) style="width: {{ $persentase }}%"></div>
    </div>

    @if ($profil)
        <ul class="mt-4 space-y-2 text-sm">
            @foreach ($profil->kelengkapan() as $item => $isSelesai)
                <li class="flex items-center gap-2">
                    @if ($isSelesai)
                        <x-icon name="check" class="size-4 text-green-600" />
                        <span>{{ $item }}</span>
                    @else
                        <x-icon name="x" class="size-4 text-gray-400" />
                        <span class="text-gray-500">{{ $item }} <span class="sr-only">(belum lengkap)</span></span>
                    @endif
                </li>
            @endforeach
        </ul>
    @else
        <p class="mt-4 text-sm text-gray-500">Isi formulir profil untuk mulai mendapatkan rekomendasi beasiswa.</p>
    @endif
</section>
