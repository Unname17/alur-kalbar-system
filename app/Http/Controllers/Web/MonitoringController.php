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

        // Ambil semua Rencana Aksi milik OPD user
        $rencanaAksi = PohonKinerja::where('opd_id', $user->id_perangkat_daerah)
            ->where('jenis_kinerja', 'rencana_aksi')
            ->with('indikators')
            ->get();

        $dataMonitoring = $rencanaAksi->map(function ($item) {
            // Simulasi Realisasi (Nanti diambil dari Modul 4/5)
            $realisasiValue = 75; // Contoh nilai realisasi
            $targetValue = $item->target_t1 > 0 ? $item->target_t1 : 100;

            // Rumus: Realisasi / Target * 100%
            $capaian = ($realisasiValue / $targetValue) * 100;

            return [
                'nama' => $item->nama_kinerja,
                'target' => $targetValue,
                'realisasi' => $realisasiValue,
                'persentase' => round($capaian, 2),
                'status' => $capaian >= 100 ? 'Selesai' : ($capaian > 0 ? 'On Tracking' : 'Belum Jalan'),
            ];
        });

        return view('kinerja.monitoring.index', [
            'viewTitle' => 'Monitoring Capaian Kinerja',
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