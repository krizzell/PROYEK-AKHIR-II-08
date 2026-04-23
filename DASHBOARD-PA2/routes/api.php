<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\PerkembanganApiController;
use App\Http\Controllers\Api\PengumumanApiController;
use App\Http\Controllers\Api\TagihanApiController;
use App\Http\Controllers\Api\PembayaranApiController;

// Public Auth Route (no middleware required)
Route::post('/login', [AuthApiController::class, 'login']);

Route::middleware('api')->group(function () {
    // Perkembangan APIs
    Route::get('/perkembangan', [PerkembanganApiController::class, 'index']);
    Route::get('/perkembangan/{id}', [PerkembanganApiController::class, 'show']);
    
    // Pengumuman APIs
    Route::get('/pengumuman', [PengumumanApiController::class, 'index']);
    Route::get('/pengumuman/{id}', [PengumumanApiController::class, 'show']);
    
    // Tagihan APIs
    Route::get('/tagihan', [TagihanApiController::class, 'index']);
    Route::get('/tagihan/{id}', [TagihanApiController::class, 'show']);
    
    // Pembayaran APIs
    Route::post('/create-payment', [PembayaranApiController::class, 'create']);
    Route::post('/pembayaran/create', [PembayaranApiController::class, 'create']);
    Route::get('/pembayaran/{transaction_id}/status', [PembayaranApiController::class, 'status']);
    
    // Midtrans callback endpoints
    Route::post('/payment-notification', [PembayaranApiController::class, 'webhook']);
    Route::post('/webhook/midtrans', [PembayaranApiController::class, 'webhook']);
});
