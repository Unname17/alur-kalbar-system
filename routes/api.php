<?php

use Illuminate\Support\Facades\Route;

// KOREKSI: Impor Controller dari Namespace API yang benar
use App\Http\Controllers\Api\Admin\PenggunaController;
use App\Http\Controllers\Api\Admin\PerangkatDaerahController;
use App\Http\Controllers\Api\Kinerja\PohonKinerjaController;
use App\Http\Controllers\Api\Kinerja\IndikatorController;
use App\Http\Controllers\Api\Admin\AuthController;

// --- [BARU] Import Controller Sekretariat
use App\Http\Controllers\Api\SekretariatController; 

// ====================================================================
// A. ROUTE MODUL ADMIN (API) - Database: sistem_admin
// ====================================================================

Route::middleware('db.set:sistem_admin')->prefix('admin')->group(function () {
    
    // Auth (Login tidak butuh token)
    Route::post('login', [AuthController::class, 'login']); 

    // --- [BARU] Group yang butuh Login (Token) ---
    Route::middleware('auth:sanctum')->group(function () {
        
        // Fitur Verifikasi Sekretariat (Setujui OPD)
        // Endpoint: POST /api/admin/verifikasi-opd/{id}
        Route::post('verifikasi-opd/{id}', [SekretariatController::class, 'setujuiNode']);
        
        // CRUD Pengguna & OPD (Jika ingin diamankan, masukkan ke sini juga)
        // Route::apiResource('opd', PerangkatDaerahController::class);
        // Route::apiResource('pengguna', PenggunaController::class);
    });

    // Jika CRUD ini masih bisa diakses publik (tanpa login), biarkan diluar group auth:
    Route::apiResource('opd', PerangkatDaerahController::class);
    Route::apiResource('pengguna', PenggunaController::class);
    
});

// ====================================================================
// B. ROUTE MODUL KINERJA (API)
// ====================================================================

Route::middleware('db.set:modul_kinerja')->prefix('kinerja')->group(function () {
    
    // Pohon Kinerja (Hirarki)
    Route::apiResource('pohon', PohonKinerjaController::class);
    
    // Indikator
    Route::apiResource('indikator', IndikatorController::class);
});