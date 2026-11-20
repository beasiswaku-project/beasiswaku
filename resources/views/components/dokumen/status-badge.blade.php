@props(['dokumen'])

<span {{ $attributes->class(['rounded-md px-2 py-0.5 text-xs font-medium whitespace-nowrap', $dokumen->status_verifikasi->badgeClass()]) }}>{{ $dokumen->status_verifikasi->label() }}</span>
