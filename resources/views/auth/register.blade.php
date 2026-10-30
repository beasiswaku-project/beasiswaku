<x-layouts.guest title="Buat akun">
    <section class="px-6 py-12 sm:px-12">
        <h1 class="text-3xl font-semibold tracking-tight">Buat akun mahasiswa</h1>
        <p class="mt-2 text-gray-500">Mulai langkahmu mendapatkan dukungan pendidikan</p>

        <form method="POST" action="{{ route('register') }}" class="mt-8" novalidate>
            @csrf

            <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                <x-form.field name="nama" label="Nama lengkap" autocomplete="name" required autofocus />
                <x-form.field name="nik" label="NIK" inputmode="numeric" maxlength="16" required />
                <x-form.field name="email" type="email" label="Email" autocomplete="email" required />
                <x-form.field name="no_telepon" type="tel" label="Nomor telepon" placeholder="08xxxxxxxxxx" autocomplete="tel" required />
                <x-form.field name="password" type="password" label="Password" autocomplete="new-password" required />
                <x-form.field name="password_confirmation" type="password" label="Konfirmasi password" autocomplete="new-password" required />
            </div>

            <div class="mt-6">
                <label class="inline-flex items-center gap-2.5 text-sm text-gray-600">
                    <input type="checkbox" name="terms" value="1" @checked(old('terms')) class="size-4 rounded border-gray-300 text-blue-500 focus:ring-blue-500">
                    Saya menyetujui syarat dan kebijakan privasi
                </label>

                @error('terms')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <x-button class="mt-8">Buat akun</x-button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-medium text-blue-500 hover:text-blue-600">Masuk</a>
        </p>
    </section>

    <section class="hidden flex-col bg-blue-50 px-12 py-12 md:flex">
        <h2 class="text-2xl leading-snug font-semibold tracking-tight">Satu akun untuk semua kebutuhanmu</h2>

        <ul class="mt-10 space-y-5">
            @foreach ([
                'user' => 'Lengkapi profil',
                'search' => 'Temukan beasiswa',
                'clipboard-check' => 'Pantau proses seleksi',
            ] as $icon => $feature)
                <li class="flex items-center gap-4 font-medium">
                    <span class="flex size-12 items-center justify-center rounded-lg border border-gray-200 bg-white text-blue-500">
                        <x-icon :name="$icon" />
                    </span>
                    {{ $feature }}
                </li>
            @endforeach
        </ul>

        <div class="mt-auto flex items-center justify-center gap-6 rounded-xl border border-blue-100 bg-white/60 py-10 text-blue-500">
            <x-icon name="graduation-cap" class="size-14" />
            <x-icon name="arrow-right" class="size-5" />
            <x-icon name="clipboard-check" class="size-14" />
        </div>
    </section>
</x-layouts.guest>
