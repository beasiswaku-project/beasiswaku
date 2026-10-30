<x-layouts.guest title="Kata sandi baru">
    <section class="hidden flex-col justify-center bg-blue-50 px-12 py-16 md:flex">
        <p class="text-2xl font-semibold text-blue-500">{{ config('app.name') }}</p>
        <h1 class="mt-14 text-4xl leading-tight font-semibold tracking-tight">Buat kata sandi baru</h1>
        <p class="mt-4 max-w-md text-lg text-gray-500">Gunakan kata sandi yang kuat dan belum pernah kamu pakai sebelumnya.</p>
    </section>

    <section class="flex flex-col justify-center px-6 py-12 sm:px-16">
        <div class="mx-auto w-full max-w-md">
            <h2 class="text-2xl font-semibold tracking-tight">Atur ulang kata sandi</h2>

            <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-5" novalidate>
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <x-form.field name="email" type="email" label="Email" :value="$email" autocomplete="username" required />
                <x-form.field name="password" type="password" label="Kata sandi baru" autocomplete="new-password" required autofocus />
                <x-form.field name="password_confirmation" type="password" label="Konfirmasi kata sandi" autocomplete="new-password" required />

                <x-button class="w-full">Simpan kata sandi</x-button>
            </form>
        </div>
    </section>
</x-layouts.guest>
