<?php

use Illuminate\Support\Facades\Route;

// 1. Import Controller Web (Group)
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
    AdminKinerjaController,
    PaguKegiatanController
};

// 2. Import Controller yang berada di root Controller (Penting: Dipisah agar tidak error)

use App\Http\Controllers\Api\Kinerja\PohonKinerjaController;
use App\Http\Controllers\Api\Perencanaan\KakApiController;

/*
|--------------------------------------------------------------------------
| Web & API Routes Simulation
|--------------------------------------------------------------------------
*/

// ========================================================================
// 1. AUTH & PUBLIC ROUTES (Database: sistem_admin)
// ========================================================================
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

// ========================================================================
// 2. MODUL KINERJA (Database: modul_kinerja)
//Prefix URL: /kinerja | Prefix Name: kinerja.
// ========================================================================
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

    // --- MANAJEMEN PAGU (Khusus Bappeda) ---
    // URL: /kinerja/manajemen-pagu
    // Route Name: kinerja.pagu.index
    Route::middleware(['role:bappeda'])->group(function () {
        Route::get('/manajemen-pagu', [PaguKegiatanController::class, 'index'])->name('pagu.index');
        Route::get('/manajemen-pagu/{id}/edit', [PaguKegiatanController::class, 'edit'])->name('pagu.edit');
        Route::put('/manajemen-pagu/{id}', [PaguKegiatanController::class, 'update'])->name('pagu.update');
    });

    // --- ADMINISTRASI (Khusus Bappeda - Manajemen Akses) ---
    Route::middleware(['role:bappeda'])->prefix('admin')->name('admin.')->group(function () {
        
        Route::prefix('access')->name('access.')->group(function () {
            // Halaman Utama Manajemen Akses
            Route::get('/', [KinerjaWizardController::class, 'manageAccess'])->name('index'); 
            
            // Simpan Aturan Baru
            Route::post('/store', [KinerjaWizardController::class, 'storeAccess'])->name('store'); 
            
            // Hapus Aturan
            Route::delete('/{id}', [KinerjaWizardController::class, 'destroyAccess'])->name('destroy'); 
        });

        // Rute Helper AJAX
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
});


// ========================================================================
// 3. MODUL RKA (Database: modul_anggaran)
// ========================================================================
Route::middleware(['auth'])->prefix('rka')->name('rka.')->group(function () {
    // Dashboard SPK
    Route::get('/dashboard', [RkaController::class, 'index'])->name('dashboard');
    
    // TAMBAHKAN INI: Route untuk halaman "Daftar Usulan" agar Navbar tidak error
    Route::get('/edit-header/{id}', [RkaController::class, 'editHeader'])->name('edit_header');
Route::put('/update-header/{id}', [RkaController::class, 'updateHeader'])->name('update_header');
Route::get('/manage-step3/{id}', [RkaController::class, 'manageStep3'])->name('manage_v3');
Route::put('/store-step3/{id}', [RkaController::class, 'storeStep3'])->name('store_step3');    
    
    // Flow Penyusunan
    Route::get('/create/{sub_activity_id}', [RkaController::class, 'createHeader'])->name('create');
    Route::post('/store-header', [RkaController::class, 'storeHeader'])->name('store_header');
    
    Route::get('/manage/{id}', [RkaController::class, 'manageDetails'])->name('manage');
    Route::post('/store-detail/{rka_id}', [RkaController::class, 'storeDetail'])->name('store_detail');
    Route::delete('/delete-detail/{id}', [RkaController::class, 'destroyDetail'])->name('destroy_detail');
    
    // Fitur Baru: Daftar RKA Terfinalisasi
    Route::get('/finalized', [RkaController::class, 'finalizedList'])->name('final');
    
    // Fitur Cetak PDF (Identik format Pemerintah Provinsi Kalimantan Barat)
    Route::get('/print/{id}', [RkaController::class, 'printPdf'])->name('print');
});

// ========================================================================
// 4. MODUL KAK (Database: modul_kak)
// ========================================================================
Route::middleware(['auth'])->prefix('kak')->name('kak.')->group(function () {
    Route::get('/dashboard', [KakController::class, 'index'])->name('index');
    // Menampilkan Form KAK (Create/Edit)
    Route::get('/manage/{rka_id}', [KakController::class, 'manage'])->name('manage');
    
    // Menyimpan Data
    Route::post('/store/{rka_id}', [KakController::class, 'store'])->name('store');
    
    // Cetak PDF (Nanti)
    Route::get('/print/{rka_id}', [KakController::class, 'printPdf'])->name('print');
});

// ========================================================================
// 5. MODUL PENGADAAN (Database: modul_pengadaan)
// ========================================================================
Route::middleware(['auth', 'db.set:modul_pengadaan'])->prefix('pengadaan')->group(function () {
    Route::get('/', [PengadaanController::class, 'index'])->name('pengadaan.index');
    Route::get('/detail/{id}', [PengadaanController::class, 'show'])->name('pengadaan.show');
    Route::post('/sync', [PengadaanController::class, 'sync'])->name('pengadaan.sync');
    Route::put('/{id}/update-metode', [PengadaanController::class, 'updateMetode'])->name('pengadaan.update_metode');
    Route::post('/document/{id}/upload', [PengadaanController::class, 'uploadDocument'])->name('pengadaan.document.upload');
    Route::get('/{id}/print/{doc}', [PengadaanController::class, 'printDocument'])->name('pengadaan.print');
});