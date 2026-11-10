@props(['value'])

{{-- Formats an amount as Indonesian rupiah without decimals, e.g. "Rp 1.500.000". --}}
<span {{ $attributes->class('tabular-nums') }}>{{ \Illuminate\Support\Number::currency((float) $value, in: 'IDR', locale: 'id', precision: 0) }}</span>
