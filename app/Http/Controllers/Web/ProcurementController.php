<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengadaan\ProcurementPackage;
use App\Models\Pengadaan\ProcurementItem;
use App\Models\Rka\RkaDetail;
use App\Models\Pengadaan\KbkiMaster;
use App\Models\Pengadaan\ProcurementVendor;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ProcurementController extends Controller
{

    protected $connection = 'modul_pengadaan';
    /**
     * 1. DASHBOARD PENGADAAN
     */
    public function index()
    {
        $packages = ProcurementPackage::with(['items'])->orderBy('updated_at', 'desc')->get();
        return view('pengadaan.index', compact('packages'));
    }

    /**
     * 2. CREATE - LANGKAH 1: IDENTITAS PAKET
     */
    public function create()
    {
        return view('pengadaan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_paket' => 'required|string|max:255',
            'jenis_pengadaan' => 'required',
            'metode_pemilihan' => 'required',
        ]);

        $package = ProcurementPackage::create([
            'nama_paket' => $request->nama_paket,
            'jenis_pengadaan' => $request->jenis_pengadaan,
            'metode_pemilihan' => $request->metode_pemilihan,
            'pagu_paket' => 0, // Pagu awal 0 (Bottom-up)
            'is_pdn' => $request->has('is_pdn'),
            'is_umkm' => $request->has('is_umkm'),
            'status_tahapan' => 'identifikasi',
        ]);

        return redirect()->route('pengadaan.picking', $package->id)
                         ->with('success', 'Wadah paket dibuat. Silakan pilih item belanja.');
    }

    /**
     * 3. PICKING - LANGKAH 2: PILIH BARANG DARI RKA
     */
    public function pickItems($id)
    {
        $package = ProcurementPackage::findOrFail($id);

        // Ambil data dari modul_anggaran & modul_kak
        $availableItems = RkaDetail::on('modul_anggaran')
            ->with(['rkaMain.subActivity'])
            ->whereHas('rkaMain', function($q) {
                $q->where('status', 'diterima')
                  ->whereExists(function ($query) {
                      $query->select(DB::raw(1))
                            ->from('alur_kalbar_kak.kak_mains')
                            ->whereColumn('kak_mains.rka_main_id', 'rka_mains.id');
                  });
            })->get();

        return view('pengadaan.picking', compact('package', 'availableItems'));
    }

    public function storePickedItems(Request $request, $id)
    {
        if(!$request->selected_items) {
            return back()->with('error', 'Pilih minimal satu item belanja.');
        }

        DB::transaction(function () use ($request, $id) {
            foreach ($request->selected_items as $rka_detail_id) {
                $rkaDetail = RkaDetail::on('modul_anggaran')->find($rka_detail_id);
                
                ProcurementItem::updateOrCreate(
                    ['package_id' => $id, 'rka_detail_id' => $rka_detail_id],
                    [
                        'nama_item' => $rkaDetail->uraian_belanja,
                        'volume' => $rkaDetail->koefisien,
                        'satuan' => $rkaDetail->satuan,
                        'harga_satuan_rka' => $rkaDetail->harga_satuan,
                        'harga_satuan_hps' => $rkaDetail->harga_satuan,
                        'total_hps' => $rkaDetail->sub_total,
                    ]
                );
            }

            // HITUNG PAGU OTOMATIS (BOTTOM-UP)
            $totalPagu = ProcurementItem::where('package_id', $id)->sum('total_hps');
            ProcurementPackage::where('id', $id)->update(['pagu_paket' => $totalPagu]);
        });

        // ALUR BARU: Mampir ke form pelengkap Doc 1
        return redirect()->route('pengadaan.edit.doc1', $id)
                         ->with('success', 'Item belanja berhasil di-pick.');
    }

    /**
     * 4. EDIT DOC 1 - LANGKAH 3: LENGKAPI ALASAN & PENGESAHAN
     */
// ... (method dashboard, create, picking tetap sama) ...

public function editDoc1($id)
{
    $package = ProcurementPackage::with(['items.rkaDetail.rkaMain'])->findOrFail($id);
    $subActivityIds = $package->items->pluck('rkaDetail.rkaMain.sub_activity_id')->unique();

    // 1. Tarik identitas organisasi
    $identitasOrganisasi = DB::connection('modul_kinerja')->table('sub_activities as sub')
        ->join('activities as act', 'sub.activity_id', '=', 'act.id')
        ->join('programs as prog', 'act.program_id', '=', 'prog.id')
        ->whereIn('sub.id', $subActivityIds)
        ->select('prog.nama_program as program', 'act.nama_kegiatan as kegiatan', 'sub.nama_sub as output')
        ->distinct()->get();

    // 2. AMBIL MASTER KBKI DARI DATABASE PENGADAAN
    $kbkiList = DB::connection('modul_pengadaan')->table('kbki_masters')->get();

    // 3. SET JADWAL STATIS (Januari - Desember)
    $jadwalDefault = "Januari - Desember " . date('Y');
    $lokasiDefault = "Kantor Dinas Kominfo Prov. Kalbar";

    $tenagaAhliCandidates = $package->items->filter(function($item) {
        return str_contains(strtolower($item->nama_item), 'tenaga ahli');
    });

    return view('pengadaan.edit_doc1', compact(
        'package', 
        'identitasOrganisasi', 
        'tenagaAhliCandidates',
        'jadwalDefault',
        'lokasiDefault',
        'kbkiList'
    ));
}

/**
 * API UNTUK AUTO-FILL DESKRIPSI KBKI (SOLUSI ERROR GAGAL AMBIL DATA)
 */
/**
 * Mengambil detail KBKI berdasarkan kode (untuk AJAX)
 */
public function getKbkiDetail($kode)
{
    // Cari data di tabel kbki_masters berdasarkan kode_kbki
    $kbki = DB::connection('modul_pengadaan')
              ->table('kbki_masters')
              ->where('kode_kbki', $kode)
              ->first();

    if ($kbki) {
        // Kirimkan data dalam format JSON agar bisa dibaca JavaScript
        return response()->json($kbki);
    }

    // Jika tidak ditemukan, kirim error 404
    return response()->json(['error' => 'Data tidak ditemukan'], 404);
}

/**
 * Menyimpan data lengkap Dokumen 1 sesuai standar regulasi
 */
public function updateDoc1(Request $request, $id)
{
    $package = ProcurementPackage::findOrFail($id);
    
    $package->update([
        // 1.1 Status Dokumen
        'perubahan_ke' => $request->perubahan_ke,
        'tanggal_perubahan' => $request->tanggal_perubahan,

        // 2.2 Pertimbangan Akun
        'pertimbangan_akun' => $request->pertimbangan_akun,

        // 3.2 Prioritas PDN & Alasan
        'opsi_pdn' => $request->opsi_pdn,
        'alasan_pdn' => $request->alasan_pdn,

        // 4.2.1 Alasan Metode
        'alasan_metode_pemilihan' => $request->alasan_metode_pemilihan,

        // 4.3.3 KBKI
        'kode_kbki' => $request->kode_kbki,
        'deskripsi_kbki' => $request->deskripsi_kbki,

        // 5.2 Uraian Pekerjaan
        'uraian_pekerjaan' => $request->uraian_pekerjaan,

        // 6.1 Penyusunan
        'tanggal_penyusunan' => $request->tanggal_penyusunan,
        
        // Data yang sudah ada sebelumnya
        'alasan_pemilihan_jenis' => $request->alasan_pemilihan_jenis,
        'lokasi_pekerjaan' => $request->lokasi_pekerjaan,
        'jadwal_pelaksanaan' => $request->jadwal_pelaksanaan,
        'nama_pa_kpa' => $request->nama_pa_kpa,
        'nip_pa_kpa' => $request->nip_pa_kpa,
        'nama_tenaga_ahli' => $request->nama_tenaga_ahli,
        'status_tahapan' => 'identifikasi_selesai',
    ]);

    return redirect()->route('pengadaan.manage', $id)->with('success', 'Dokumen 1 telah diverifikasi dan siap cetak.');
}

    /**
 * 7. UPDATE STRATEGI - LANGKAH 4: Form untuk Doc 2 & 3
 */
/**
 * UPDATE STRATEGI (Doc 2 & 3)
 */

public function updateStrategi(Request $request, $id)
    {
        DB::connection($this->connection)->table('procurement_preparations')->updateOrInsert(
            ['package_id' => $id],
            [
                'jalur_prioritas' => $request->jalur_prioritas,
                'jalur_strategis' => $request->jalur_strategis,
                'justifikasi_pilihan' => $request->justifikasi_pilihan,
                'alasan_metode' => $request->alasan_metode ?? 'Sesuai dengan justifikasi strategi e-purchasing.',
                'kriteria_barang_jasa' => $request->kriteria_barang_jasa ?? 'Standar',
                
                // Konversi checkbox ke boolean/integer
                'uji_pasar_ideal' => $request->has('uji_pasar_ideal') ? 1 : 0,
                'uji_non_kritikal' => $request->has('uji_non_kritikal') ? 1 : 0,
                'uji_nol_value_added' => $request->has('uji_nol_value_added') ? 1 : 0,
                'uji_spek_stabil' => $request->has('uji_spek_stabil') ? 1 : 0,
                'uji_pengalaman_identik' => $request->has('uji_pengalaman_identik') ? 1 : 0,
                
                'tanggal_analisis' => now(),
                'updated_at' => now(),
            ]
        );

        // Update status tahapan paket
        ProcurementPackage::where('id', $id)->update(['status_tahapan' => 'strategi']);

        return redirect()->route('pengadaan.manage', $id)->with([
            'success' => 'Strategi Pengadaan (Doc 2) berhasil disimpan.',
            'tab' => 'strategi' // State agar tetap di tab strategi
        ]);
    }

    /**
     * 4. UPDATE ANALISA (DOC 3)
     */
    public function updateDoc3(Request $request, $id)
    {
        DB::connection($this->connection)->table('procurement_preparation_analyses')->updateOrInsert(
            ['package_id' => $id],
            [
                'nama_calon_penyedia' => $request->nama_calon_penyedia,
                'produk_katalog' => $request->produk_katalog,
                'harga_tayang_katalog' => $request->harga_tayang_katalog,
                'link_produk_katalog' => $request->link_produk_katalog,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return redirect()->route('pengadaan.manage', $id)->with([
            'success' => 'Kertas Kerja Analisa (Doc 3) berhasil disimpan.',
            'tab' => 'strategi'
        ]);
    }

/**
 * Cetak Dokumen 2 - Justifikasi Strategis Pemilihan Metode
 */
public function printDoc2($id)
{
    $package = ProcurementPackage::with(['preparation'])->findOrFail($id);

    // Kirim data ke view PDF
    $pdf = Pdf::loadView('pengadaan.print.doc2', compact('package'))
              ->setPaper('a4', 'portrait');

    return $pdf->stream('Doc2_Justifikasi_Strategi_' . $package->id . '.pdf');
}

/**
 * Menyimpan Analisis Persiapan (Doc 3)
 */
public function updateAnalisisPersiapan(Request $request, $id)
{
    DB::connection('modul_pengadaan')->table('procurement_preparation_analyses')->updateOrInsert(
        ['package_id' => $id],
        [
            'nama_calon_penyedia' => $request->nama_calon_penyedia,
            'produk_katalog' => $request->produk_katalog,
            'harga_tayang_katalog' => $request->harga_tayang_katalog,
            'link_produk_katalog' => $request->link_produk_katalog,
            'evaluasi_teknis' => json_encode($request->eval_teknis),
            'evaluasi_harga' => json_encode($request->eval_harga),
            'evaluasi_kontrak' => json_encode($request->eval_kontrak),
            'evaluasi_katalog' => json_encode($request->eval_katalog),
            'updated_at' => now(),
        ]
    );

    return redirect()->back()->with('success', 'Kertas Kerja Analisis (Doc 3) berhasil disimpan.');
}

/**
 * Cetak Dokumen 3 - Kertas Kerja Induk
 */
public function printDoc3($id)
{
    $package = ProcurementPackage::with(['items.rkaDetail.rkaMain'])->findOrFail($id);
    $analysis = DB::connection('modul_pengadaan')->table('procurement_preparation_analyses')
                  ->where('package_id', $id)->first();
    
    $analysisData = $analysis ? [
        'calon' => $analysis,
        'teknis' => json_decode($analysis->evaluasi_teknis, true),
        'harga' => json_decode($analysis->evaluasi_harga, true),
        'kontrak' => json_decode($analysis->evaluasi_kontrak, true),
        'katalog' => json_decode($analysis->evaluasi_katalog, true),
    ] : null;

    $pdf = Pdf::loadView('pengadaan.print.doc3', compact('package', 'analysisData'))
              ->setPaper('a4', 'portrait');

    return $pdf->stream('Doc3_Kertas_Kerja_Analisis_' . $id . '.pdf');
}

/**
 * UPDATE DETAIL ITEM / SPESIFIKASI (Doc 4 & 5)
 * Menangani error 'pengadaan.store.item'
 */
// --- INPUT DOKUMEN 4 & 5 (Detail Item) ---
public function updateItemDetail(Request $request, $item_id)
{
    $item = ProcurementItem::findOrFail($item_id);
    
    $item->update([
        'merk_tipe' => $request->merk_tipe,
        'masa_garansi' => $request->masa_garansi,
        'standar_mutu' => $request->standar_mutu,
        'aspek_pemeliharaan' => $request->aspek_pemeliharaan,
        'deskripsi_spesifikasi' => $request->deskripsi_spesifikasi,
    ]);

    return redirect()->back()->with('success', 'Spesifikasi item berhasil disimpan.');
}

public function batchUpdateItems(Request $request, $id)
{
    $itemsData = $request->input('items', []);

    foreach ($itemsData as $itemId => $data) {
        $item = ProcurementItem::where('id', $itemId)->where('package_id', $id)->first();
        if ($item) {
            $item->update([
                'merk_tipe' => $data['merk_tipe'] ?? null,
                'standar_mutu' => $data['standar_mutu'] ?? null,
                'masa_garansi' => $data['masa_garansi'] ?? null,
                'aspek_pemeliharaan' => $data['aspek_pemeliharaan'] ?? null,
                'fungsi_kinerja' => $data['fungsi_kinerja'] ?? null,
                'link_produk_katalog' => $data['link_produk_katalog'] ?? null,
                'deskripsi_spesifikasi' => $data['deskripsi_spesifikasi'] ?? null,
            ]);
        }
    }

    return redirect()->back()->with('success', 'Data Doc 4 & 5 berhasil diperbarui.');
}

/**
 * Simpan Referensi Harga (Doc 6 - Bagian II) dengan Upload Screenshot
 */
public function storePriceReference(Request $request, $id)
{
    // 1. Proses Upload File Bukti (Screenshot) jika ada
    $fileName = null;
    if ($request->hasFile('file_bukti')) {
        $file = $request->file('file_bukti');
        // Simpan ke folder: storage/app/public/bukti_harga
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('public/bukti_harga', $fileName);
    }

    // 2. Simpan Data ke Tabel Referensi
    DB::connection('modul_pengadaan')->table('procurement_price_references')->insert([
        'package_id' => $id,
        'type' => $request->type,
        'merek_model' => $request->merek_model,
        'sumber_nama' => $request->sumber_nama,
        'link_url' => $request->link_url,
        'harga_satuan' => $request->harga_satuan ?? 0,
        'kelebihan' => $request->kelebihan,
        'kekurangan' => $request->kekurangan,
        'garansi_layanan' => $request->garansi_layanan,
        'nomor_tanggal_dok' => $request->nomor_tanggal_dok,
        'tahun_anggaran' => $request->tahun_anggaran,
        'catatan_relevansi' => $request->catatan_relevansi,
        'catatan_penyesuaian' => $request->catatan_penyesuaian,
        'file_bukti' => $fileName, // Simpan nama file SS
        'tanggal_akses' => now(),
        'created_at' => now(),
    ]);

    // 3. Update Ringkasan Harga di Tabel Paket
    $prices = DB::connection('modul_pengadaan')->table('procurement_price_references')
        ->where('package_id', $id)
        ->whereIn('type', ['market', 'sbu', 'contract'])
        ->pluck('harga_satuan');

    if ($prices->count() > 0) {
        DB::connection('modul_pengadaan')->table('procurement_packages')->where('id', $id)->update([
            'hps_terendah' => $prices->min(),
            'hps_tertinggi' => $prices->max(),
            'hps_hitung_rata_rata' => $prices->avg(),
        ]);
    }

    return redirect()->back()->with('success', 'Referensi dan Bukti Screenshot berhasil disimpan.');
}

/**
 * Update Justifikasi & Upload Lampiran Resmi (Doc 6 - Bagian IV & V)
 */
public function updatePriceJustification(Request $request, $id)
{
    $package = ProcurementPackage::findOrFail($id);
    
    $dataUpdate = [
        'kesimpulan_analisis_harga' => $request->kesimpulan_analisis_harga,
        'updated_at' => now(),
    ];

    // Proses Upload File SBU Utama jika ada
    if ($request->hasFile('file_sbu')) {
        $fileSbu = $request->file('file_sbu');
        $nameSbu = 'sbu_' . time() . '.' . $fileSbu->getClientOriginalExtension();
        $fileSbu->storeAs('public/dokumen_pendukung', $nameSbu);
        $dataUpdate['file_sbu'] = $nameSbu;
    }

    // Proses Upload File Kontrak Lama jika ada
    if ($request->hasFile('file_kontrak_lama')) {
        $fileKontrak = $request->file('file_kontrak_lama');
        $nameKontrak = 'kontrak_' . time() . '.' . $fileKontrak->getClientOriginalExtension();
        $fileKontrak->storeAs('public/dokumen_pendukung', $nameKontrak);
        $dataUpdate['file_kontrak_lama'] = $nameKontrak;
    }

    $package->update($dataUpdate);

    return redirect()->back()->with('success', 'Justifikasi akhir dan lampiran berhasil diperbarui.');
}

public function destroyPriceReference($id)
{
    DB::connection('modul_pengadaan')->table('procurement_price_references')->where('id', $id)->delete();
    return redirect()->back()->with('success', 'Referensi berhasil dihapus.');
}

// --- INPUT DOKUMEN 6 & 7 (Analisis Pasar & Harga) ---
public function storeMarketAnalysis(Request $request, $id)
{
    // Simpan data survei pasar [cite: 1838, 2050]
    DB::connection('modul_pengadaan')->table('procurement_market_analysis')->insert([
        'package_id' => $id,
        'sumber_referensi' => $request->sumber_referensi, // Contoh: Tokopedia/Bhinneka
        'link_url' => $request->link_url,
        'harga_tayang' => $request->harga_tayang,
        'tanggal_akses' => $request->tanggal_akses,
        'ulasan_kualitatif' => $request->ulasan_kualitatif, // Bagian A.1 Doc 6 [cite: 1831]
        'reputasi_merek' => $request->reputasi_merek,
        'spesifikasi_sumber' => $request->spesifikasi_sumber,
        'created_at' => now(),
    ]);

    return redirect()->back()->with('success', 'Referensi harga pasar berhasil ditambahkan.');
}



// --- METHOD CETAK (PDF) ---
public function printDoc4($id) { 
    $package = ProcurementPackage::with('items')->findOrFail($id);
    return Pdf::loadView('pengadaan.print.doc4', compact('package'))->setPaper('a4', 'portrait')->stream('Doc4_Spek_EP.pdf'); 
}

public function printDoc5($id) { 
    $package = ProcurementPackage::with('items')->findOrFail($id);
    return Pdf::loadView('pengadaan.print.doc5', compact('package'))->setPaper('a4', 'portrait')->stream('Doc5_Spek_Umum.pdf'); 
}

public function printDoc6($id)
{
    // Mengambil data paket beserta item dan semua referensi harga
    $package = ProcurementPackage::with(['items', 'price_references'])->findOrFail($id);

    // Load view PDF
    $pdf = Pdf::loadView('pengadaan.print.doc6', compact('package'));
    
    // Set format kertas A4 Portrait
    $pdf->setPaper('a4', 'portrait');

    return $pdf->stream('Dokumen_6_Analisis_Harga_' . $package->id . '.pdf');
}

public function printDoc7($id) { 
    $package = ProcurementPackage::with(['marketAnalyses'])->findOrFail($id);
    return Pdf::loadView('pengadaan.print.doc7', compact('package'))->setPaper('a4', 'portrait')->stream('Doc7_Referensi_Harga.pdf'); 
}



public function storeContract(Request $request, $id)
{
    // 1. Validasi
    $request->validate([
        'vendor_id' => 'required|exists:modul_pengadaan.procurement_vendors,id',
        'nomor_sp' => 'required|string',
        'tanggal_sp' => 'required|date',
    ]);

    // 2. Kalkulasi Total (Tetap menggunakan sum agar akurat Rp 234,3 Juta)
    $totalNilaiKontrak = DB::connection($this->connection)
        ->table('procurement_items')
        ->where('package_id', $id)
        ->sum('total_hps');

    $package = DB::connection($this->connection)->table('procurement_packages')->where('id', $id)->first();

    // 3. Simpan data lengkap termasuk Jaminan dan Syarat Khusus
    DB::connection($this->connection)->table('procurement_contracts')->updateOrInsert(
        ['package_id' => $id],
        [
            'vendor_id' => $request->vendor_id,
            'nomor_sp' => $request->nomor_sp,
            'tanggal_sp' => $request->tanggal_sp,
            'sumber_dana' => $request->sumber_dana,
            'waktu_penyelesaian' => $request->waktu_penyelesaian,
            'alamat_penyerahan' => $request->alamat_penyerahan,
            'nilai_kontrak_final' => $totalNilaiKontrak,
            
            // KOLOM BARU UNTUK MELENGKAPI DOC 10
            'nilai_jaminan_pelaksanaan' => $request->nilai_jaminan_pelaksanaan,
            'penerbit_jaminan' => $request->penerbit_jaminan,
            'syarat_khusus_tambahan' => $request->syarat_khusus_tambahan,

            'nama_pejabat_penandatangan' => $package->nama_pa_kpa,
            'nip_pejabat_penandatangan' => $package->nip_pa_kpa,
            'jabatan_pejabat' => 'Pejabat Pembuat Komitmen (PPK)',
            'updated_at' => now(),
            'created_at' => now(),
        ]
    );

    return back()->with('success', 'Kontrak Smart City telah dilengkapi dengan Jaminan Pelaksanaan & Denda.');
}

    /**
     * Cetak PDF Surat Pesanan (Doc 10)
     */
public function printDoc10($id)
{
    // Ambil data paket dan kontrak beserta relasinya
    $package = DB::connection($this->connection)->table('procurement_packages')->where('id', $id)->first();
    $contract = DB::connection($this->connection)->table('procurement_contracts')->where('package_id', $id)->first();

    if (!$contract) {
        return "Data kontrak belum lengkap. Silakan simpan form Doc 10 terlebih dahulu.";
    }

    $vendor = DB::connection($this->connection)->table('procurement_vendors')->where('id', $contract->vendor_id)->first();
    $items = DB::connection($this->connection)->table('procurement_items')->where('package_id', $id)->get();

    // Load view PDF dengan data tambahan jaminan dan syarat khusus
    $pdf = Pdf::loadView('pengadaan.print.doc10', compact('package', 'contract', 'vendor', 'items'));
    
    return $pdf->stream('Doc10_Surat_Pesanan_' . $id . '.pdf');
}

    /**
     * 5. MANAGE - PUSAT KENDALI 10 DOKUMEN
     */
    public function manage($id)
    {
        $package = ProcurementPackage::with(['items', 'preparation', 'marketAnalyses', 'negotiations.vendor', 'contract','price_references'])->findOrFail($id);
        $vendors = ProcurementVendor::all();
        $winner = $package->negotiations()->where('hasil_akhir', 'Sepakat')->first();
        // TAMBAHKAN BARIS INI: Ambil data analisis agar bisa ditampilkan di form
    $analysis = DB::connection('modul_pengadaan')
                  ->table('procurement_preparation_analyses')
                  ->where('package_id', $id)
                  ->first();

        return view('pengadaan.manage', compact('package', 'vendors', 'winner', 'analysis'));
    }

    /**
     * 6. PRINT DOC 1 - GENERATE PDF
     */


    
/**
 * Cetak Dokumen 1 - Identifikasi Kebutuhan (Full Update)
 */
public function printDoc1($id)
{
    // 1. Ambil data paket beserta relasi items dan anggaran
    $package = ProcurementPackage::with([
        'items.rkaDetail.rekening', 
        'items.rkaDetail.rkaMain'
    ])->findOrFail($id);

    // 2. Tarik Identitas Organisasi (Konsolidasi Multi-Program)
    $subActivityIds = $package->items->pluck('rkaDetail.rkaMain.sub_activity_id')->unique();
    $identitasOrganisasi = DB::connection('modul_kinerja')->table('sub_activities as sub')
        ->join('activities as act', 'sub.activity_id', '=', 'act.id')
        ->join('programs as prog', 'act.program_id', '=', 'prog.id')
        ->whereIn('sub.id', $subActivityIds)
        ->select(
            'prog.nama_program as program',
            'act.nama_kegiatan as kegiatan',
            'sub.nama_sub as output'
        )
        ->distinct()->get();

    // 3. Grouping Akun untuk Tabel 2.2
    $accountDetails = $package->items->groupBy(function($item) {
        return $item->rkaDetail->rekening->kode_rekening;
    })->map(function($group) {
        return [
            'kode' => $group->first()->rkaDetail->rekening->kode_rekening,
            'nama' => $group->first()->rkaDetail->rekening->nama_rekening,
            'total' => $group->sum('total_hps')
        ];
    });

    // 4. Generate PDF
    $pdf = Pdf::loadView('pengadaan.print.doc1', compact('package', 'identitasOrganisasi', 'accountDetails'))
              ->setPaper('a4', 'portrait');

    return $pdf->stream('Doc1_Identifikasi_' . $package->id . '.pdf');
}

public function archive()
{
    // 1. Ambil semua paket yang statusnya 'kontrak_selesai'
    $packages = ProcurementPackage::with(['contract.vendor'])
        ->where('status_tahapan', 'kontrak_selesai')
        ->orderBy('updated_at', 'desc')
        ->get();

    // 2. Hitung total akumulasi belanja dari seluruh paket yang diarsip
    $totalSpending = $packages->sum(function($pkg) {
        return $pkg->contract->nilai_kontrak_final ?? 0;
    });

    // 3. Hitung jumlah paket untuk statistik tambahan
    $totalPackages = $packages->count();

    return view('pengadaan.archive', compact('packages', 'totalSpending', 'totalPackages'));
}

}