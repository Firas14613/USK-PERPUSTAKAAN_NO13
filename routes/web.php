<?php

use App\Http\Controllers\Siswa\KoleksiBukuController;
use App\Http\Controllers\Siswa\PeminjamanSiswaController;
use App\Http\Controllers\Siswa\ProfilSiswaController;
use App\Http\Controllers\Siswa\RiwayatPinjamController;
use App\Http\Controllers\Siswa\SiswaDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'siswa'])->group(function () {
    Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('siswa.dashboard');
    Route::get('/koleksi-buku', [KoleksiBukuController::class, 'index'])->name('siswa.koleksi');
    Route::post('/pinjam', [PeminjamanSiswaController::class, 'store'])->name('siswa.pinjam');
    Route::get('/riwayat-pinjam', [RiwayatPinjamController::class, 'index'])->name('siswa.riwayat');
    Route::get('/profil', [ProfilSiswaController::class, 'edit'])->name('siswa.profil.edit');
    Route::post('/profil', [ProfilSiswaController::class, 'update'])->name('siswa.profil.update');
});

require __DIR__.'/auth.php';
