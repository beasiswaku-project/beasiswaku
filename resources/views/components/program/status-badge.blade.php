@props(['program'])

<span {{ $attributes->class(['rounded-md px-2 py-0.5 text-xs font-medium whitespace-nowrap', $program->status->badgeClass()]) }}>{{ $program->status->label() }}</span>
