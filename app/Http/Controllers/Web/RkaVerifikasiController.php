<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rka\KakDetail; 
use App\Models\Kak\Kak;
use Illuminate\Support\Facades\Auth;

class RkaVerifikasiController extends Controller
{
    /**
     * HALAMAN 1: Monitoring Usulan Manual (Satu per satu item)
     */
    public function index()
    {
        // Ambil item manual yang statusnya 1 (Menunggu Verifikasi)
        $usulan = KakDetail::with('kak')
                    ->where('is_manual', true)
                    ->where('is_verified', 1) 
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('rka.monitoring_usulan', compact('usulan'));
    }

    // --- LOGIKA ACC/TOLAK ITEM SATUAN (MANUAL) ---

    public function approve($id)
    {
        KakDetail::where('id', $id)->update(['is_verified' => 2]); // 2 = Disetujui
        return back()->with('success', 'Item berhasil disetujui.');
    }

    public function reject(Request $request, $id)
    {
        // Update status di tabel lokal Modul Anggaran
    \DB::connection('modul_anggaran')
        ->table('rka_perencanaan')
        ->where('kak_id', $id)
        ->update([
            'status_internal' => 'revisi', // Kembali muncul di pilihKak user
            'catatan_revisi'  => $request->catatan
        ]);

    return redirect()->route('verifikasi.belanja')->with('success', 'RKA ditolak untuk diperbaiki.');
    }

    public function bulkApprove(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        KakDetail::whereIn('id', $request->ids)->update(['is_verified' => 2]);
        return back()->with('success', count($request->ids) . ' item berhasil disetujui sekaligus.');
    }

    public function bulkReject(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'alasan_tolak' => 'required']);
        
        $items = KakDetail::whereIn('id', $request->ids)->get();
        foreach($items as $item) {
            $catatan = "[DITOLAK MASSAL: " . $request->alasan_tolak . "] " . $item->keterangan;
            $item->update(['is_verified' => 0, 'keterangan' => $catatan]);
        }
        return back()->with('success', count($request->ids) . ' item berhasil ditolak.');
    }


    /**
     * HALAMAN 2: Daftar KAK Masuk (Dokumen Final)
     */
    // app/Http/Controllers/Web/RkaVerifikasiController.php

public function indexBelanja()
{
    /** * SEKARANG: Ambil data dari tabel lokal rka_perencanaan yang statusnya 'pengajuan'.
     * Gunakan Eager Loading 'rincianBelanja' untuk melihat detailnya nanti.
     */
$daftarRka = \App\Models\Rka\RkaPerencanaan::with('rincianBelanja')
                    ->where('status_internal', 'pengajuan')
                    ->orderBy('updated_at', 'desc')
                    ->get();

    return view('rka.verifikasi_belanja', compact('daftarRka'));
}

    /**
     * HALAMAN 3: Detail KAK & Eksekusi (Tampil saat klik tombol 'Periksa')
     */
    public function show($id)
    {
        $kak = Kak::with(['user', 'pohonKinerja', 'rincianBelanja'])
                ->findOrFail($id);

        return view('rka.detail_belanja', compact('kak'));
    }

    // --- LOGIKA ACC/TOLAK DOKUMEN KAK ---

// app/Http/Controllers/Web/RkaVerifikasiController.php

public function setujuKak($id)
{
    // 1. Ambil data Header RKA dari tabel rka_main
    $rkaMain = \App\Models\Rka\Rka::where('kak_id', $id)->firstOrFail();

    // 2. Hitung Total Anggaran dari tabel rincian belanja (kak_details)
    $totalAnggaran = \App\Models\Rka\KakDetail::where('kak_id', $id)->sum('total_harga');

    /** * 3. UPDATE TABEL UTAMA (rka_main):
     * Kita isi Nomor RKA resmi dan Total Anggaran yang sudah dihitung.
     */
    $rkaMain->update([
        'nomor_rka'       => 'RKA/' . now()->format('Ymd') . '/' . str_pad($rkaMain->id, 4, '0', STR_PAD_LEFT),
        'total_anggaran'  => $totalAnggaran,
        'status_anggaran' => 'disetujui',
        'updated_at'      => now()
    ]);

    /** * 4. BINDING DATA:
     * Masukkan ID dari rka_main ke kolom rka_id di tabel rincian belanja.
     * Inilah yang membuat data rincian "mengakar" ke dokumen RKA yang sah.
     */
    \App\Models\Rka\KakDetail::where('kak_id', $id)->update([
        'rka_id' => $rkaMain->id 
    ]);

    // 5. Update Mirror Table lokal agar status di user OPD menjadi 'final'
    \App\Models\Rka\RkaPerencanaan::where('kak_id', $id)->update([
        'status_internal' => 'final',
        'updated_at'      => now()
    ]);

    return redirect()->route('verifikasi.belanja')->with('success', 'RKA Berhasil Disahkan! Nomor: ' . $rkaMain->nomor_rka);
}

    public function tolakKak(Request $request, $id)
    {
    
        $request->validate(['catatan' => 'required']);

    // 1. Update tabel rka_perencanaan (Gunakan 'revisi' karena kolom ini bertipe VARCHAR/TEXT)
    \App\Models\Rka\RkaPerencanaan::where('kak_id', $id)->update([
        'status_internal' => 'revisi', // Nilai ini aman karena kolomnya bukan ENUM kaku
        'catatan_revisi'  => $request->catatan
    ]);

    // 2. Update tabel rka_main (Gunakan 'ditolak' sesuai definisi ENUM di migrasi)
    \App\Models\Rka\Rka::where('kak_id', $id)->update([
        'status_anggaran' => 'ditolak' // UBAH DARI 'revisi' MENJADI 'ditolak'
    ]);

    return redirect()->route('verifikasi.belanja')->with('success', 'RKA berhasil dikembalikan untuk diperbaiki.');
    }
}