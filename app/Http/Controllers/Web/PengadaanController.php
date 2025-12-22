<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

// Import Model dari Folder Baru
use App\Models\Pengadaan\Pengadaan;
use App\Models\Pengadaan\PengadaanDocument;
use App\Models\Pengadaan\Vendor;

// Import Model dari Folder RKA
use App\Models\Rka\Rka;
use App\Models\Rka\RkaPerencanaan;

use Illuminate\Support\Facades\DB;

class PengadaanController extends Controller
{
    /**
     * Menampilkan Halaman Utama (Index)
     * Inilah fungsi yang tadi dianggap 'undefined'
     */
    public function index()
    {
        // Mengambil semua data pengadaan beserta relasinya untuk tampilan antrean
        $daftarPengadaan = Pengadaan::with(['rka', 'rkaPerencanaan', 'documents'])->get();

        return view('pengadaan.index', compact('daftarPengadaan'));
    }

    /**
     * Menampilkan Detail Dokumen
     */
    public function show($id)
{
    $pengadaan = Pengadaan::with(['rkaPerencanaan', 'documents', 'vendor'])->findOrFail($id);
    
    // Ambil semua vendor untuk dropdown di modal
    $vendors = DB::connection('modul_pengadaan')->table('vendors')->get();

    return view('pengadaan.show', compact('pengadaan', 'vendors'));
}


    public function printDocument($id, $docType)
{
    // Pastikan memuat rincianBelanja dari model Rka
$pengadaan = Pengadaan::with(['rka.details', 'rkaPerencanaan', 'vendor'])->findOrFail($id);    
    // Tentukan view berdasarkan jenis dokumen 
    $view = match((int)$docType) {
        1 => 'pdf.spesifikasi',
        2 => 'pdf.hps',
        4 => 'pdf.surat_pesanan', // Template baru kita [cite: 25]
        default => abort(404),
    };

    $pdf = \Pdf::loadView($view, compact('pengadaan'));
    return $pdf->stream("Dokumen_{$docType}.pdf");
}
    /**
     * Fungsi Sinkronisasi Data dari RKA
     */
    public function sync()
    {
        // 1. Ambil data RKA yang sudah 'disetujui' dari database ANGGARAN
        $rkaDisetujui = DB::connection('modul_anggaran')
            ->table('rka_main')
            ->where('status_anggaran', 'disetujui') // Pastikan statusnya final
            ->get();

        if ($rkaDisetujui->isEmpty()) {
            return back()->with('info', 'Belum ada data RKA baru yang disetujui.');
        }

        $count = 0;

        // 2. Gunakan Transaksi di database PENGADAAN
        DB::connection('modul_pengadaan')->transaction(function () use ($rkaDisetujui, &$count) {
            foreach ($rkaDisetujui as $item) {
                // Cek apakah RKA ini sudah pernah di-sync sebelumnya
                $exists = Pengadaan::where('rka_id', $item->id)->exists();

                if (!$exists) {
                    // Masukkan ke tabel pengadaans (Header)
                    $pengadaan = Pengadaan::create([
                        'rka_id'           => $item->id,
                        'kak_id'           => $item->kak_id,
                        'target_volume'    => $item->total_target_volume ?? 0, // Ambil target dari RKA
                        'status_pengadaan' => 'berjalan',
                    ]);

                    // Otomatis buat 9 baris checklist dokumen
                    $listNamaDokumen = [
                        'Spesifikasi Teknis', 'HPS', 'Nota Dinas Pengajuan', 
                        'Surat Pesanan/Kontrak', 'SPMK', 'Laporan Progres', 
                        'Berita Acara Pemeriksaan', 'BAST (Serah Terima)', 'Bukti Pembayaran'
                    ];

                    foreach ($listNamaDokumen as $index => $nama) {
                        PengadaanDocument::create([
                            'pengadaan_id'   => $pengadaan->id,
                            'urutan_dokumen' => $index + 1,
                            'nama_dokumen'   => $nama,
                        ]);
                    }
                    $count++;
                }
            }
        });

        return back()->with('success', "$count paket RKA berhasil disinkronkan ke pengadaan.");
    }

    /**
     * Update Metode Pengadaan (Katalog, PL, Tender)
     */
    // app/Http/Controllers/Web/PengadaanController.php

public function updateMetode(Request $request, $id)
{
    // Validasi pilihan metode sesuai 3 opsi dari mentor
    $request->validate(['metode' => 'required|in:katalog,pl,tender']);

    $pengadaan = Pengadaan::findOrFail($id);
    $pengadaan->update(['metode_pengadaan' => $request->metode]);

    return back()->with('success', 'Metode pengadaan berhasil ditetapkan.');
}

public function uploadDocument(Request $request, $id)
{
    $doc = PengadaanDocument::findOrFail($id);
    $pengadaan = $doc->pengadaan;

    // 1. Proses Simpan File ke Storage
    if ($request->hasFile('file_pdf')) {
        $path = $request->file('file_pdf')->store('dokumen_pengadaan', 'public');
        $doc->update([
            'file_path' => $path,
            'is_verified' => true // Dokumen dianggap sah setelah upload
        ]);
    }

    // 2. LOGIKA KHUSUS DOKUMEN KE-4 (KONTRAK): Kunci Vendor
    if ($doc->urutan_dokumen == 4 && $request->vendor_id) {
        $pengadaan->update(['vendor_id' => $request->vendor_id]);
    }

    // 3. LOGIKA KHUSUS DOKUMEN KE-8 (BAST): Update Pohon Kinerja
    if ($doc->urutan_dokumen == 8 && $request->has('realisasi_volume')) {
        $pengadaan->update([
            'realisasi_volume' => $request->realisasi_volume // Input manual realisasi fisik
        ]);
    }

    return back()->with('success', 'Dokumen ' . $doc->nama_dokumen . ' berhasil diperbarui.');
}
}