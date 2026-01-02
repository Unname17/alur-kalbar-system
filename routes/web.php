<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\{
    AdminWebController,
    WebPortalController,
    KinerjaWebController,
    KakController,
    RkaController,
    ProcurementWebController,
    PengadaanController,
    RkaVerifikasiController,
    LogAktivitasController,
    KinerjaWizardController,
    KinerjaApprovalController,
    AdminKinerjaController // Tambahkan Controller Admin Kinerja jika Anda memisahkan logic admin
};
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

        // MODUL ADMIN UTAMA
        Route::prefix('admin')->middleware(['role:admin_utama,sekretariat'])->group(function () {
            Route::get('opd', [AdminWebController::class, 'showOpdList'])->name('admin.opd');
            Route::post('opd/toggle-status/{id}', [AdminWebController::class, 'toggleStatusInput'])->name('admin.opd.toggle');
        });
    });
});

// 2. MODUL KINERJA (Database: modul_kinerja)
Route::middleware(['auth', 'db.set:modul_kinerja'])->prefix('kinerja')->name('kinerja.')->group(function () {
    
    // DASHBOARD & MONITORING
    Route::get('/dashboard', [KinerjaWebController::class, 'index'])->name('index');
    Route::get('/monitoring', [KinerjaWizardController::class, 'monitoring'])->name('monitoring');
    Route::get('/pohon', [KinerjaWebController::class, 'showPohonKinerja'])->name('pohon');

    // GRUP WIZARD CASCADING (Diproteksi Middleware Kunci Akses)
    Route::middleware(['check.input'])->prefix('wizard')->name('wizard.')->group(function () {
        Route::get('/', [KinerjaWizardController::class, 'index'])->name('index');
        Route::post('/store', [KinerjaWizardController::class, 'storeStep'])->name('store');
        
        // API Fetch Wizard
        Route::get('/fetch-parents/{level}', [KinerjaWizardController::class, 'fetchParents']);
        Route::get('/fetch-existing/{level}/{parentId}', [KinerjaWizardController::class, 'fetchExisting']);
        Route::get('/fetch-detail/{level}/{id}', [KinerjaWizardController::class, 'fetchDetail']);
        Route::get('/fetch-rejected', [KinerjaWizardController::class, 'fetchAllRejected'])->name('rejected');
    });

    // RUTE INBOX APPROVAL
    Route::prefix('inbox')->name('inbox.')->group(function () {
        Route::get('/', [KinerjaApprovalController::class, 'index'])->name('index');
        Route::get('/count', [KinerjaApprovalController::class, 'count'])->name('count');
        Route::post('/approve/{level}/{id}', [KinerjaApprovalController::class, 'approve'])->name('approve');
        Route::post('/reject/{level}/{id}', [KinerjaApprovalController::class, 'reject'])->name('reject');
    });

    // --- MANAJEMEN AKSES (Hanya Bappeda) ---
    // PERBAIKAN DI SINI: Menambahkan rute store dan destroy
    Route::middleware(['role:bappeda'])->prefix('admin')->name('admin.')->group(function () {
        
        Route::prefix('access')->name('access.')->group(function () {
            // Halaman Utama Manajemen Akses
            Route::get('/', [KinerjaWizardController::class, 'manageAccess'])->name('index'); // kinerja.admin.access.index
            
            // Simpan Aturan Baru (Multi-select)
            Route::post('/store', [KinerjaWizardController::class, 'storeAccess'])->name('store'); // kinerja.admin.access.store
            
            // Hapus Aturan
            Route::delete('/{id}', [KinerjaWizardController::class, 'destroyAccess'])->name('destroy'); // kinerja.admin.access.destroy
        });

        // Rute Helper AJAX (untuk Dropdown Pegawai & Goals)
        Route::get('/fetch-pegawai/{pd_id}', [KinerjaWizardController::class, 'fetchPegawaiByOpd'])->name('fetch-pegawai');
        Route::get('/fetch-goals/{pd_id}', [KinerjaWizardController::class, 'fetchGoalsByOpd'])->name('fetch-goals');
    });

    // LOG & SYNC
    Route::middleware(['role:bappeda,admin_utama'])->group(function () {
        Route::post('/sync-visi-misi', [KinerjaWebController::class, 'syncVisiMisi'])->name('sync');
        Route::prefix('log')->name('log.')->group(function () {
            Route::get('/', [LogAktivitasController::class, 'index'])->name('index');
            Route::get('/export', [LogAktivitasController::class, 'exportExcel'])->name('export');
            Route::post('/archive', [LogAktivitasController::class, 'archiveAndCleanup'])->name('archive');
            Route::get('/{id}', [LogAktivitasController::class, 'show'])->name('show');
            Route::delete('/{id}', [LogAktivitasController::class, 'destroy'])->name('destroy');
        });
    });
    Route::middleware(['role:bappeda,admin_utama'])->prefix('log')->name('log.')->group(function () {
    Route::get('/', [LogAktivitasController::class, 'index'])->name('index');
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
    Route::get('/api/list-valid', [KakApiController::class, 'getValidKak'])->name('api.kak.valid');
});

// 4. MODUL RKA (Database: modul_anggaran)
Route::middleware(['auth', 'db.set:modul_anggaran'])->prefix('rka')->group(function () {
    Route::get('/', [RkaController::class, 'pilihKak'])->name('rka.pilih_kak');
    Route::get('/penyusunan/{kak_id}', [RkaController::class, 'index'])->name('rka.index');
    Route::get('/cetak/{kak_id}', [RkaController::class, 'cetak'])->name('rka.cetak');
    Route::post('/sync-all', [RkaController::class, 'syncAllFromKak'])->name('rka.sync_all');
    Route::post('/store/{kak_id}', [RkaController::class, 'store'])->name('rka.store');
    Route::delete('/destroy/{id}', [RkaController::class, 'destroy'])->name('rka.destroy');
    Route::put('/update-manual/{id}', [RkaController::class, 'updateManual'])->name('rka.update_manual');
    Route::post('/finalisasi/{kak_id}', [RkaController::class, 'finalisasi'])->name('rka.finalisasi');
    Route::post('/{id}/import', [RkaController::class, 'importExcel'])->name('rka.import');
    Route::get('/template/download', [RkaController::class, 'downloadTemplate'])->name('rka.download_template');

    // Sub-Modul Verifikasi
    Route::prefix('verifikasi')->group(function () {
        Route::get('/monitoring', [RkaVerifikasiController::class, 'index'])->name('verifikasi.index');
        Route::get('/belanja', [RkaVerifikasiController::class, 'indexBelanja'])->name('verifikasi.belanja');
        Route::get('/detail/{id}', [RkaVerifikasiController::class, 'show'])->name('verifikasi.show');
        Route::post('/approve/{id}', [RkaVerifikasiController::class, 'approve'])->name('verifikasi.approve');
        Route::post('/reject/{id}', [RkaVerifikasiController::class, 'reject'])->name('verifikasi.reject');
        Route::post('/bulk-approve', [RkaVerifikasiController::class, 'bulkApprove'])->name('verifikasi.bulk_approve');
        Route::post('/bulk-reject', [RkaVerifikasiController::class, 'bulkReject'])->name('verifikasi.bulk_reject');
        Route::post('/setuju-kak/{id}', [RkaVerifikasiController::class, 'setujuKak'])->name('verifikasi.kak.acc');
        Route::post('/tolak-kak/{id}', [RkaVerifikasiController::class, 'tolakKak'])->name('verifikasi.kak.tolak');
    });
});

// 5. MODUL PENGADAAN (Database: modul_pengadaan)
Route::middleware(['auth', 'db.set:modul_pengadaan'])->prefix('pengadaan')->group(function () {
    Route::get('/', [PengadaanController::class, 'index'])->name('pengadaan.index');
    Route::get('/detail/{id}', [PengadaanController::class, 'show'])->name('pengadaan.show');
    Route::post('/sync', [PengadaanController::class, 'sync'])->name('pengadaan.sync');
    Route::put('/{id}/update-metode', [PengadaanController::class, 'updateMetode'])->name('pengadaan.update_metode');
    Route::post('/document/{id}/upload', [PengadaanController::class, 'uploadDocument'])->name('pengadaan.document.upload');
    Route::get('/{id}/print/{doc}', [PengadaanController::class, 'printDocument'])->name('pengadaan.print');
});