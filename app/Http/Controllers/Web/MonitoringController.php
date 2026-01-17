<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Kinerja\PohonKinerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringController extends Controller
{
    /**
     * Menampilkan Dashboard Monitoring "On Tracking"
     */
public function index()
    {
        $user = Auth::user();
        
        // 1. Tentukan Tahun Berjalan & Kolom Target
        $vision = Vision::on('modul_kinerja')->where('is_active', true)->first();
        $startYear = $vision ? (int)$vision->tahun_awal : date('Y');
        $currentYear = (int) date('Y');
        
        // Hitung index tahun (1 s.d 5)
        $yearIndex = ($currentYear - $startYear) + 1;
        // Pastikan tidak error jika di luar range (misal tahun ke-6, ambil tahun_5 atau 0)
        $targetColumn = ($yearIndex >= 1 && $yearIndex <= 5) ? "tahun_{$yearIndex}" : "tahun_5";

        // 2. Ambil Sub Kegiatan (atau Rencana Aksi) milik OPD User
        // Asumsi: SubActivity punya relasi ke Activity -> Program -> Goal -> PD
        // Disini saya contohkan ambil SubActivity langsung yg statusnya approved
        $items = SubActivity::on('modul_kinerja')
            ->where('status', 'approved')
            // ->where('pd_id', $user->pd_id) // Tambahkan filter OPD jika ada kolomnya
            ->get();

        $dataMonitoring = $items->map(function ($item) use ($targetColumn, $currentYear) {
            // Ambil target dinamis dari kolom tahun_X
            $targetValue = (float) $item->{$targetColumn};
            
            // Simulasi Realisasi (Nanti diganti data real dari Modul Evaluasi)
            $realisasiValue = 0; // Default 0
            
            // Hitung Capaian
            $capaian = ($targetValue > 0) ? ($realisasiValue / $targetValue * 100) : 0;

            return [
                'nama' => $item->nama_sub,
                'indikator' => $item->indikator_sub,
                'satuan' => $item->satuan,
                'tahun_berjalan' => $currentYear,
                'target_tahun_ini' => $targetValue, // Data dari kolom tahun_1/2/3/dst
                'realisasi' => $realisasiValue,
                'persentase' => round($capaian, 2),
                'status' => $capaian >= 100 ? 'Selesai' : ($capaian > 0 ? 'On Progress' : 'Belum Dimulai'),
            ];
        });

        return view('kinerja.monitoring.index', [
            'viewTitle' => 'Monitoring Capaian Kinerja Tahun ' . $currentYear,
            'data' => $dataMonitoring
        ]);
    }

    /**
     * Melihat Log Histori Perubahan
     */
    public function showHistory($id)
    {
        $histories = \App\Models\Kinerja\HistoryKinerja::where('pohon_kinerja_id', $id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('kinerja.pohon.history', compact('histories'));
    }
}