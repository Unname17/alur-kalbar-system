<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AdminWebController;
use App\Http\Controllers\Web\WebPortalController;
use App\Http\Controllers\Web\KinerjaWebController; 
use App\Http\Controllers\Web\KakController;
use App\Http\Controllers\Web\RkaController;
use App\Http\Controllers\Web\ProcurementWebController;
use App\Http\Controllers\Web\PengadaanController;
use App\Http\Controllers\Web\RkaVerifikasiController;

// --- API CONTROLLERS (Untuk Microservices Simulation) ---
use App\Http\Controllers\Api\Kinerja\PohonKinerjaController;
use App\Http\Controllers\Api\Perencanaan\KakApiController;

/*
|--------------------------------------------------------------------------
| Web & API Routes Simulation
|--------------------------------------------------------------------------
*/

// 1. AUTH & PUBLIC ROUTES (Database: sistem_admin)
Route::middleware('db.set:sistem_admin')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AdminWebController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AdminWebController::class, 'loginAction']);
    });

    Route::middleware('auth')->group(function () {
        Route::post('logout', [AdminWebController::class, 'logoutAction'])->name('logout');
        Route::get('/', [WebPortalController::class, 'index'])->name('dashboard');
        
        // MODUL ADMIN
        Route::prefix('admin')->middleware(['role:admin_utama,sekretariat'])->group(function () {
            Route::get('opd', [AdminWebController::class, 'showOpdList'])->name('admin.opd');
            Route::post('/opd/toggle-status/{id}', [AdminWebController::class, 'toggleStatusInput'])->name('admin.opd.toggle');
        });
    });
});

// 2. MODUL KINERJA (Database: modul_kinerja)
Route::middleware(['auth', 'db.set:modul_kinerja'])->prefix('kinerja')->group(function () {
    Route::get('pohon', [KinerjaWebController::class, 'showPohonKinerja'])->name('kinerja.pohon');
    Route::get('/edit-detail/{id}', [KinerjaWebController::class, 'editDetail'])->name('kinerja.edit.detail');
    
    // API Internal untuk diakses Modul KAK (Automasi)
    Route::get('/api/sub-detail/{id}', [KinerjaWebController::class, 'getApiDetail'])->name('api.kinerja.detail');

    Route::middleware(['role:admin_utama,sekretariat'])->group(function () {
        Route::post('/approve/{id}', [KinerjaWebController::class, 'approval'])->name('kinerja.approve');
    });
            Route::get('/akses', [KinerjaWebController::class, 'indexAkses'])->name('kinerja.akses.index');
        Route::post('/akses', [KinerjaWebController::class, 'storeAkses'])->name('kinerja.akses.store');
        Route::delete('/akses/{id}', [KinerjaWebController::class, 'deleteAkses'])->name('kinerja.akses.delete');

    Route::middleware(['check.input'])->group(function () {
        Route::post('store', [KinerjaWebController::class, 'store'])->name('kinerja.store');
        Route::post('update/{id}', [KinerjaWebController::class, 'update'])->name('kinerja.update');
    });
});

// 3. MODUL KAK (Database: modul_kak)
Route::middleware(['auth', 'db.set:modul_kak'])->prefix('kak')->group(function () {
    Route::get('/', [KakController::class, 'index'])->name('kak.index'); 
    Route::get('/create/{pohon_kinerja_id}', [KakController::class, 'create'])->name('kak.create');
    Route::post('/store', [KakController::class, 'store'])->name('kak.store');
    Route::get('/show/{id}', [KakController::class, 'show'])->name('kak.show');
    Route::get('/edit/{id}', [KakController::class, 'edit'])->name('kak.edit');
    Route::post('/update/{id}', [KakController::class, 'update'])->name('kak.update');
    Route::post('/verifikasi/{id}', [KakController::class, 'verifikasi'])->name('kak.verifikasi');
    Route::post('/timeline/{id}', [KakController::class, 'storeTimeline'])->name('kak.timeline.store');


    // API KAK untuk integrasi RKA
    Route::get('/api/list-valid', [KakApiController::class, 'getValidKak'])->name('api.kak.valid');
});

// 4. MODUL RKA (Database: modul_anggaran)
    Route::middleware(['auth', 'db.set:modul_anggaran'])->prefix('rka')->group(function () {
Route::get('/cetak/{kak_id}', [RkaController::class, 'cetak'])->name('rka.cetak');
        Route::get('/api/list-valid', [KakApiController::class, 'getValidKak'])->name('api.kak.valid');
    // Rute RKA untuk User OPD
    Route::get('/', [RkaController::class, 'pilihKak'])->name('rka.pilih_kak');
Route::post('/sync-all', [RkaController::class, 'syncAllFromKak'])->name('rka.sync_all');
        Route::get('/penyusunan/{kak_id}', [RkaController::class, 'index'])->name('rka.index');
        
        // Proses CRUD Item
        Route::post('/store/{kak_id}', [RkaController::class, 'store'])->name('rka.store');
        Route::delete('/destroy/{id}', [RkaController::class, 'destroy'])->name('rka.destroy');
    
        // Update Manual & Import (Saya rapikan path-nya agar tidak double /rka/rka/)
        Route::put('/update-manual/{id}', [RkaController::class, 'updateManual'])->name('rka.update_manual');
        Route::post('/{id}/import', [RkaController::class, 'importExcel'])->name('rka.import');
        Route::get('/template/download', [RkaController::class, 'downloadTemplate'])->name('rka.download_template');

        Route::post('/finalisasi/{kak_id}', [RkaController::class, 'finalisasi'])->name('rka.finalisasi');

    // --- RUTE VERIFIKASI UNTUK SEKRETARIAT (Tambahkan Ini) ---
    Route::prefix('verifikasi')->group(function () {
        Route::get('/monitoring', [RkaVerifikasiController::class, 'index'])->name('verifikasi.index');
       Route::post('/approve/{id}', [RkaVerifikasiController::class, 'approve'])->name('verifikasi.approve');
        Route::post('/reject/{id}', [RkaVerifikasiController::class, 'reject'])->name('verifikasi.reject');

        // Rute untuk aksi massal
        Route::post('/bulk-approve', [RkaVerifikasiController::class, 'bulkApprove'])->name('verifikasi.bulk_approve');
        Route::post('/bulk-reject', [RkaVerifikasiController::class, 'bulkReject'])->name('verifikasi.bulk_reject');

        // Rute verifikasi dokumen KAK final
        Route::get('/belanja', [RkaVerifikasiController::class, 'indexBelanja'])->name('verifikasi.belanja');
        Route::get('/detail/{id}', [RkaVerifikasiController::class, 'show'])->name('verifikasi.show');
        // --- PERBAIKAN DI SINI (Sesuaikan Nama Rute dengan View) ---
    Route::post('/setuju-kak/{id}', [RkaVerifikasiController::class, 'setujuKak'])->name('verifikasi.kak.acc');
    Route::post('/tolak-kak/{id}', [RkaVerifikasiController::class, 'tolakKak'])->name('verifikasi.kak.tolak');
    });
});

// 5. MODUL PENGADAAN (Database: modul_pengadaan)
Route::middleware(['auth', 'db.set:modul_pengadaan'])->prefix('pengadaan')->group(function () {
    // 1. Halaman Index (Daftar Antrean)
    Route::get('/', [PengadaanController::class, 'index'])->name('pengadaan.index');

    // 2. Fungsi Sync All (Tarik data dari RKA)
    Route::post('/sync', [PengadaanController::class, 'sync'])->name('pengadaan.sync');

    // 3. Halaman Detail (Checklist 9 Dokumen)
    Route::get('/detail/{id}', [PengadaanController::class, 'show'])->name('pengadaan.show');

    // 4. Update Metode (Pilihan: Katalog, PL, atau Tender)
Route::put('/pengadaan/{id}/update-metode', [PengadaanController::class, 'updateMetode'])->name('pengadaan.update_metode');

    // 5. Update/Upload Dokumen (Untuk proses upload PDF nanti)
    Route::post('/pengadaan/document/{id}/upload', [PengadaanController::class, 'uploadDocument'])->name('pengadaan.document.upload');
// Route untuk generate PDF otomatis
Route::get('/pengadaan/{id}/print/{doc}', [PengadaanController::class, 'printDocument'])->name('pengadaan.print');
});