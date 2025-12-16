<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kinerja\PohonKinerja;

class PohonKinerjaController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // LOGIKA FILTERING
        if ($user->peran === 'admin_utama') {
            // CASE 1: GUBERNUR / ADMIN
            // Tampilkan dari Akar (Visi) -> Misi -> Sasaran Daerah -> Semua OPD
            // Kita ambil yang parent_id nya NULL (Visi)
            $pohons = PohonKinerja::whereNull('parent_id')
                        ->with('children.children.children.children') // Eager Load berlevel-level biar ringan
                        ->get();
            
            $viewTitle = 'Pohon Kinerja Pemerintah Provinsi (Full View)';

        } else {
            // CASE 2: KEPALA DINAS / STAF
            // Hanya tampilkan node yang opd_id nya sama dengan user
            // Biasanya dimulai dari 'Sasaran OPD' atau 'Program' yang dimiliki OPD tersebut
            // Kita cari node milik OPD ini yang induknya BUKAN milik OPD ini (Top Level-nya OPD)
            
            $pohons = PohonKinerja::where('opd_id', $user->id_perangkat_daerah)
                        ->whereHas('parent', function($q) use ($user) {
                            // Cari yang bapaknya BUKAN OPD ini (misal bapaknya Sasaran Daerah/Pemprov)
                            $q->where('opd_id', '!=', $user->id_perangkat_daerah)
                              ->orWhereNull('opd_id');
                        })
                        ->with('children.children.children')
                        ->get();

            // Fallback: Jika query di atas kosong (mungkin datanya belum link ke pemprov), 
            // ambil saja semua root yang punya opd_id ini
            if ($pohons->isEmpty()) {
                 $pohons = PohonKinerja::where('opd_id', $user->id_perangkat_daerah)
                            ->where(function($q) {
                                // Ambil yang jenisnya Sasaran OPD atau Program (level atas dinas)
                                $q->where('jenis_kinerja', 'sasaran_opd')
                                  ->orWhere('jenis_kinerja', 'program');
                            })
                            ->with('children.children')
                            ->get();
            }

            $viewTitle = 'Pohon Kinerja Perangkat Daerah';
        }

        return view('kinerja.pohon.index', compact('pohons', 'viewTitle'));
    }
}