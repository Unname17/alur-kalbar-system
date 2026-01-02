<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// Import Model Kunci Akses dari database modul_kinerja
use App\Models\Kinerja\AccessSetting;

class WebPortalController extends Controller
{
public function index()
{
    $user = Auth::user();
    
    // Ambil status kunci untuk OPD user tersebut
    $lockData = AccessSetting::where('pd_id', $user->pd_id)->first();
    $is_locked = $lockData ? (bool) $lockData->is_locked : false;

    $apps = [
        [
            'title' => 'Pohon Kinerja',
            'desc' => 'Visualisasi Cascading Visi-Misi ke Sub-Kegiatan & Target Kinerja.',
            'icon' => 'bi-diagram-3-fill',
            'url' => route('kinerja.index'), // Semua role masuk ke sini
            'is_locked' => $is_locked,
        ],
        [
            'title' => 'E-Budgeting (RKA)',
            'desc' => 'Penyusunan Rincian Anggaran Belanja berdasarkan output kerja.',
            'icon' => 'bi-cash-stack',
            'url' => '#', 
        ],
        [
            'title' => 'E-KAK',
            'desc' => 'Kerangka Acuan Kerja, Tahapan, dan Spesifikasi Teknis Pekerjaan.',
            'icon' => 'bi-file-earmark-medical-fill',
            'url' => '#',
        ],
        [
            'title' => 'Pengadaan Barang/Jasa',
            'desc' => 'Manajemen paket pekerjaan dan metode pengadaan pemerintah.',
            'icon' => 'bi-truck',
            'url' => '#',
        ],
    ];

    return view('portal.index', compact('apps', 'is_locked'));
}
}