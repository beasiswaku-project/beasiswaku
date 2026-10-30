@if (session('success'))
    <div {{ $attributes->merge(['class' => 'rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700']) }} role="status">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div {{ $attributes->merge(['class' => 'rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700']) }} role="alert">
        {{ session('error') }}
    </div>
@endif
