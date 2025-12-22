<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebPortalController extends Controller
{
    public function index()
    {
        // 1. Ambil data user yang sedang login
        $user = Auth::user();

        // 2. Siapkan daftar Menu UMUM (Semua orang bisa lihat ini)
        // Mulai dari Pohon Kinerja ke bawah
        $apps = [
            [
                'title' => 'Pohon Kinerja E-Performance',
                'desc' => 'Perencanaan strategis: Visi, Misi, Tujuan, Sasaran, Program, dan Indikator.',
                'icon' => 'bi-bar-chart-line-fill',
                'url' => route('kinerja.pohon'),
            ],
            [
                'title' => 'Kerangka Acuan Kerja (KAK)',
                'desc' => 'Manajemen Kerangka Acuan Kerja, Tahapan, dan Sumber Daya.',
                'icon' => 'bi-journal-richtext',
                'url' => route('kak.index'), 
            ],
            [
                'title' => 'Anggaran (RKA & SSH)',
                'desc' => 'Input RKA, standar harga, dan pagu alokasi anggaran.',
                'icon' => 'bi-currency-dollar',
                'url' => route('rka.pilih_kak'), 
            ],
            [
                'title' => 'Pengadaan Barang/Jasa',
                'desc' => 'Manajemen paket pekerjaan, metode pengadaan, dan kontrak.',
                'icon' => 'bi-truck',
                'url' => route('pengadaan.index'),
            ],
        ];

        // 3. LOGIKA KHUSUS ADMIN UTAMA
        // Cek apakah user ada DAN perannya adalah 'admin_utama'
        if ($user && $user->peran === 'admin_utama') {
            
            // Siapkan menu khusus Admin
            $adminMenu = [
                'title' => 'Administrasi & Pengguna',
                'desc' => 'Kelola Perangkat Daerah, User Login, dan Hak Akses Sistem.',
                'icon' => 'bi-people-fill',
                'url' => route('admin.opd'),
            ];

            // Masukkan menu Admin ke urutan PERTAMA (paling atas)
            array_unshift($apps, $adminMenu);
        }

        // 4. Kirim ke View
        return view('portal.index', compact('apps'));
    }
}