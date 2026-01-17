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
    PaguKegiatanController,
    ProcurementController
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
Route::prefix('pengadaan')->name('pengadaan.')->group(function () {
    Route::get('/', [ProcurementController::class, 'index'])->name('index');
    Route::get('/create', [ProcurementController::class, 'create'])->name('create');
    Route::post('/store', [ProcurementController::class, 'store'])->name('store');
    
    // Alur Picking
    Route::get('/{id}/picking', [ProcurementController::class, 'pickItems'])->name('picking');
    Route::post('/{id}/picking', [ProcurementController::class, 'storePickedItems'])->name('store.picked');
    
    // Alur Pelengkap Doc 1 (Alasan & Pengesahan)
    Route::get('/{id}/edit-doc1', [ProcurementController::class, 'editDoc1'])->name('edit.doc1');
    Route::post('/{id}/update-doc1', [ProcurementController::class, 'updateDoc1'])->name('update.doc1');
    
    // Management & Print
    Route::get('/{id}/manage', [ProcurementController::class, 'manage'])->name('manage');
    Route::get('/{id}/print/doc-1', [ProcurementController::class, 'printDoc1'])->name('print.doc1');
    // Route untuk menyimpan data Strategi (Doc 2 & 3)
    // Route untuk Doc 2 & 3 (Strategi)
    Route::post('/{id}/update-strategi', [ProcurementController::class, 'updateStrategi'])->name('update.strategi');
    Route::get('/{id}/print/doc-2', [ProcurementController::class, 'printDoc2'])->name('print.doc2');

    // FIX: Nama disamakan dengan Blade (update.doc3) dan Method Cetak yang benar (printDoc3)
    Route::post('/{id}/update-doc3', [ProcurementController::class, 'updateAnalisisPersiapan'])->name('update.doc3');
    Route::get('/{id}/print/doc-3', [ProcurementController::class, 'printDoc3'])->name('print.doc3');

  // --- ALUR SPESIFIKASI TEKNIS (Doc 4 & 5) ---
    // Update per-item untuk detail spesifikasi, merk, garansi, dsb.
    Route::post('/item/{item_id}/update', [ProcurementController::class, 'updateItemDetail'])->name('update.item');
    // Tambahkan ini di dalam group 'pengadaan.'
Route::post('/{id}/update-items-bulk', [ProcurementController::class, 'batchUpdateItems'])->name('update.items_bulk');
    Route::get('/{id}/print/doc-4', [ProcurementController::class, 'printDoc4'])->name('print.doc4');
    Route::get('/{id}/print/doc-5', [ProcurementController::class, 'printDoc5'])->name('print.doc5');

// --- ALUR ANALISIS HARGA PASAR & KEWAJARAN (Doc 6 & 7) ---
    
    // TAMBAHKAN BARIS INI: Rute untuk menyimpan referensi harga (A.1, A.2, B, C)
    Route::post('/{id}/store-price-ref', [ProcurementController::class, 'storePriceReference'])->name('store.price_ref');
    Route::delete('/price-ref/{id}', [ProcurementController::class, 'destroyPriceReference'])->name('destroy.price_ref');

    // Rute yang sudah ada tetap biarkan
    Route::post('/{id}/store-market', [ProcurementController::class, 'storeMarketAnalysis'])->name('store.market');
    Route::post('/{id}/update-price-justification', [ProcurementController::class, 'updatePriceJustification'])->name('update.price_justification');
    Route::get('/{id}/print/doc-6', [ProcurementController::class, 'printDoc6'])->name('print.doc6');
    Route::get('/{id}/print/doc-7', [ProcurementController::class, 'printDoc7'])->name('print.doc7');

    // Route untuk Doc 9 (Negosiasi)
    Route::post('/{id}/store-negotiation', [ProcurementController::class, 'storeNegotiation'])->name('store.negotiation');

    // Route untuk Doc 10 (Kontrak/SPK)
Route::post('/{id}/store-contract', [ProcurementController::class, 'storeContract'])->name('store.contract');
    Route::get('/{id}/print-doc10', [ProcurementController::class, 'printDoc10'])->name('print.doc10');
    
    Route::get('/api/kbki/{kode}', [ProcurementController::class, 'getKbkiDetail']);
});