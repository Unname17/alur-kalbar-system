<?php

use Illuminate\Support\Facades\Route;

// ====================================================================
// IMPORT CONTROLLERS
// ====================================================================

// --- SERVICE ADMIN ---
use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\PenggunaController;
use App\Http\Controllers\Api\Admin\PerangkatDaerahController;
use App\Http\Controllers\Api\SekretariatController;

// --- SERVICE KINERJA ---
use App\Http\Controllers\Api\Kinerja\PohonKinerjaController;
use App\Http\Controllers\Api\Kinerja\IndikatorController;

// --- SERVICE PERENCANAAN ---
use App\Http\Controllers\Api\Perencanaan\KakApiController;
use App\Http\Controllers\Api\Perencanaan\RkaApiController;

/*
|--------------------------------------------------------------------------
| API Routes (Microservices Gateway Simulation)
|--------------------------------------------------------------------------
*/

Route::middleware('db.set:sistem_admin')->prefix('admin')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('verifikasi-opd/{id}', [SekretariatController::class, 'setujuiNode']);
    });
    Route::apiResource('opd', PerangkatDaerahController::class)->only(['index', 'show']);
    Route::apiResource('pengguna', PenggunaController::class);
});

// ====================================================================
// SERVICE 2: KINERJA (Database: modul_kinerja)
// ====================================================================
Route::middleware('db.set:modul_kinerja')->prefix('kinerja')->group(function () {
    Route::apiResource('pohon', PohonKinerjaController::class);
    Route::get('pohon/history/{id}', [PohonKinerjaController::class, 'getHistory']);
    Route::get('pohon/target-tahunan/{id}', [PohonKinerjaController::class, 'getTargets']);
    Route::apiResource('indikator', IndikatorController::class);
});

// ====================================================================
// SERVICE 3: PERENCANAAN (DIPISAH BERDASARKAN DATABASE MASING-MASING)
// ====================================================================

// --- SUB-SERVICE: KAK (Database: modul_kak) ---
Route::middleware('db.set:modul_kak')->prefix('perencanaan/kak')->group(function () {
    Route::get('list-valid', [KakApiController::class, 'getValidKak']);
    Route::get('{id}', [KakApiController::class, 'show']);
});

// --- SUB-SERVICE: RKA (Database: modul_anggaran) ---
Route::middleware('db.set:modul_anggaran')->prefix('perencanaan/rka')->group(function () {
    // Ambil daftar KAK yang statusnya sudah 'Disetujui' (Status 2)
    Route::get('approved-kak', [RkaApiController::class, 'getApprovedKak']);

    // Tarik data dari KAK ke RKA (Automasi Sinkronisasi)
    Route::post('sync-kak/{kak_id}', [RkaApiController::class, 'syncFromKak']);

    // Cek Pagu Anggaran (Digunakan Modul Pengadaan/Monitoring)
    Route::get('cek-pagu/{id}', [RkaApiController::class, 'checkBudgetAvailability']);

    // Ambil item belanja RKA
    Route::get('item-belanja/{id}', [RkaApiController::class, 'getRkaItems']);

    // Update status RKA dari modul lain (Misal: Pengadaan berhasil)
    Route::post('update-status/{id}', [RkaApiController::class, 'updateStatusFromExternal']);
});

// ====================================================================
// SERVICE 4: PENGADAAN (Database: modul_pengadaan)
// ====================================================================
Route::middleware('db.set:modul_pengadaan')->prefix('pengadaan')->group(function () {
    // Tambahkan rute pengadaan di sini jika sudah ada controllernya
});