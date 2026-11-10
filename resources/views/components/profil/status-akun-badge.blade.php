@props(['user'])

@if ($user->is_active)
    <span {{ $attributes->class('rounded-md bg-green-50 px-2 py-0.5 text-xs font-medium whitespace-nowrap text-green-700') }}>Aktif</span>
@else
    <span {{ $attributes->class('rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium whitespace-nowrap text-gray-600') }}>Nonaktif</span>
@endif
