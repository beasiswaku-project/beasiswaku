<x-layouts.app title="Profil" :breadcrumbs="['Mahasiswa', 'Profil']">
    <h1 class="text-3xl font-semibold tracking-tight">Profil saya</h1>
    <p class="mt-2 text-gray-500">Data ini dipakai untuk mencocokkan beasiswa dan menghitung skor kebutuhan ekonomi.</p>

    <x-flash class="mt-6" />

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <form
            method="POST"
            action="{{ $profil ? route('mahasiswa.profil.update') : route('mahasiswa.profil.store') }}"
            class="space-y-8 rounded-xl border border-gray-200 bg-white p-6 lg:col-span-2"
            novalidate
        >
            @csrf
            @if ($profil)
                @method('PUT')
            @endif

            <fieldset class="space-y-5">
                <legend class="text-lg font-semibold">Identitas</legend>

                <div class="grid gap-5 sm:grid-cols-2">
                    <x-form.field name="nama" label="Nama lengkap" :value="$user->nama" maxlength="100" autocomplete="name" required />
                    <x-form.field name="no_telepon" label="Nomor telepon" type="tel" :value="$user->no_telepon" placeholder="08xxxxxxxxxx" autocomplete="tel" required />
                </div>

                <p class="text-sm text-gray-500">Email {{ $user->email }} · NIK {{ $user->nik }}</p>
            </fieldset>

            <fieldset class="space-y-5">
                <legend class="text-lg font-semibold">Akademik dan asal</legend>

                <x-form.field name="kampus" label="Nama kampus" :value="$profil?->kampus" maxlength="150" placeholder="Contoh: Universitas Telkom" required />

                <div class="grid gap-5 sm:grid-cols-2">
                    <x-form.select name="jenjang_id" label="Jenjang" placeholder="Pilih jenjang" required>
                        @foreach ($daftarJenjang as $jenjang)
                            <option value="{{ $jenjang->id }}" @selected(old('jenjang_id', $profil?->jenjang_id) == $jenjang->id)>{{ $jenjang->nama_jenjang }}</option>
                        @endforeach
                    </x-form.select>

                    <x-form.select name="wilayah_id" label="Wilayah asal (kabupaten/kota)" placeholder="Pilih wilayah" required>
                        @foreach ($daftarWilayah as $provinsi => $wilayahProvinsi)
                            <optgroup label="{{ $provinsi }}">
                                @foreach ($wilayahProvinsi as $wilayah)
                                    <option value="{{ $wilayah->id }}" @selected(old('wilayah_id', $profil?->wilayah_id) == $wilayah->id)>{{ $wilayah->kabupaten }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </x-form.select>
                </div>
            </fieldset>

            <fieldset class="space-y-5">
                <legend class="text-lg font-semibold">Kondisi ekonomi</legend>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <x-form.field
                            name="penghasilan_ortu"
                            label="Penghasilan orang tua per bulan (Rp)"
                            type="number"
                            :value="$profil ? (int) $profil->penghasilan_ortu : null"
                            min="0"
                            step="1"
                            inputmode="numeric"
                            required
                        />
                        <p class="mt-1.5 text-sm text-gray-500">Isi 0 jika tidak ada penghasilan tetap.</p>
                    </div>

                    <div>
                        <x-form.field name="jml_tanggungan" label="Jumlah tanggungan orang tua" type="number" :value="$profil?->jml_tanggungan" min="1" max="30" step="1" inputmode="numeric" required />
                        <p class="mt-1.5 text-sm text-gray-500">Termasuk kamu sendiri.</p>
                    </div>
                </div>

                <x-form.checkbox name="punya_kipk" label="Penerima KIP Kuliah" description="Bukti KIP-K wajib diunggah saat mendaftar beasiswa." :checked="$profil?->punya_kipk ?? false" />

                <x-form.checkbox name="terdaftar_dtsen" label="Terdaftar di DTSEN" description="Data Tunggal Sosial dan Ekonomi Nasional." :checked="$profil?->terdaftar_dtsen ?? false" />

                <div class="sm:w-1/2">
                    <x-form.field name="desil_dtsen" label="Desil DTSEN (1–10)" type="number" :value="$profil?->desil_dtsen" min="1" max="10" step="1" inputmode="numeric" />
                    <p class="mt-1.5 text-sm text-gray-500">Wajib diisi jika terdaftar di DTSEN.</p>
                </div>

                <x-form.textarea name="kondisi_khusus" label="Kondisi khusus (opsional)" :value="$profil?->kondisi_khusus" maxlength="255" placeholder="Contoh: yatim piatu, penyandang disabilitas" />
            </fieldset>

            <div class="flex justify-end">
                <x-button>{{ $profil ? 'Simpan perubahan' : 'Simpan profil' }}</x-button>
            </div>
        </form>

        <x-profil.kelengkapan :profil="$profil" class="self-start" />
    </div>
</x-layouts.app>
