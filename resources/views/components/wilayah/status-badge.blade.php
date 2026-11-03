@props(['wilayah'])

@if ($wilayah->is_3t)
    <span {{ $attributes->class('rounded-md bg-amber-50 px-2 py-0.5 text-xs font-medium whitespace-nowrap text-amber-700') }}>Wilayah 3T</span>
@else
    <span {{ $attributes->class('rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium whitespace-nowrap text-gray-600') }}>Non-3T</span>
@endif
