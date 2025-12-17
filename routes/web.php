<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AdminWebController;
use App\Http\Controllers\Web\WebPortalController;
use App\Http\Controllers\Api\SekretariatController; 
use App\Http\Controllers\Web\KinerjaWebController; 
use App\Http\Controllers\Web\KakController;
use App\Http\Controllers\Web\KakTimelineController;

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

    // ROUTE MODUL ADMIN
    Route::prefix('admin')->group(function () {
        Route::get('opd', [AdminWebController::class, 'showOpdList'])->name('admin.opd');
        Route::get('pengguna', [AdminWebController::class, 'showPenggunaList'])->name('admin.pengguna');
        Route::get('opd/create', [AdminWebController::class, 'showOpdForm'])->name('admin.opd.create');
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
 
        // --- PERBAIKAN DI SINI ---
        // Saya komen dulu baris di bawah ini karena VerifikasiController TIDAK ADA
        // Jika nanti Anda membuatnya, silakan uncomment.
        
        // Route::get('/admin/verifikasi-opd', [VerifikasiController::class, 'index'])->name('admin.verifikasi.index');
        // Route::post('/admin/verifikasi-opd/{id}/setujui', [VerifikasiController::class, 'setujuiOpd'])->name('sekretariat.setujui_opd');
    
        // AKSES PENGGUNA
        Route::get('/akses', [KinerjaWebController::class, 'indexAkses'])->name('kinerja.akses.index');
        Route::post('/akses', [KinerjaWebController::class, 'storeAkses'])->name('kinerja.akses.store');
        Route::delete('/akses/{id}', [KinerjaWebController::class, 'deleteAkses'])->name('kinerja.akses.delete');
    });
    

    // ROUTE MODUL KAK
    Route::prefix('kak')->group(function () {
        
        Route::get('/', [KakController::class, 'index'])->name('kak.index'); 
        
        // Menampilkan form buat KAK
        Route::get('/create/{pohon_kinerja_id}', [KakController::class, 'create'])->name('kak.create');
        
        // Menyimpan data KAK
        Route::post('/store', [KakController::class, 'store'])->name('kak.store');
        
        // Menampilkan detail KAK
        Route::get('/show/{id}', [KakController::class, 'show'])->name('kak.show');

        // Edit & Update
        Route::get('/edit/{id}', [KakController::class, 'edit'])->name('kak.edit');
        Route::post('/update/{id}', [KakController::class, 'update'])->name('kak.update');
    
        // --- PERBAIKAN URL VERIFIKASI & CETAK ---
        // Hapus '/kak' di depan karena sudah ada prefix
        // URL Hasil: domain.com/kak/verifikasi/{id}
        Route::post('/verifikasi/{id}', [KakController::class, 'verifikasi'])->name('kak.verifikasi');
        Route::get('/cetak/{id}', [KakController::class, 'cetakPdf'])->name('kak.cetak');
        Route::post('/timeline/store/{kak_id}', [KakTimelineController::class, 'store'])->name('kak.timeline.store');
    });

});