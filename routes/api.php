<?php

use Illuminate\Support\Facades\Route;

// --- 1. CONTROLLER SERVICE ADMIN ---
use App\Http\Controllers\Api\Admin\PenggunaController;
use App\Http\Controllers\Api\Admin\PerangkatDaerahController;
use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\SekretariatController; 

// --- 2. CONTROLLER SERVICE KINERJA ---
use App\Http\Controllers\Api\Kinerja\PohonKinerjaController;
use App\Http\Controllers\Api\Kinerja\IndikatorController;

// --- 3. CONTROLLER SERVICE PERENCANAAN (BARU) ---
// Pastikan kamu nanti buat Controller ini di folder App\Http\Controllers\Api\Perencanaan
use App\Http\Controllers\Api\Perencanaan\KakApiController;
use App\Http\Controllers\Api\Perencanaan\RkaApiController;

/*
|--------------------------------------------------------------------------
| API Routes (Microservices Gateway Simulation)
|--------------------------------------------------------------------------
|
| Setiap group route merepresentasikan satu "Service" yang independen.
| Komunikasi antar modul (misal Pengadaan butuh data RKA) WAJIB
| memanggil endpoint di sini, bukan query database langsung.
|
*/

// ====================================================================
// SERVICE 1: ADMIN & AUTHORIZATION (Database: sistem_admin)
// ====================================================================
Route::middleware('db.set:sistem_admin')->prefix('admin')->group(function () {
    
    // Public Auth
    Route::post('login', [AuthController::class, 'login']); 

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        // Verifikasi OPD oleh Sekretariat
        Route::post('verifikasi-opd/{id}', [SekretariatController::class, 'setujuiNode']);
        
        // Master Data (Jika ingin diproteksi)
        // Route::apiResource('opd', PerangkatDaerahController::class);
    });

    // Public Master Data (Read Only for other services)
    Route::apiResource('opd', PerangkatDaerahController::class)->only(['index', 'show']);
    Route::apiResource('pengguna', PenggunaController::class);
});

// ====================================================================
// SERVICE 2: KINERJA (Database: modul_kinerja)
// ====================================================================
Route::middleware('db.set:modul_kinerja')->prefix('kinerja')->group(function () {
    
    // Pohon Kinerja (Digunakan oleh Modul KAK untuk referensi)
    Route::apiResource('pohon', PohonKinerjaController::class);
    
    // Indikator Kinerja
    Route::apiResource('indikator', IndikatorController::class);
});

// ====================================================================
// SERVICE 3: PERENCANAAN (KAK & RKA) (Database: modul_perencanaan)
// ====================================================================
// Kita asumsikan KAK dan RKA ada di satu database 'modul_perencanaan'
Route::middleware('db.set:modul_perencanaan')->prefix('perencanaan')->group(function () {

    // --- ENDPOINT UNTUK MODUL KAK ---
    Route::prefix('kak')->group(function () {
        // Mendapatkan list KAK yang sudah valid (untuk referensi modul lain)
        Route::get('/list-valid', [KakApiController::class, 'getValidKak']);
        
        // Detail KAK specific
        Route::get('/{id}', [KakApiController::class, 'show']);
    });

    // --- ENDPOINT UNTUK MODUL RKA ---
    // Ini yang krusial untuk Modul Pengadaan nanti
    Route::prefix('rka')->group(function () {
        
        // Cek Pagu Anggaran (Dipanggil oleh Modul Pengadaan sebelum bikin SPK)
        // Return: { "status": "available", "sisa_pagu": 50000000 }
        Route::get('/cek-pagu/{id}', [RkaApiController::class, 'checkBudgetAvailability']);
        
        // Ambil Data Item Belanja RKA untuk di-import ke Pengadaan
        Route::get('/item-belanja/{id}', [RkaApiController::class, 'getRkaItems']);
        
        // Update Status RKA (Misal: "Sedang Dalam Pengadaan")
        Route::post('/update-status/{id}', [RkaApiController::class, 'updateStatusFromExternal']);
    });
});

// ====================================================================
// SERVICE 4: PENGADAAN (Database: modul_pengadaan) -> NEXT STEP
// ====================================================================
// Nanti route pengadaan akan masuk sini
// Route::middleware('db.set:modul_pengadaan')->prefix('pengadaan')...