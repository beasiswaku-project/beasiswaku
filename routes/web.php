<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Mahasiswa;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware(['auth', 'active'])->group(function () {
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', Admin\DashboardController::class)->name('dashboard');
    });

    Route::middleware('role:mahasiswa')->group(function () {
        Route::get('/dashboard', Mahasiswa\DashboardController::class)->name('mahasiswa.dashboard');
    });
});

require __DIR__.'/auth.php';
