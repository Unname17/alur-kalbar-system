<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rka\RkaMain;
use App\Models\Kak\KakMain;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class KakController extends Controller
{
    public function index()
    {
        $rkas = RkaMain::with(['subActivity', 'kak'])
                ->where('total_anggaran', '>', 0)
                ->orderBy('updated_at', 'desc')
                ->get();

        return view('kak.final_list', compact('rkas'));
    }

    public function manage($rka_id)
    {
        $rka = RkaMain::with(['subActivity.activity.program', 'details'])->findOrFail($rka_id);
        $kak = KakMain::where('rka_main_id', $rka_id)->first();
        
        return view('kak.manage', compact('rka', 'kak'));
    }

    public function store(Request $request, $rka_id)
    {
        $request->validate([
            'maksud' => 'required',
            // Validasi lain opsional karena form dinamis
        ]);

        // 1. CARI DATA KEPALA DINAS (Untuk Tanda Tangan)
        $rka = RkaMain::with('subActivity.activity.program')->findOrFail($rka_id);
        
        $namaPejabat = '-';
        $nipPejabat = '-';
        $jabatanPejabat = 'Kepala Dinas'; // Default

        $pdId = auth()->user()->pd_id;
        $roleKadis = DB::connection('sistem_admin')->table('roles')->where('name', 'kadis')->first();

        if ($roleKadis) {
            $pejabat = DB::connection('sistem_admin')->table('users')
                        ->where('pd_id', $pdId)
                        ->where('role_id', $roleKadis->id)
                        ->where('is_active', true)
                        ->first();
            
            if ($pejabat) {
                $namaPejabat = $pejabat->nama_lengkap;
                $nipPejabat = $pejabat->nip;
                // Opsional: $jabatanPejabat = 'Pengguna Anggaran';
            }
        }

        // 2. BERSIHKAN DATA ARRAY
        // Hapus baris kosong pada Dasar Hukum & Tujuan
        $cleanDasarHukum = array_values(array_filter($request->dasar_hukum ?? []));
        $cleanTujuan = array_values(array_filter($request->tujuan ?? []));
        
        // Hapus tahapan yang uraiannya kosong
        $cleanTahapan = array_values(array_filter($request->tahapan_pelaksanaan ?? [], function($item) {
            return !empty($item['uraian']); 
        }));

        // 3. SIMPAN KAK
        KakMain::updateOrCreate(
            ['rka_main_id' => $rka_id],
            [
                'sub_activity_id' => $rka->sub_activity_id,
                'latar_belakang' => $request->latar_belakang,
                'dasar_hukum' => $cleanDasarHukum,
                'penerima_manfaat' => $request->penerima_manfaat,
                'maksud' => $request->maksud,
                'tujuan' => $cleanTujuan,
                'metode_pelaksanaan' => $request->metode_pelaksanaan,
                'tempat_pelaksanaan' => $request->tempat_pelaksanaan,
                'tahapan_pelaksanaan' => $cleanTahapan, // Sudah include timeline matrix
                'jadwal_matriks' => null, // Kita gabung di tahapan, jadi ini null saja
                
                // Snapshot Pejabat
                'nama_pa_kpa' => $namaPejabat,
                'nip_pa_kpa' => $nipPejabat,
                'jabatan_pa_kpa' => $jabatanPejabat
            ]
        );

        // Redirect ke List dengan Pesan Sukses
        return redirect()->route('kak.index')
                         ->with('success', 'Dokumen KAK berhasil disimpan dan siap dicetak.');
    }

    public function printPdf($rka_id)
    {
        $rka = RkaMain::with(['subActivity.activity.program', 'details'])->findOrFail($rka_id);
        $kak = KakMain::where('rka_main_id', $rka_id)->firstOrFail();

        // Object Pejabat untuk View
        $pejabat = (object) [
            'nama' => $kak->nama_pa_kpa ?? '.......................',
            'nip'  => $kak->nip_pa_kpa ?? '.......................',
            'jabatan' => $kak->jabatan_pa_kpa ?? 'Kepala Dinas'
        ];

        $pdf = Pdf::loadView('kak.print', compact('rka', 'kak', 'pejabat'))
                ->setPaper('a4', 'portrait');

        return $pdf->stream('KAK_'.$rka->subActivity->kode_sub.'.pdf');
    }
}