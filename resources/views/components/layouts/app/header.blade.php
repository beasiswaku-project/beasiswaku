@props(['breadcrumbs' => []])

@php($user = auth()->user())

<header class="sticky top-0 z-10 flex h-20 items-center justify-between gap-4 border-b border-gray-200 bg-white px-4 sm:px-8">
    <nav aria-label="Breadcrumb" class="min-w-0">
        <ol class="flex items-center gap-3 text-sm text-gray-500">
            @foreach ($breadcrumbs as $crumb)
                <li @class(['flex items-center gap-3', 'font-medium text-gray-900' => $loop->last])>
                    @unless ($loop->first)
                        <x-icon name="chevron-right" class="size-4 text-gray-400" />
                    @endunless
                    <span class="truncate" @if ($loop->last) aria-current="page" @endif>{{ $crumb }}</span>
                </li>
            @endforeach
        </ol>
    </nav>

    <div class="flex items-center gap-4">
        <button type="button" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100" aria-label="Notifikasi">
            <x-icon name="bell" />
        </button>

        <div class="hidden h-8 w-px bg-gray-200 sm:block"></div>

        <div class="flex items-center gap-3">
            <span class="flex size-10 items-center justify-center rounded-full bg-blue-50 text-sm font-semibold text-blue-500">{{ $user->initials }}</span>
            <div class="hidden sm:block">
                <p class="text-sm font-semibold">{{ $user->nama }}</p>
                <p class="text-xs text-gray-500">{{ $user->role->label() }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="lg:hidden">
            @csrf
            <button type="submit" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100" aria-label="Keluar">
                <x-icon name="log-out" />
            </button>
        </form>
    </div>
</header>
