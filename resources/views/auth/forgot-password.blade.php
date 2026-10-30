<x-layouts.guest title="Lupa kata sandi">
    <section class="hidden flex-col justify-center bg-blue-50 px-12 py-16 md:flex">
        <p class="text-2xl font-semibold text-blue-500">{{ config('app.name') }}</p>
        <h1 class="mt-14 text-4xl leading-tight font-semibold tracking-tight">Lupa kata sandi?</h1>
        <p class="mt-4 max-w-md text-lg text-gray-500">Tenang, kami akan membantumu mendapatkan kembali akses ke akunmu.</p>
    </section>

    <section class="flex flex-col justify-center px-6 py-12 sm:px-16">
        <div class="mx-auto w-full max-w-md">
            <h2 class="text-2xl font-semibold tracking-tight">Atur ulang kata sandi</h2>
            <p class="mt-3 text-sm text-gray-500">Masukkan email akunmu dan kami akan mengirimkan tautan untuk membuat kata sandi baru.</p>

            <x-auth-status :status="session('status')" class="mt-6" />

            <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-5" novalidate>
                @csrf

                <x-form.field name="email" type="email" label="Email" placeholder="nama@email.com" autocomplete="username" required autofocus />

                <x-button>Kirim tautan</x-button>
            </form>

            <p class="mt-8 text-center text-sm text-gray-600">
                Ingat kata sandimu?
                <a href="{{ route('login') }}" class="font-medium text-blue-500 hover:text-blue-600">Kembali masuk</a>
            </p>
        </div>
    </section>
</x-layouts.guest>
