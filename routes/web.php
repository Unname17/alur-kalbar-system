<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AdminWebController;
use App\Http\Controllers\Web\WebPortalController;
use App\Http\Controllers\Api\SekretariatController; 
use App\Http\Controllers\Web\KinerjaWebController; 
use App\Http\Controllers\Kak\KakController;

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
        // URL: /kinerja/pohon
        Route::get('pohon', [KinerjaWebController::class, 'showPohonKinerja'])->name('kinerja.pohon');
        
        // 2. CRUD APPROVAL (AJAX POST)
        // URL: /kinerja/store
        Route::post('store', [KinerjaWebController::class, 'store'])->name('kinerja.store');
        // URL: /kinerja/update/{id}
        Route::post('update/{id}', [KinerjaWebController::class, 'update'])->name('kinerja.update');
        // URL: /kinerja/approval/{id}
        Route::post('approval/{id}', [KinerjaWebController::class, 'approval'])->name('kinerja.approval');

        // 3. REALISASI
        Route::get('realisasi', [KinerjaWebController::class, 'showRealisasi'])->name('kinerja.realisasi');
 
// Halaman Tabel Verifikasi
    Route::get('/admin/verifikasi-opd', [VerifikasiController::class, 'index'])
        ->name('admin.verifikasi.index');

    // Aksi Tombol Setujui
    Route::post('/admin/verifikasi-opd/{id}/setujui', [VerifikasiController::class, 'setujuiOpd'])
        ->name('sekretariat.setujui_opd');
    
        Route::get('/kinerja/akses', [KinerjaWebController::class, 'indexAkses'])->name('kinerja.akses.index');
    Route::post('/kinerja/akses', [KinerjaWebController::class, 'storeAkses'])->name('kinerja.akses.store');
    Route::delete('/kinerja/akses/{id}', [KinerjaWebController::class, 'deleteAkses'])->name('kinerja.akses.delete');
    
    });
    

    // ... kode route sebelumnya ...

    Route::prefix('kak')->group(function () {
        // --- BARIS INI YANG HARUS DITAMBAHKAN ---
        Route::get('/', [KakController::class, 'index'])->name('kak.index'); 
        
        // Menampilkan form buat KAK berdasarkan ID Sub Kegiatan
        Route::get('/create/{pohon_kinerja_id}', [KakController::class, 'create'])->name('kak.create');
        
        // Menyimpan data KAK
        Route::post('/store', [KakController::class, 'store'])->name('kak.store');
        
        // Menampilkan detail KAK
        Route::get('/show/{id}', [KakController::class, 'show'])->name('kak.show');
    });

});