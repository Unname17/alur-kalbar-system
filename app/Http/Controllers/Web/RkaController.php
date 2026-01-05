<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kinerja\SubActivity;
use App\Models\Rka\{RkaMain, RkaDetail, MasterRekening};
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;


class RkaController extends Controller
{
    /**
     * Dashboard SPK (Leaderboard Prioritas)
     */
    public function index(Request $request)
    {
        $isSpkMode = $request->query('mode') === 'spk';

        $candidates = SubActivity::on('modul_kinerja')
            ->where('status', 'approved')
            ->with('activity.program')
            ->get();

        $existingRkas = RkaMain::pluck('total_anggaran', 'sub_activity_id')->toArray();
        $rkaIds = RkaMain::pluck('id', 'sub_activity_id')->toArray();

        $mappedItems = $candidates->map(function ($item) use ($existingRkas, $rkaIds) {
            $baseline = (float) $item->baseline_2024;
            $target   = (float) $item->target_2025;
            $gap      = $target - $baseline;

            $scoreGap = 0;
            if ($gap > 0) {
                $persen = ($baseline > 0) ? ($gap / $baseline * 100) : 100;
                $scoreGap = ($persen >= 100) ? 100 : (($persen >= 50) ? 80 : 50);
            } else { $scoreGap = 20; }

            $scoreKlas = match($item->klasifikasi) {
                'IKU' => 100, 'IKD' => 80, default => 50
            };

            $item->spk_score = ($scoreKlas * 0.6) + ($scoreGap * 0.4);
            $item->gap_value = $gap;
            $item->gap_persen = $baseline > 0 ? round(($gap/$baseline)*100) : 100;
            $item->has_rka = isset($rkaIds[$item->id]);
            $item->rka_id  = $rkaIds[$item->id] ?? null;

            return $item;
        });

        $rankedItems = $isSpkMode ? $mappedItems->sortByDesc('spk_score')->values() : $mappedItems->sortBy('kode_sub')->values();

        return view('rka.index', compact('rankedItems', 'isSpkMode'));
    }

    /**
     * Langkah 1: Form Identitas Dokumen
     */
    public function createHeader($sub_activity_id)
    {
        $sub = SubActivity::on('modul_kinerja')->with('activity')->findOrFail($sub_activity_id);
        return view('rka.create_header', compact('sub'));
    }

    /**
     * Simpan Header RKA
     */
    public function storeHeader(Request $request)
    {
        // Simpan ke rka_mains sesuai skema migration
        $rka = RkaMain::create($request->all());
        return redirect()->route('rka.manage', $rka->id);
    }

    /**
     * Langkah 2: Kelola Rincian Belanja
     */
    public function manageDetails($id)
    {
        $rka = RkaMain::with('subActivity.activity')->findOrFail($id);
        $kegiatan = $rka->subActivity->activity;

        // Ambil ID semua Sub-Kegiatan di bawah Kegiatan ini (Modul Kinerja)
        $subActivityIds = DB::connection('modul_kinerja')
            ->table('sub_activities')
            ->where('activity_id', $kegiatan->id)
            ->pluck('id');

        // Hitung akumulasi belanja rka_details menggunakan rka_main_id & sub_total
        $totalTerpakai = DB::connection('modul_anggaran')
            ->table('rka_details')
            ->whereIn('rka_main_id', function($query) use ($subActivityIds) {
                $query->select('id')
                      ->from('rka_mains')
                      ->whereIn('sub_activity_id', $subActivityIds);
            })->sum('sub_total');

        $sisaPagu = $kegiatan->pagu_anggaran - $totalTerpakai;
        $details = RkaDetail::where('rka_main_id', $id)->get();
        $rekenings = MasterRekening::all();

        return view('rka.manage', compact('rka', 'kegiatan', 'details', 'rekenings', 'totalTerpakai', 'sisaPagu'));
    }

    /**
     * Simpan Item Rincian Belanja
     */
    public function storeDetail(Request $request, $rka_id)
    {
        $request->validate([
            'uraian_belanja' => 'required',
            'koefisien' => 'required|numeric|min:1',
            'harga_satuan' => 'required|numeric|min:1',
            'rekening_id' => 'required'
        ]);

        $rka = RkaMain::with('subActivity.activity')->findOrFail($rka_id);
        $kegiatan = $rka->subActivity->activity;

        $subActivityIds = DB::connection('modul_kinerja')->table('sub_activities')
            ->where('activity_id', $kegiatan->id)->pluck('id');

        $totalTerpakai = DB::connection('modul_anggaran')->table('rka_details')
            ->whereIn('rka_main_id', function($query) use ($subActivityIds) {
                $query->select('id')->from('rka_mains')->whereIn('sub_activity_id', $subActivityIds);
            })->sum('sub_total');

        $nominalInputBaru = $request->koefisien * $request->harga_satuan;

        // Validasi Pagu
        if (($totalTerpakai + $nominalInputBaru) > $kegiatan->pagu_anggaran) {
            $sisa = $kegiatan->pagu_anggaran - $totalTerpakai;
            return back()->with('error', 'Pagu tidak cukup. Sisa: Rp ' . number_format($sisa, 0, ',', '.'));
        }

        try {
            RkaDetail::create([
                'rka_main_id' => $rka_id, // Sesuai migration
                'rekening_id' => $request->rekening_id,
                'uraian_belanja' => $request->uraian_belanja,
                'spesifikasi' => $request->spesifikasi,
                'koefisien' => $request->koefisien,
                'satuan' => $request->satuan,
                'harga_satuan' => $request->harga_satuan,
                'sub_total' => $nominalInputBaru, // Sesuai migration
            ]);

            // Sync total_anggaran di RkaMain
            $newTotal = RkaDetail::where('rka_main_id', $rka_id)->sum('sub_total');
            $rka->update(['total_anggaran' => $newTotal]);

            return back()->with('success', 'Item belanja berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Item
     */
    public function destroyDetail($id)
    {
        $detail = RkaDetail::findOrFail($id);
        $rkaId = $detail->rka_main_id;
        $detail->delete();

        $total = RkaDetail::where('rka_main_id', $rkaId)->sum('sub_total');
        RkaMain::where('id', $rkaId)->update(['total_anggaran' => $total]);

        return back()->with('success', 'Item dihapus.');
    }



public function printPdf($id)
{
    // Pastikan relasi subActivity, activity, dan program dimuat sekaligus
    $rka = RkaMain::with(['subActivity.activity.program'])->findOrFail($id);
    
    // Kelompokkan detail berdasarkan rekening
    $details = RkaDetail::with('rekening')
                ->where('rka_main_id', $id)
                ->get()
                ->groupBy('rekening_id');

    // Data Pejabat (Samuel)
    $namaPptk = $rka->nama_pptk ;
    $nipPptk = $rka->nip_pptk ;

    $pdf = Pdf::loadView('rka.print', compact(
        'rka', 
        'details', 
        'namaPptk', 
        'nipPptk'
    ))->setPaper('a4', 'portrait');

    return $pdf->stream('RKA_SKPD_'.$id.'.pdf');
}

    public function finalizedList()
{
    // Mengambil RKA yang sudah memiliki total anggaran (sudah diinput detailnya)
    $finalRka = RkaMain::with(['subActivity'])
                ->where('total_anggaran', '>', 0)
                ->orderBy('updated_at', 'desc')
                ->get();

    return view('rka.final_list', compact('finalRka'));
}
public function editHeader($id)
{
    // Mengambil data RKA yang sudah ada
    $rka = RkaMain::findOrFail($id);
    // Kita tetap butuh data sub kegiatan untuk info di sidebar/header
    $sub = $rka->subActivity; 

    return view('rka.edit_header', compact('rka', 'sub'));
}

public function updateHeader(Request $request, $id)
{
    $rka = RkaMain::findOrFail($id);
    
    // Update data identitas
    $rka->update($request->all());

    // Setelah update, langsung lempar kembali ke Step 2 (Manage)
    return redirect()->route('rka.manage', $rka->id)
                     ->with('success', 'Identitas RKA berhasil diperbarui');
}
// Tambahkan method ini di dalam RkaController.php

public function manageStep3($id)
{
    $rka = RkaMain::with(['subActivity.activity'])->findOrFail($id);
    return view('rka.manage_v3', compact('rka'));
}

public function storeStep3(Request $request, $id)
{
    $rka = RkaMain::findOrFail($id);

    // Mengolah data Tim Anggaran menjadi JSON
    $timData = [];
    if($request->has('tim_nama')) {
        foreach($request->tim_nama as $key => $nama) {
            if(!empty($nama)) {
                $timData[] = [
                    'nama' => $nama,
                    'nip'  => $request->tim_nip[$key] ?? '-',
                    'jabatan' => $request->tim_jabatan[$key] ?? '-'
                ];
            }
        }
    }

    $rka->update([
        'jenis_layanan' => $request->jenis_layanan,
        'spm'           => $request->spm,
        'tim_anggaran'  => count($timData) > 0 ? json_encode($timData) : null
    ]);

    return redirect()->route('rka.final')->with('success', 'RKA berhasil diselesaikan dan siap cetak.');
}
}