<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Mahasiswa;
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
    });

    Route::middleware('role:mahasiswa')->group(function () {
        Route::get('/dashboard', Mahasiswa\DashboardController::class)->name('mahasiswa.dashboard');

        Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
            Route::get('/profil', [Mahasiswa\ProfilController::class, 'show'])->name('profil.show');
            Route::post('/profil', [Mahasiswa\ProfilController::class, 'store'])->name('profil.store');
            Route::put('/profil', [Mahasiswa\ProfilController::class, 'update'])->name('profil.update');
        });
    });
});

require __DIR__.'/auth.php';
