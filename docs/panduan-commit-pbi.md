# Panduan Commit dan Push PBI

Kode setiap PBI sudah disiapkan dalam bentuk file patch, satu file per PBI. Tugas setiap anggota adalah menerapkan patch miliknya, lalu commit dan push dari akun GitHub masing-masing. Tujuannya agar riwayat commit tercatat atas nama anggota yang memegang PBI tersebut.

Aturan utama:

- Satu PBI = satu commit. Jangan menggabungkan beberapa PBI dalam satu commit.
- Branch diberi nama sesuai nama anggota, sama seperti branch `Galang`.
- Patch harus diterapkan **sesuai urutan nomor**, dan anggota berikutnya baru mulai setelah Pull Request anggota sebelumnya di-merge ke `develop`.

## 1. Urutan pengerjaan

Semua patch mengubah beberapa file yang sama, misalnya `routes/web.php` dan menu sidebar. Karena itu urutannya tidak boleh dilompati. Kalau urutannya salah, `git apply` akan gagal.

| Giliran | Anggota | Branch | Patch | Mulai setelah |
|---|---|---|---|---|
| 1 | Aufa | `Aufa` | pbi-04, pbi-05, pbi-06 | — |
| 2 | Caesya | `Caesya` | pbi-07, pbi-08, pbi-09 | PR Aufa di-merge |
| 3 | Gina | `Gina` | pbi-10, pbi-11, pbi-12 | PR Caesya di-merge |
| 4 | Sulthan | `Sulthan` | pbi-13, pbi-14, pbi-15 | PR Gina di-merge |
| 5 | Imanuel | `Imanuel` | pbi-16, pbi-17, pbi-18, pbi-24 | PR Sulthan di-merge |
| 6 | Nicholas | `Nicholas` | pbi-19, pbi-20, pbi-21, pbi-23 | PR Imanuel di-merge |

Giliran 1–3 adalah Sprint 1, giliran 4–6 adalah Sprint 2. Imanuel menerapkan pbi-24 setelah pbi-18. Nicholas menerapkan pbi-23 paling akhir.

## 2. Commit message per patch

Salin persis sesuai tabel. Nomor BEAS mengikuti urutan tiket di Jira. Kalau nomor di Jira berbeda, sesuaikan nomor BEAS-nya saja.

| Anggota | File patch | Commit message |
|---|---|---|
| Aufa | `pbi-04.patch` | `BEAS-5 PBI-04 : Profil Mahasiswa` |
| Aufa | `pbi-05.patch` | `BEAS-6 PBI-05 : Riwayat Pendidikan/Nilai` |
| Aufa | `pbi-06.patch` | `BEAS-7 PBI-06 : Prestasi/Organisasi` |
| Caesya | `pbi-07.patch` | `BEAS-8 PBI-07 : Penyedia Beasiswa` |
| Caesya | `pbi-08.patch` | `BEAS-9 PBI-08 : Program Beasiswa` |
| Caesya | `pbi-09.patch` | `BEAS-10 PBI-09 : Persyaratan Beasiswa` |
| Gina | `pbi-10.patch` | `BEAS-11 PBI-10 : Pendaftaran Beasiswa` |
| Gina | `pbi-11.patch` | `BEAS-12 PBI-11 : Dokumen Persyaratan` |
| Gina | `pbi-12.patch` | `BEAS-13 PBI-12 : Esai/Motivasi` |
| Sulthan | `pbi-13.patch` | `BEAS-14 PBI-13 : Tahap Seleksi` |
| Sulthan | `pbi-14.patch` | `BEAS-15 PBI-14 : Penilaian` |
| Sulthan | `pbi-15.patch` | `BEAS-16 PBI-15 : Jadwal Wawancara` |
| Imanuel | `pbi-16.patch` | `BEAS-17 PBI-16 : Data Penerima` |
| Imanuel | `pbi-17.patch` | `BEAS-18 PBI-17 : Pencairan Dana` |
| Imanuel | `pbi-18.patch` | `BEAS-19 PBI-18 : Laporan Perkembangan Studi` |
| Imanuel | `pbi-24.patch` | `BEAS-25 PBI-24 : Dashboard Admin dan Kesenjangan` |
| Nicholas | `pbi-19.patch` | `BEAS-20 PBI-19 : Pengumuman` |
| Nicholas | `pbi-20.patch` | `BEAS-21 PBI-20 : Artikel Tips/Panduan` |
| Nicholas | `pbi-21.patch` | `BEAS-22 PBI-21 : Testimoni Alumni` |
| Nicholas | `pbi-23.patch` | `BEAS-24 PBI-23 : Dashboard Mahasiswa` |

## 3. Persiapan (sekali saja)

1. Pastikan kamu sudah punya akses push ke repo `beasiswaku-project/beasiswaku`.
2. Atur identitas git agar sama dengan akun GitHub-mu. Kalau email-nya beda, commit tidak tercatat di profil GitHub-mu.

   ```bash
   git config --global user.name "Nama Kamu"
   git config --global user.email "email-akun-github@kamu.com"
   ```

3. Clone repo kalau belum punya:

   ```bash
   git clone https://github.com/beasiswaku-project/beasiswaku.git
   cd beasiswaku
   ```

4. Simpan file patch dari Galang **di luar folder project**, misalnya di `~/Downloads/patches/`. Kalau disimpan di dalam folder project, file patch-nya akan ikut ter-commit.

## 4. Langkah per anggota

### a. Buat branch dari `develop` terbaru

Lakukan sekali, tepat sebelum mulai PBI pertama:

```bash
git checkout develop
git pull origin develop
git checkout -b NamaKamu
```

Ganti `NamaKamu` dengan nama branch di tabel bagian 1, misalnya `Sulthan`.

### b. Terapkan satu patch, commit, lalu push

Ulangi langkah ini untuk setiap patch, sesuai urutan nomornya. Contoh untuk pbi-13:

```bash
git apply --check ~/Downloads/patches/pbi-13.patch   # tidak ada output berarti aman
git apply ~/Downloads/patches/pbi-13.patch
git add -A
git status                                            # pastikan tidak ada file .patch yang ikut
git commit -m "BEAS-14 PBI-13 : Tahap Seleksi"
git push -u origin NamaKamu
```

Untuk patch berikutnya cukup ulangi dari `git apply --check`. `git push` cukup tanpa `-u origin NamaKamu`. Setiap PBI boleh dikerjakan di hari yang berbeda.

### c. (Disarankan) Jalankan aplikasi dan test sebelum push

```bash
composer install
npm install
npm run build
php artisan migrate
php artisan test
```

Semua test harus lulus. Kalau ada yang gagal, jangan push dulu. Hubungi Galang.

### d. Buat Pull Request

Setelah semua patch milikmu di-push:

1. Buka repo di GitHub, lalu klik **Compare & pull request** untuk branch-mu.
2. Base: `develop`, compare: `NamaKamu`.
3. Kabari Galang di grup bahwa PR sudah siap direview.

Setelah PR di-merge, kabari anggota berikutnya di tabel bagian 1 agar dia bisa mulai.

## 5. Untuk Galang (reviewer)

- Merge PR **satu per satu** sesuai urutan giliran. Jangan merge PR anggota berikutnya sebelum PR sebelumnya masuk ke `develop`.
- Gunakan **Create a merge commit** (bukan *Squash and merge*), agar satu commit per PBI tetap terlihat.
- Branch lokal `wip/<Nama>` di laptop Galang hanya sumber patch. Jangan di-push. Hapus setelah semua PR di-merge:

  ```bash
  git branch -D wip/Aufa wip/Caesya wip/Gina wip/Sulthan wip/Imanuel wip/Nicholas
  ```

## 6. Kalau ada masalah

| Masalah | Penyebab umum | Solusi |
|---|---|---|
| `git apply` gagal: `patch does not apply` | Branch belum berisi kode anggota sebelumnya, atau urutan patch terlompat | Pastikan PR sebelumnya sudah di-merge, lalu `git checkout develop && git pull origin develop`, buat ulang branch dari awal. Jangan edit file patch secara manual. |
| `git apply` gagal: `already exists in working directory` | Patch yang sama diterapkan dua kali | Cek `git log`. Kalau commit PBI itu sudah ada, lanjut ke patch berikutnya. |
| File `.patch` ikut ter-commit | Patch disimpan di dalam folder project | `git rm --cached nama.patch`, lalu `git commit --amend --no-edit`. Pindahkan patch ke luar folder project. |
| Commit tidak muncul di profil GitHub | Email git berbeda dengan email akun GitHub | Atur ulang `git config --global user.email`, lalu hubungi Galang untuk memperbaiki commit yang sudah terlanjur. |
| Halaman error `Unable to locate file in Vite manifest` | Aset frontend belum dibangun | Jalankan `npm run build` atau `npm run dev`. |
| Error tabel tidak ada | Database belum dimigrasi | Jalankan `php artisan migrate`. |

Kalau masalahnya tidak ada di tabel ini, jangan ubah kode secara manual. Kirim pesan error-nya ke Galang.
