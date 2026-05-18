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

    // Akses guru dan superadmin untuk melihat data siswa, kelas, dan tagihan
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::get('/siswa/{nomor_induk_siswa}', [SiswaController::class, 'show'])->name('siswa.show')->where('nomor_induk_siswa', '[0-9]+');

    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::get('/kelas/{id_kelas}', [KelasController::class, 'show'])->name('kelas.show')->whereNumber('id_kelas');

    Route::get('/tagihan', [TagihanController::class, 'index'])->name('tagihan.index');
    Route::get('/tagihan/{tagihan}', [TagihanController::class, 'show'])->name('tagihan.show')->whereNumber('tagihan');

    // Routes khusus SuperAdmin untuk mengelola data guru, akun, siswa, kelas, dan tagihan
    Route::middleware('check.super.admin')->group(function () {
        Route::resource('guru', GuruController::class);
        Route::post('/guru/bulk-destroy', [GuruController::class, 'bulkDestroy'])->name('guru.bulkDestroy');
        
        Route::resource('akun', AkunController::class);
        Route::post('/akun/bulk-destroy', [AkunController::class, 'bulkDestroy'])->name('akun.bulkDestroy');
        
        // Bulk generate student accounts
        Route::get('/akun/bulk-generate-siswa/form', [AkunController::class, 'bulkGenerateSiswaForm'])->name('akun.bulkGenerateSiswaForm');
        Route::post('/akun/bulk-generate-siswa', [AkunController::class, 'bulkGenerateSiswaStore'])->name('akun.bulkGenerateSiswaStore');

        // Kelas management
        Route::get('/kelas/create', [KelasController::class, 'create'])->name('kelas.create');
        Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
        Route::post('/kelas/bulk-destroy', [KelasController::class, 'bulkDestroy'])->name('kelas.bulkDestroy');
        Route::get('/kelas/{id_kelas}/edit', [KelasController::class, 'edit'])->name('kelas.edit');
        Route::put('/kelas/{id_kelas}', [KelasController::class, 'update'])->name('kelas.update');
        Route::delete('/kelas/{id_kelas}', [KelasController::class, 'destroy'])->name('kelas.destroy');

        // Siswa management
        Route::get('/siswa/import', [SiswaController::class, 'importForm'])->name('siswa.importForm');
        Route::post('/siswa/import-store', [SiswaController::class, 'importStore'])->name('siswa.importStore');
        Route::get('/siswa/create', [SiswaController::class, 'create'])->name('siswa.create');
        Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');
        Route::post('/siswa/bulk-destroy', [SiswaController::class, 'bulkDestroy'])->name('siswa.bulkDestroy');
        Route::get('/siswa/{nomor_induk_siswa}/edit', [SiswaController::class, 'edit'])->name('siswa.edit')->where('nomor_induk_siswa', '[0-9]+');
        Route::put('/siswa/{nomor_induk_siswa}', [SiswaController::class, 'update'])->name('siswa.update')->where('nomor_induk_siswa', '[0-9]+');
        Route::delete('/siswa/{nomor_induk_siswa}', [SiswaController::class, 'destroy'])->name('siswa.destroy')->where('nomor_induk_siswa', '[0-9]+');

        // Tagihan management
        Route::get('/tagihan/bulk-create', [TagihanController::class, 'bulkCreate'])->name('tagihan.bulkCreate');
        Route::post('/tagihan/bulk-store', [TagihanController::class, 'bulkCreateStore'])->name('tagihan.bulkCreateStore');
        Route::get('/tagihan/create', [TagihanController::class, 'create'])->name('tagihan.create');
        Route::post('/tagihan', [TagihanController::class, 'store'])->name('tagihan.store');
        Route::get('/tagihan/bulk-update-status', [TagihanController::class, 'bulkUpdateStatus'])->name('tagihan.bulkUpdateStatus');
        Route::post('/tagihan/bulk-update-status', [TagihanController::class, 'bulkUpdateStatusStore'])->name('tagihan.bulkUpdateStatusStore');
        Route::get('/tagihan/{tagihan}/edit', [TagihanController::class, 'edit'])->name('tagihan.edit');
        Route::put('/tagihan/{tagihan}', [TagihanController::class, 'update'])->name('tagihan.update');
        Route::delete('/tagihan/{tagihan}', [TagihanController::class, 'destroy'])->name('tagihan.destroy');
    });
    
    // Routes untuk Pengumuman - SuperAdmin dan guru bisa manage
    Route::resource('pengumuman', PengumumanController::class);
    Route::post('/pengumuman/bulk-destroy', [PengumumanController::class, 'bulkDestroy'])->name('pengumuman.bulkDestroy');
    
    // Routes untuk Perkembangan - read-only untuk SuperAdmin, CRUD untuk guru
    Route::resource('perkembangan', PerkembanganController::class);
    Route::post('/perkembangan/bulk-destroy', [PerkembanganController::class, 'bulkDestroy'])->name('perkembangan.bulkDestroy');
    
    // Routes untuk Pembayaran
    Route::resource('pembayaran', PembayaranController::class);
});
