<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PegawaiController; // <--- INI BARIS YANG HILANG

// Rute Autentikasi
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute yang Dilindungi Auth
Route::middleware('auth')->group(function () {
    
    // -- RUTE ADMIN --
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Rute Manajemen Pegawai
    Route::get('/admin/pegawai', [PegawaiController::class, 'index'])->name('admin.pegawai.index');
    Route::post('/admin/pegawai', [PegawaiController::class, 'store'])->name('admin.pegawai.store');
    Route::get('/admin/pegawai/{id}/edit', [PegawaiController::class, 'edit'])->name('admin.pegawai.edit');
    Route::put('/admin/pegawai/{id}', [PegawaiController::class, 'update'])->name('admin.pegawai.update');
    Route::delete('/admin/pegawai/{id}', [PegawaiController::class, 'destroy'])->name('admin.pegawai.destroy');

    // -- RUTE PEGAWAI --
    Route::get('/pegawai/dashboard', function () {
        return 'Ini Halaman Dashboard Pegawai';
    })->name('pegawai.dashboard');

});