<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AdminWebController;
use App\Http\Controllers\Web\WebPortalController;
use App\Http\Controllers\Api\SekretariatController; 
use App\Http\Controllers\Web\KinerjaWebController; 
use App\Http\Controllers\Web\KakController;
use App\Http\Controllers\Web\KakTimelineController;
use App\Http\Controllers\Web\RkaController;
// --- TAMBAHAN BARU ---
use App\Http\Controllers\Web\RkaVerifikasiController; 

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 1. AUTHENTIKASI (Guest Middleware) ---
Route::middleware('guest')->group(function () {
    Route::get('login', [AdminWebController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AdminWebController::class, 'loginAction']);
});

// --- 2. SETELAH LOGIN (Auth Middleware) ---
Route::middleware('auth')->group(function () {
    
    // Halaman Portal Pemilih Modul
    Route::get('/', [WebPortalController::class, 'index'])->name('dashboard');
    
    Route::post('logout', [AdminWebController::class, 'logoutAction'])->name('logout');

    // ROUTE MODUL ADMIN (Umum)
    // ROUTE MODUL ADMIN (Umum)
    Route::prefix('admin')->group(function () {
        Route::get('opd', [AdminWebController::class, 'showOpdList'])->name('admin.opd');
        Route::get('pengguna', [AdminWebController::class, 'showPenggunaList'])->name('admin.pengguna');
        Route::get('opd/create', [AdminWebController::class, 'showOpdForm'])->name('admin.opd.create');

        // --- VERIFIKASI RKA (SEKRETARIAT) ---
        
        // 1. Halaman Utama
        Route::get('/verifikasi-rka', [RkaVerifikasiController::class, 'index'])->name('verifikasi.index');
        Route::get('/verifikasi-belanja', [RkaVerifikasiController::class, 'indexBelanja'])->name('verifikasi.belanja');

        // 2. Action Satuan (Per Item)
        Route::post('/verifikasi-rka/approve/{id}', [RkaVerifikasiController::class, 'approve'])->name('verifikasi.approve');
        Route::post('/verifikasi-rka/reject/{id}', [RkaVerifikasiController::class, 'reject'])->name('verifikasi.reject');
        
        // 3. Action Massal (Bulk - Checkbox) --> TAMBAHKAN INI!
        Route::post('/verifikasi-rka/bulk-approve', [RkaVerifikasiController::class, 'bulkApprove'])->name('verifikasi.bulk_approve');
        Route::post('/verifikasi-rka/bulk-reject', [RkaVerifikasiController::class, 'bulkReject'])->name('verifikasi.bulk_reject');


        // --- TAMBAHAN WAJIB (UNTUK HALAMAN DETAIL & ACC DOKUMEN) ---
        
        // 1. Halaman Detail (Saat klik tombol 'Periksa')
        Route::get('/verifikasi-belanja/{id}', [RkaVerifikasiController::class, 'show'])->name('verifikasi.show');

        // 2. Action ACC Dokumen KAK (Tombol Hijau 'Sah-kan')
        Route::post('/verifikasi-belanja/{id}/acc', [RkaVerifikasiController::class, 'setujuKak'])->name('verifikasi.kak.acc');

        // 3. Action Tolak Dokumen KAK (Tombol Merah 'Tolak/Revisi')
        Route::post('/verifikasi-belanja/{id}/tolak', [RkaVerifikasiController::class, 'tolakKak'])->name('verifikasi.kak.tolak');
    });

    // ROUTE MODUL KINERJA
    Route::prefix('kinerja')->group(function () {
        // 1. TAMPILAN POHON KINERJA (GET)
        Route::get('pohon', [KinerjaWebController::class, 'showPohonKinerja'])->name('kinerja.pohon');
        
        // 2. CRUD APPROVAL (AJAX POST)
        Route::post('store', [KinerjaWebController::class, 'store'])->name('kinerja.store');
        Route::post('update/{id}', [KinerjaWebController::class, 'update'])->name('kinerja.update');
        Route::post('approval/{id}', [KinerjaWebController::class, 'approval'])->name('kinerja.approval');

        // 3. REALISASI
        Route::get('realisasi', [KinerjaWebController::class, 'showRealisasi'])->name('kinerja.realisasi');
    
        // AKSES PENGGUNA
        Route::get('/akses', [KinerjaWebController::class, 'indexAkses'])->name('kinerja.akses.index');
        Route::post('/akses', [KinerjaWebController::class, 'storeAkses'])->name('kinerja.akses.store');
        Route::delete('/akses/{id}', [KinerjaWebController::class, 'deleteAkses'])->name('kinerja.akses.delete');



    });
    

    // ROUTE MODUL KAK
    Route::prefix('kak')->group(function () {
        Route::get('/', [KakController::class, 'index'])->name('kak.index'); 
        Route::get('/create/{pohon_kinerja_id}', [KakController::class, 'create'])->name('kak.create');
        Route::post('/store', [KakController::class, 'store'])->name('kak.store');
        Route::get('/show/{id}', [KakController::class, 'show'])->name('kak.show');
        Route::get('/edit/{id}', [KakController::class, 'edit'])->name('kak.edit');
        Route::post('/update/{id}', [KakController::class, 'update'])->name('kak.update');
        Route::post('/verifikasi/{id}', [KakController::class, 'verifikasi'])->name('kak.verifikasi');
        Route::get('/cetak/{id}', [KakController::class, 'cetakPdf'])->name('kak.cetak');
        Route::post('/timeline/store/{kak_id}', [KakTimelineController::class, 'store'])->name('kak.timeline.store');
    });

    // ROUTE MODUL RKA (USER / PENYUSUN)
    Route::prefix('rka')->group(function () {
        // Menu Utama
        Route::get('/', [RkaController::class, 'pilihKak'])->name('rka.pilih_kak');
        
        // Halaman Belanja
        Route::get('/penyusunan/{kak_id}', [RkaController::class, 'index'])->name('rka.index');
        
        // Proses CRUD Item
        Route::post('/store/{kak_id}', [RkaController::class, 'store'])->name('rka.store');
        Route::delete('/destroy/{id}', [RkaController::class, 'destroy'])->name('rka.destroy');
    
        // Update Manual & Import (Saya rapikan path-nya agar tidak double /rka/rka/)
        Route::put('/update-manual/{id}', [RkaController::class, 'updateManual'])->name('rka.update_manual');
        Route::post('/{id}/import', [RkaController::class, 'importExcel'])->name('rka.import');
        Route::get('/template/download', [RkaController::class, 'downloadTemplate'])->name('rka.download_template');

        Route::post('/finalisasi/{kak_id}', [RkaController::class, 'finalisasi'])->name('rka.finalisasi');
    });

});