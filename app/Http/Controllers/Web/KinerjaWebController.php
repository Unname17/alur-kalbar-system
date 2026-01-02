<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Helpers\LogKinerja; // <--- Import Helper
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;   
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

// Import Models Lengkap (Koneksi modul_kinerja)
use App\Models\Kinerja\Vision;
use App\Models\Kinerja\Mission;
use App\Models\Kinerja\Goal;
use App\Models\Kinerja\SasaranStrategis;
use App\Models\Kinerja\Program;
use App\Models\Kinerja\Activity;
use App\Models\Kinerja\SubActivity;
use App\Models\Kinerja\AccessSetting;

class KinerjaWebController extends Controller
{
    /**
     * Halaman Dashboard Utama (Unified Dashboard)
     */
    public function index()
    {
        $user = Auth::user();
        $pd_id = $user->pd_id;
        $role = strtolower($user->role->name ?? '');
        $isBappeda = ($role === 'bappeda');
        

        // 1. LOGIKA STATISTIK DINAMIS
        if ($isBappeda) {
            // Bappeda: Statistik Global (Seluruh OPD)
            $stats = [
                'tujuan'       => Goal::on('modul_kinerja')->count(),
                'program'      => Program::on('modul_kinerja')->count(),
                'kegiatan'     => Activity::on('modul_kinerja')->count(),
                'sub_kegiatan' => SubActivity::on('modul_kinerja')->count(),
            ];

            // Bappeda: Pohon dimulai dari Visi
            $treeData = Vision::on('modul_kinerja')
                ->with(['missions.goals.sasaranStrategis.programs.activities.subActivities'])
                ->where('is_active', true)->get();
            $startType = 'visi';
        } else {
            // OPD: Statistik Internal (Filter by pd_id)
            $stats = [
                'tujuan' => Goal::on('modul_kinerja')->where('pd_id', $pd_id)->count(),
                'program' => Program::on('modul_kinerja')
                    ->whereHas('sasaranStrategis.goal', function($q) use ($pd_id) { $q->where('pd_id', $pd_id); })->count(),
                'kegiatan' => Activity::on('modul_kinerja')
                    ->whereHas('program.sasaranStrategis.goal', function($q) use ($pd_id) { $q->where('pd_id', $pd_id); })->count(),
                'sub_kegiatan' => SubActivity::on('modul_kinerja')
                    ->whereHas('activity.program.sasaranStrategis.goal', function($q) use ($pd_id) { $q->where('pd_id', $pd_id); })->count(),
            ];

            // OPD: Pohon dimulai langsung dari TUJUAN PD [Perubahan Krusial]
            $treeData = Goal::on('modul_kinerja')
                ->where('pd_id', $pd_id)
                ->with(['sasaranStrategis.programs.activities.subActivities'])
                ->get();
            $startType = 'tujuan';
        }

        $lockData = AccessSetting::on('modul_kinerja')->where('pd_id', $pd_id)->first();
        $is_locked = $lockData ? (bool)$lockData->is_locked : false;

        LogKinerja::record('ACCESS', 'Pengguna masuk ke Dashboard Kinerja');
        // Gunakan variabel 'treeData' agar sinkron dengan JavaScript di View
        return view('kinerja.opd.index', compact('stats', 'is_locked', 'treeData', 'isBappeda', 'startType'));
    }

    /**
     * Menampilkan Visualisasi Pohon Kinerja Full
     */
    public function showPohonKinerja()
    {
        $user = Auth::user();
        $pd_id = $user->pd_id;
        $role = strtolower($user->role->name ?? '');
        $isValidator = ($role === 'bappeda');

        $lockData = AccessSetting::on('modul_kinerja')->where('pd_id', $pd_id)->first();
        $is_locked = $lockData ? (bool)$lockData->is_locked : false;

        // Logika pengambilan data sama dengan index untuk konsistensi
        if ($isValidator) {
            $treeData = Vision::on('modul_kinerja')
                ->with(['missions.goals.sasaranStrategis.programs.activities.subActivities'])
                ->where('is_active', true)
                ->get();
            $startType = 'visi';
        } else {
            $treeData = Goal::on('modul_kinerja')
                ->where('pd_id', $pd_id)
                ->with(['sasaranStrategis.programs.activities.subActivities'])
                ->get();
            $startType = 'tujuan';
        }

        // 'visions' tetap dikirim sebagai cadangan metadata periode
        $visions = $isValidator ? $treeData : Vision::on('modul_kinerja')->where('is_active', true)->get();

        return view('kinerja.pohon.pohon', compact('treeData', 'visions', 'isValidator', 'is_locked', 'startType'));
    }

    /**
     * Fitur Sinkronisasi Visi-Misi Manual
     */
    public function syncVisiMisi()
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ])->get('https://kalbarprov.go.id/profil/visi-misi/');

            if (!$response->successful()) return response()->json(['message' => 'Gagal terhubung ke website'], 500);

            $crawler = new Crawler($response->body());
            $visiNode = $crawler->filter('.vision-text');
            if ($visiNode->count() === 0) return response()->json(['message' => 'Selektor .vision-text tidak ditemukan'], 422);
            $visiText = trim($visiNode->text(), ' "');

            $misiNodes = $crawler->filter('.mission-container')->first()->filter('.mission-text');
            $misiList = $misiNodes->each(fn(Crawler $node) => trim($node->text()));

            DB::connection('modul_kinerja')->transaction(function () use ($visiText, $misiList) {
                $vision = Vision::on('modul_kinerja')->updateOrCreate(
                    ['is_active' => true],
                    ['visi_text' => $visiText, 'tahun_awal' => 2025, 'tahun_akhir' => 2029]
                );

                Mission::on('modul_kinerja')->where('vision_id', $vision->id)->delete();
                foreach ($misiList as $index => $text) {
                    Mission::on('modul_kinerja')->create([
                        'vision_id' => $vision->id,
                        'nomor_misi' => $index + 1,
                        'misi_text' => $text
                    ]);
                }
            });

            return response()->json(['message' => 'Sinkronisasi Berhasil!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}