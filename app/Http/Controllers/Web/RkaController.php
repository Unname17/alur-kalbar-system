<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rka\{RkaMain, RkaDetail, MasterRekening};
use App\Models\Kinerja\{SubActivity, Activity, Program}; 
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;


class RkaController extends Controller
{

/**
     * Dashboard Utama: Menangani Mode SPK, List Biasa, dan Rekap
     */
    public function index(Request $request)
    {
        // Ambil Mode: 'list' (default), 'spk', atau 'rekap'
        $mode = $request->query('mode', 'list');
        
        // --- LOGIKA 1: MODE REKAPITULASI ---
        if ($mode === 'rekap') {
            $level = $request->query('level', 'program'); // program, kegiatan, sub_kegiatan
            $rekapData = [];
            $grandTotal = 0;

            if ($level == 'program') {
                $rekapData = Program::with(['activities.subActivities.rkaMain'])->get();
            } 
            elseif ($level == 'kegiatan') {
                $rekapData = Activity::with(['program', 'subActivities.rkaMain'])->get();
            } 
            else { // Sub Kegiatan
                $rekapData = SubActivity::with(['activity.program', 'rkaMain'])
                    ->whereHas('rkaMain') // Hanya yang punya RKA
                    ->get();
            }

            // Hitung Grand Total
            $grandTotal = $rekapData->sum(fn($item) => $item->total_anggaran);

            return view('rka.index', compact('mode', 'rekapData', 'level', 'grandTotal'));
        }

        // --- LOGIKA 2: MODE SPK & LIST BIASA (DIPERBARUI) ---
        
        // 1. Ambil Tahun Awal Visi untuk Logika Dinamis
        $vision = \App\Models\Kinerja\Vision::on('modul_kinerja')->where('is_active', true)->first();
        $startYear = $vision ? (int)$vision->tahun_awal : date('Y');

        $candidates = SubActivity::on('modul_kinerja')
            ->where('status', 'approved')
            ->with('activity.program')
            ->get();

        $rkaIds = RkaMain::pluck('id', 'sub_activity_id')->toArray();

        $mappedItems = $candidates->map(function ($item) use ($rkaIds, $startYear) {
            $base = (float) $item->baseline;
            
            // --- Hitung Total Pertumbuhan (Gap) Selama 5 Tahun ---
            $totalGap = 0;
            $accumulatedTarget = 0;

            for ($i = 1; $i <= 5; $i++) {
                $valTahun = (float)$item->{"tahun_$i"}; 
                if ($valTahun > 0) {
                    $totalGap += ($valTahun - $base); // Selisih tahun berjalan vs baseline
                    $accumulatedTarget += $valTahun;
                }
            }

            // --- Logika Scoring SPK (Gap 70%, Klasifikasi 30%) ---
            if ($base > 0) {
                // Persentase pertumbuhan total terhadap total baseline 5 tahun
                $ratio = ($totalGap / ($base * 5)) * 100;
                $scoreGap = ($ratio > 100) ? 100 : ($ratio < 0 ? 0 : $ratio);
                
                // Boost skor jika pertumbuhannya positif signifikan
                if($scoreGap > 0 && $scoreGap < 50) $scoreGap += 30; 
            } else {
                // Jika baseline 0 tapi ada target (kegiatan baru), prioritas tinggi
                $scoreGap = ($accumulatedTarget > 0) ? 100 : 0; 
            }

            $scoreKlas = match($item->klasifikasi) {
                'IKU' => 100, 'IKD' => 80, default => 50
            };

            $item->spk_score = ($scoreGap * 0.7) + ($scoreKlas * 0.3);
            
            // Metadata untuk View
            $item->gap_value = $totalGap;
            // Persentase rata-rata pertumbuhan per tahun
            $item->gap_persen = ($base > 0) ? round(($totalGap / 5 / $base) * 100) : ($accumulatedTarget > 0 ? 100 : 0);
            
            $item->has_rka = isset($rkaIds[$item->id]);
            $item->rka_id  = $rkaIds[$item->id] ?? null;

            return $item;
        });

        // Sorting
        $rankedItems = ($mode === 'spk') 
            ? $mappedItems->sortByDesc('spk_score')->values() 
            : $mappedItems->sortBy('kode_sub')->values();

        return view('rka.index', compact('rankedItems', 'mode', 'startYear'));
    }

    /**
     * Langkah 1: Form Identitas Dokumen (Updated)
     */
    public function createHeader($sub_activity_id)
    {
        // 1. Ambil data Sub Kegiatan dari modul_kinerja
        $sub = SubActivity::on('modul_kinerja')->with('activity')->findOrFail($sub_activity_id);

        // 2. LOGIKA AMBIL DATA PPTK (Kabid) dari database sistem_admin
        // Kita mencari user yang memiliki bidang_id yang sama dengan sub kegiatan ini
        // dan memiliki role 'kabid' (Role ID: 3 berdasarkan DatabaseAdminSeeder)
        $pptk = DB::connection('sistem_admin')->table('users')
                    ->where('bidang_id', $sub->bidang_id) // Pastikan sub_activities punya kolom bidang_id
                    ->where('role_id', 3) // ID Role Kabid
                    ->first();

        // Jika tidak ketemu berdasarkan bidang, kita beri fallback (opsional)
        $pptkNama = $pptk->nama_lengkap ?? 'Pejabat Belum Ditunjuk';
        $pptkNip  = $pptk->nip ?? '-';

        // 3. HITUNG TOTAL TERPAKAI PADA KEGIATAN INDUK (Untuk monitoring sisa anggaran)
        $activityId = $sub->activity_id;
        $siblingSubIds = SubActivity::on('modul_kinerja')
                            ->where('activity_id', $activityId)
                            ->pluck('id');

        $totalTerpakai = RkaMain::whereIn('sub_activity_id', $siblingSubIds)
                            ->sum('total_anggaran');

        // 4. Kirim semua variabel ke view termasuk pptkNama dan pptkNip
        return view('rka.create_header', compact('sub', 'totalTerpakai', 'pptkNama', 'pptkNip'));
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
     * Langkah 2: Kelola Rincian Belanja (Updated)
     */
    public function manageDetails($id)
    {
        // Ambil data RKA beserta sub-activity dan activity-nya
        $rka = RkaMain::with('subActivity.activity')->findOrFail($id);
        $kegiatan = $rka->subActivity->activity;

        // Ambil rincian belanja untuk RKA ini
        $details = RkaDetail::where('rka_main_id', $id)->get();
        
        // Ambil semua daftar rekening
        $rekenings = MasterRekening::all();

        // Kita hanya perlu mengirim data dasar. Total usulan sudah ada di dalam objek $rka
        return view('rka.manage', compact('rka', 'kegiatan', 'details', 'rekenings'));
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
        // 1. Ambil data RKA
        $rka = RkaMain::with(['subActivity.activity.program'])->findOrFail($id);
        
        // 2. Kelompokkan detail belanja
        $details = RkaDetail::with('rekening')
                    ->where('rka_main_id', $id)
                    ->get()
                    ->groupBy('rekening_id');

        // --- UPDATE: INISIALISASI VARIABEL ---
        $namaPptk   = '(Pejabat Belum Ditunjuk)';
        $nipPptk    = '-';
        $kodeBidang = '-';
        $namaBidang = '-';
        $kodePd     = '-';
        $namaPd     = '-';

        $program = $rka->subActivity->activity->program ?? null;

        if ($program && $program->bidang_id) {
            // A. AMBIL DATA BIDANG & PD (Join antar tabel di sistem_admin)
            $infoOrganisasi = DB::connection('sistem_admin')->table('bidang')
                                ->join('perangkat_daerah', 'bidang.pd_id', '=', 'perangkat_daerah.id')
                                ->where('bidang.id', $program->bidang_id)
                                ->select(
                                    'bidang.kode_bidang', 
                                    'bidang.nama_bidang', 
                                    'perangkat_daerah.kode_pd', 
                                    'perangkat_daerah.nama_pd'
                                )
                                ->first();

            if ($infoOrganisasi) {
                $kodeBidang = $infoOrganisasi->kode_bidang;
                $namaBidang = $infoOrganisasi->nama_bidang;
                $kodePd     = $infoOrganisasi->kode_pd;
                $namaPd     = $infoOrganisasi->nama_pd;
            }

            // B. AMBIL PPTK (Logic Lama tetap jalan)
            $roleKabid = DB::connection('sistem_admin')->table('roles')
                            ->where('name', 'kabid')->first();

            if ($roleKabid) {
                $pptk = DB::connection('sistem_admin')->table('users')
                            ->where('bidang_id', $program->bidang_id)
                            ->where('role_id', $roleKabid->id)
                            ->where('is_active', true)
                            ->first();
                
                if ($pptk) {
                    $namaPptk = $pptk->nama_lengkap;
                    $nipPptk  = $pptk->nip;
                }
            }
        }

        // 4. Generate PDF (Kirim variabel baru ke View)
        $pdf = Pdf::loadView('rka.print', compact(
            'rka', 'details', 'namaPptk', 'nipPptk',
            'kodeBidang', 'namaBidang', 'kodePd', 'namaPd'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('RKA_SKPD_'.$rka->subActivity->kode_sub.'.pdf');
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