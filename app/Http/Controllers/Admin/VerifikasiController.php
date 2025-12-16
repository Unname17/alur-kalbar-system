<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VerifikasiController extends Controller
{
    /**
     * Menampilkan Halaman Tabel Verifikasi OPD
     */
    public function index()
    {
        // Ambil data OPD dari database 'sistem_admin'
        $list_opd = DB::connection('sistem_admin')
            ->table('perangkat_daerah')
            ->orderBy('nama_perangkat_daerah', 'asc')
            ->get();

        // Tampilkan View yang tadi kita buat (pastikan nama file bladenya sesuai)
        return view('admin.opd.verifikasi', [
            'viewTitle' => 'Verifikasi Perangkat Daerah',
            'list_opd' => $list_opd
        ]);
    }

    /**
     * Proses Saat Tombol "Setujui" Diklik
     */
    public function setujuiOpd($id)
    {
        // 1. Validasi Keamanan: Pastikan yang klik adalah Sekretariat
        if (Auth::user()->peran !== 'sekretariat') {
            return redirect()->back()->with('error', 'Akses Ditolak. Anda bukan Sekretariat.');
        }

        // 2. Update Database
        try {
            DB::connection('sistem_admin')
                ->table('perangkat_daerah')
                ->where('id', $id)
                ->update([
                    'status_verifikasi' => 'disetujui',
                    'diverifikasi_oleh' => Auth::id(),
                    'updated_at'        => now(),
                ]);

            return redirect()->back()->with('sukses', 'Data Perangkat Daerah berhasil disetujui!');
        
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}