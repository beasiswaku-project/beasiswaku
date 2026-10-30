@props(['title' => null])

<x-layouts.base :title="$title" class="bg-gray-50">
    <main class="flex min-h-screen items-center justify-center px-4 py-10 sm:px-6">
        <div class="grid w-full max-w-6xl overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm md:min-h-[640px] md:grid-cols-2">
            {{ $slot }}
        </div>
    </main>
</x-layouts.base>
