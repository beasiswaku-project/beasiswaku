@props([
    'title' => null,
    'breadcrumbs' => [],
])

<x-layouts.base :title="$title" class="bg-blue-50/60">
    <div class="flex min-h-screen">
        <x-layouts.app.sidebar />

        <div class="flex min-w-0 flex-1 flex-col">
            <x-layouts.app.header :breadcrumbs="$breadcrumbs" />

            <main class="flex-1 px-4 py-8 sm:px-10">
                {{ $slot }}
            </main>
        </div>
    </div>
</x-layouts.base>
