<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rka\KakDetail; // Model Detail Belanja
use App\Models\Kak\Kak;       // Model KAK
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
        $item = KakDetail::findOrFail($id);
        $catatan = "[DITOLAK: " . $request->alasan_tolak . "] " . $item->keterangan;
        
        $item->update([
            'is_verified' => 0, // 0 = Ditolak
            'keterangan' => $catatan
        ]);

        return back()->with('success', 'Item berhasil ditolak.');
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
    public function indexBelanja()
    {
        // Ambil KAK yang statusnya 3 (Menunggu Verifikasi Sekretariat)
        $daftarRka = Kak::with('user', 'rincianBelanja')
                        ->where('status', 3) 
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

    public function setujuKak($id)
    {
        $kak = Kak::findOrFail($id);
        
        // Status 4 = Disahkan / Final
        $kak->update([
            'status' => 4,
            'catatan_sekretariat' => null
        ]);

        return redirect()->route('verifikasi.belanja')->with('success', 'Dokumen KAK berhasil DISAHKAN.');
    }

    public function tolakKak(Request $request, $id)
    {
        $request->validate(['catatan' => 'required']);
        $kak = Kak::findOrFail($id);

        // Status 0 = Ditolak / Revisi (Agar user bisa edit lagi)
        $kak->update([
            'status' => 0, 
            'catatan_sekretariat' => $request->catatan
        ]);

        return redirect()->route('verifikasi.belanja')->with('success', 'Dokumen dikembalikan ke User untuk revisi.');
    }
}