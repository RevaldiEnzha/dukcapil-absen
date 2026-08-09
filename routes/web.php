<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// --- CONTROLLER ADMIN ---
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PegawaiController; 
use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Admin\IzinController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\HariLiburController;

// --- CONTROLLER PEGAWAI (USER) ---
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\AbsenController as UserAbsenController;
use App\Http\Controllers\User\IzinController as UserIzinController;
use App\Http\Controllers\User\ProfilController as UserProfilController;

// RUTE AUTENTIKASI UTAMA
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// RUTE YANG DILINDUNGI (HARUS LOGIN)
Route::middleware('auth')->group(function () {
    // 1. ADMIN
    Route::prefix('admin')->name('admin.')->group(function () {
        
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Manajemen Pegawai
        Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');
        Route::post('/pegawai', [PegawaiController::class, 'store'])->name('pegawai.store');
        Route::get('/pegawai/{id}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');
        Route::put('/pegawai/{id}', [PegawaiController::class, 'update'])->name('pegawai.update');
        Route::delete('/pegawai/{id}', [PegawaiController::class, 'destroy'])->name('pegawai.destroy');
        
        // Manajemen Absensi
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
        Route::get('/absensi/export-pdf', [AbsensiController::class, 'exportPdf'])->name('absensi.export-pdf');
        
        // Manajemen Izin
        Route::get('/izin', [IzinController::class, 'index'])->name('izin.index');
        Route::put('/izin/{id}/status', [IzinController::class, 'updateStatus'])->name('izin.update-status');
        
        // Manajemen Laporan
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
        
        // Manajemen Hari Libur
        Route::get('/hari-libur', [HariLiburController::class, 'index'])->name('hari-libur.index');
        Route::post('/hari-libur', [HariLiburController::class, 'store'])->name('hari-libur.store');
        Route::delete('/hari-libur/{id}', [HariLiburController::class, 'destroy'])->name('hari-libur.destroy');
    });

    // 2. PEGAWAI (USER)
    Route::prefix('user')->name('pegawai.')->group(function () {
        
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
        
        // Absensi (Scanner)
        Route::get('/absen', [UserAbsenController::class, 'index'])->name('absen');
        Route::post('/absen/scan', [UserAbsenController::class, 'processScan'])->name('absen.scan'); 
        
        // Pengajuan Izin
        Route::get('/izin', [UserIzinController::class, 'index'])->name('izin');
        Route::post('/izin', [UserIzinController::class, 'store'])->name('izin.store');
        Route::delete('/izin/{id}', [UserIzinController::class, 'destroy'])->name('izin.destroy');
        
        // Profil & Riwayat
        Route::get('/profil', [UserProfilController::class, 'index'])->name('profil');
        Route::put('/profil/update', [UserProfilController::class, 'updateAkun'])->name('profil.update'); 
        Route::get('/riwayat-absensi', [UserProfilController::class, 'riwayat'])->name('riwayat');
    });

});