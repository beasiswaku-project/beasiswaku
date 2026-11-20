<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Mahasiswa;
use App\Http\Controllers\PrestasiBuktiController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware(['auth', 'active'])->group(function () {
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', Admin\DashboardController::class)->name('dashboard');

        Route::resource('kategori', Admin\KategoriController::class)->except(['create', 'edit']);
        Route::resource('jenjang', Admin\JenjangController::class)->except(['create', 'edit']);
        Route::resource('wilayah', Admin\WilayahController::class)->except(['create', 'edit']);

        Route::resource('profil', Admin\ProfilController::class)->only(['index', 'show']);
        Route::patch('/profil/{profil}/nonaktif', [Admin\ProfilController::class, 'nonaktif'])->name('profil.nonaktif');

        Route::resource('penyedia', Admin\PenyediaController::class)->except(['create', 'edit'])->parameters(['penyedia' => 'penyedia']);
        Route::resource('program', Admin\ProgramController::class)->except(['create', 'edit']);
        Route::patch('/program/{program}/status', [Admin\ProgramController::class, 'status'])->name('program.status');
    });

    Route::middleware('role:mahasiswa')->group(function () {
        Route::get('/dashboard', Mahasiswa\DashboardController::class)->name('mahasiswa.dashboard');

        Route::get('/beasiswa', [Mahasiswa\BeasiswaController::class, 'index'])->name('beasiswa.index');
        Route::get('/beasiswa/{program}', [Mahasiswa\BeasiswaController::class, 'show'])->name('beasiswa.show');

        Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
            Route::get('/profil', [Mahasiswa\ProfilController::class, 'show'])->name('profil.show');
            Route::post('/profil', [Mahasiswa\ProfilController::class, 'store'])->name('profil.store');
            Route::put('/profil', [Mahasiswa\ProfilController::class, 'update'])->name('profil.update');

            Route::resource('riwayat-pendidikan', Mahasiswa\RiwayatPendidikanController::class)
                ->only(['index', 'store', 'update', 'destroy'])
                ->parameters(['riwayat-pendidikan' => 'riwayatPendidikan']);

            Route::resource('prestasi', Mahasiswa\PrestasiController::class)->only(['index', 'store', 'update', 'destroy']);
        });
    });

    Route::get('/prestasi/{prestasi}/bukti', PrestasiBuktiController::class)->name('prestasi.bukti');
});

require __DIR__.'/auth.php';
