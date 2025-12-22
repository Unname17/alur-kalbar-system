<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VerifikasiController extends Controller
{
    // Fungsi untuk Buka/Tutup Akses Input OPD secara manual [cite: 66, 67]
    public function toggleAksesInput(Request $request, $id)
    {
        if (!in_array(Auth::user()->peran, ['admin_utama', 'sekretariat'])) {
            return redirect()->back()->with('error', 'Hanya Bappeda yang bisa mengontrol akses.');
        }

        DB::connection('sistem_admin')->table('perangkat_daerah')
            ->where('id', $id)
            ->update([
                'status_input' => $request->status, // 'buka' atau 'tutup'
                'updated_at'   => now(),
            ]);

        return redirect()->back()->with('sukses', 'Status akses OPD berhasil diubah!');
    }
}