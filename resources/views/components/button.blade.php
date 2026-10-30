@props(['variant' => 'primary'])

<button {{ $attributes->merge(['type' => 'submit'])->class([
    'inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold transition focus:outline-none focus:ring-4 disabled:cursor-not-allowed disabled:opacity-60',
    'bg-blue-500 text-white hover:bg-blue-600 focus:ring-blue-100' => $variant === 'primary',
    'border border-gray-200 bg-white text-gray-900 hover:bg-gray-50 focus:ring-gray-100' => $variant === 'secondary',
]) }}>
    {{ $slot }}
</button>
