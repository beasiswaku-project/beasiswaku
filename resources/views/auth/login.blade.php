<x-layouts.guest title="Masuk">
    <section class="hidden flex-col justify-center bg-blue-50 px-12 py-16 md:flex">
        <p class="text-2xl font-semibold text-blue-500">{{ config('app.name') }}</p>

        <h1 class="mt-14 text-4xl leading-tight font-semibold tracking-tight">Akses pendidikan yang lebih setara</h1>
        <p class="mt-4 max-w-md text-lg text-gray-500">Temukan beasiswa yang sesuai dengan kebutuhan dan potensimu.</p>

        <ul class="mt-12 space-y-4 text-sm text-gray-800">
            <li>Rekomendasi personal</li>
            <li>Verifikasi transparan</li>
            <li>Dukungan sampai lulus</li>
        </ul>
    </section>

    <section class="flex flex-col justify-center px-6 py-12 sm:px-16">
        <div class="mx-auto w-full max-w-md">
            <h2 class="text-2xl font-semibold tracking-tight">Masuk ke {{ config('app.name') }}</h2>
            <p class="mt-3 text-sm text-gray-500">Gunakan akunmu untuk melanjutkan</p>

            <x-auth-status :status="session('status')" class="mt-6" />

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5" novalidate>
                @csrf

                <x-form.field name="email" type="email" label="Email" placeholder="nama@email.com" autocomplete="username" required autofocus />
                <x-form.field name="password" type="password" label="Kata sandi" placeholder="Masukkan kata sandi" autocomplete="current-password" required />

                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center gap-2.5 text-sm text-gray-900">
                        <input type="checkbox" name="remember" value="1" @checked(old('remember')) class="size-4 rounded border-gray-300 text-blue-500 focus:ring-blue-500">
                        Ingat saya
                    </label>

                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-500 hover:text-blue-600">Lupa kata sandi?</a>
                </div>

                <x-button>Masuk</x-button>
            </form>

            <p class="mt-8 text-center text-sm text-gray-600">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-medium text-blue-500 hover:text-blue-600">Daftar sekarang</a>
            </p>

            <p class="mt-8 text-center text-xs text-gray-500">Mahasiswa atau administrator</p>
        </div>
    </section>
</x-layouts.guest>
