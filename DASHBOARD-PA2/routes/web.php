<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\PerkembanganController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\PembayaranController;

// Public Routes
Route::get('/', function () {
    if (session('akun_id')) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Protected Routes (hanya guru)
Route::middleware('check.guru')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Profile Routes
    Route::get('/profile/edit-password', [ProfileController::class, 'editPassword'])->name('profile.edit-password');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Routes untuk Guru (hanya super admin)
    Route::middleware('check.super.admin')->group(function () {
        Route::resource('guru', GuruController::class);
        Route::resource('akun', AkunController::class);
    });
    
    // Routes untuk Kelas - use explicit routes to avoid singularization issue
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::get('/kelas/create', [KelasController::class, 'create'])->name('kelas.create');
    Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
    Route::get('/kelas/{id_kelas}', [KelasController::class, 'show'])->name('kelas.show');
    Route::get('/kelas/{id_kelas}/edit', [KelasController::class, 'edit'])->name('kelas.edit');
    Route::put('/kelas/{id_kelas}', [KelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{id_kelas}', [KelasController::class, 'destroy'])->name('kelas.destroy');
    
    // Routes untuk Siswa - IMPORTANT: Static routes MUST come before dynamic parameter routes
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::get('/siswa/create', [SiswaController::class, 'create'])->name('siswa.create');
    Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');
    Route::get('/siswa/{nomor_induk_siswa}', [SiswaController::class, 'show'])->name('siswa.show')->where('nomor_induk_siswa', '[0-9]+');
    Route::get('/siswa/{nomor_induk_siswa}/edit', [SiswaController::class, 'edit'])->name('siswa.edit')->where('nomor_induk_siswa', '[0-9]+');
    Route::put('/siswa/{nomor_induk_siswa}', [SiswaController::class, 'update'])->name('siswa.update')->where('nomor_induk_siswa', '[0-9]+');
    Route::delete('/siswa/{nomor_induk_siswa}', [SiswaController::class, 'destroy'])->name('siswa.destroy')->where('nomor_induk_siswa', '[0-9]+');
    
    // Routes untuk Pengumuman
    Route::resource('pengumuman', PengumumanController::class);
    
    // Routes untuk Perkembangan
    Route::resource('perkembangan', PerkembanganController::class);
    
    // Routes untuk Tagihan
    Route::get('/tagihan/bulk-create', [TagihanController::class, 'bulkCreate'])->name('tagihan.bulkCreate');
    Route::post('/tagihan/bulk-store', [TagihanController::class, 'bulkCreateStore'])->name('tagihan.bulkCreateStore');
    Route::get('/tagihan/bulk-update-status', [TagihanController::class, 'bulkUpdateStatus'])->name('tagihan.bulkUpdateStatus');
    Route::post('/tagihan/bulk-update-status', [TagihanController::class, 'bulkUpdateStatusStore'])->name('tagihan.bulkUpdateStatusStore');
    Route::resource('tagihan', TagihanController::class);
    
    // Routes untuk Pembayaran
    Route::resource('pembayaran', PembayaranController::class);
});
