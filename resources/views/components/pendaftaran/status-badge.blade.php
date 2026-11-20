@props(['pendaftaran'])

<span {{ $attributes->class(['rounded-md px-2 py-0.5 text-xs font-medium whitespace-nowrap', $pendaftaran->status->badgeClass()]) }}>{{ $pendaftaran->status->label() }}</span>
