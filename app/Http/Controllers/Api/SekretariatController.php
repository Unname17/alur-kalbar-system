<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SekretariatController extends Controller
{
    /**
     * Menyetujui Node (Verifikasi)
     */
public function setujuiNode(Request $request, $id)
{
    // ... (Validasi user dan query database TETAP SAMA seperti sebelumnya) ...
    // LANGSUNG KE BAGIAN RETURN PALING BAWAH:

    try {
        DB::connection('sistem_admin')
          ->table('perangkat_daerah') // Pastikan nama tabel benar
          ->where('id', $id)
          ->update([
              'status_verifikasi' => 'disetujui',
              'diverifikasi_oleh' => $user->id,
              'updated_at'        => now(),
          ]);

        // PERUBAHAN DISINI:
        // Jika request datang dari Web (bukan API Client), lakukan Redirect
        return redirect()->back()->with('sukses', 'Data Perangkat Daerah berhasil disetujui!');

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal memverifikasi: ' . $e->getMessage());
    }
}
}