<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;  // <--- Tambahkan ini untuk debugging error
use Illuminate\Http\Request;
use App\Models\Rka\RkaMain;
use App\Models\Kak\KakMain; // Pastikan namespace model sesuai file sebelumnya
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan library ini sudah diinstall

class KakController extends Controller
{
    public function manage($rka_id)
    {
        // UPDATE: Load relasi 'details' untuk rincian anggaran
        $rka = RkaMain::with(['subActivity.activity.program', 'details'])->findOrFail($rka_id);
        
        $kak = KakMain::where('rka_main_id', $rka_id)->first();
        
        // ... (Logika Smart Template dll tetap sama) ...

        return view('kak.manage', compact('rka', 'kak'));
    }

    public function store(Request $request, $rka_id)
    {
        // Validasi input yang dipecah
        $request->validate([
            'maksud' => 'required',
            'tujuan' => 'required',
        ]);

 KakMain::updateOrCreate(
        ['rka_main_id' => $rka_id],
        [
            'latar_belakang' => $request->latar_belakang,
            'dasar_hukum' => $request->dasar_hukum,
            'penerima_manfaat' => $request->penerima_manfaat,
            
            // PASTIKAN DUA BARIS INI ADA:
            'maksud' => $request->maksud,
            'tujuan' => $request->tujuan,

            'metode_pelaksanaan' => $request->metode_pelaksanaan,
            'tempat_pelaksanaan' => $request->tempat_pelaksanaan,
            'tahapan_pelaksanaan' => $request->tahapan_pelaksanaan,
            'jadwal_matriks' => $request->jadwal // Jika ada input jadwal manual
        ]
    );

    // Redirect tetap di halaman edit agar user bisa langsung lihat preview
    return redirect()->back()->with('success', 'Dokumen KAK berhasil disimpan.');
}

    // FUNGSI BARU: CETAK PDF
    public function printPdf($rka_id)
    {
        $rka = RkaMain::with(['subActivity.activity.program', 'details'])->findOrFail($rka_id);
        $kak = KakMain::where('rka_main_id', $rka_id)->firstOrFail();

        $pdf = Pdf::loadView('kak.print', compact('rka', 'kak'))
                ->setPaper('a4', 'portrait');

        return $pdf->stream('KAK_'.$rka->subActivity->nama_sub.'.pdf');
    }

    

}