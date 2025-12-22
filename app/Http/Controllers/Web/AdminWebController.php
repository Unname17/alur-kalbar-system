<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // <--- BARIS INI WAJIB ADA UNTUK MEMPERBAIKI ERROR
use Illuminate\Support\Facades\Hash;

class AdminWebController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function loginAction(Request $request)
    {
        $credentials = $request->validate([
            'nip' => 'required|string',
            'kata_sandi' => 'required|string', 
        ]);

        $loginData = [
            'nip' => $credentials['nip'],
            'password' => $credentials['kata_sandi'],
        ];

        if (Auth::attempt($loginData)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard')); 
        }

        return back()->withErrors([
            'nip' => 'Kredensial (NIP/Password) tidak valid.',
        ])->onlyInput('nip');
    }
    
    public function logoutAction(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // --- MANAJEMEN OPD ---
    public function showOpdList()
    {
        // Menggunakan koneksi sistem_admin sesuai arsitektur database Anda
        $opds = DB::connection('sistem_admin')->table('perangkat_daerah')->get();
        return view('admin.opd.index', compact('opds'));
    }

    // --- FITUR KUNCI/BUKA INPUT (TOGGLE STATUS) ---
    public function toggleStatusInput($id)
    {
        // 1. Cari data OPD di database admin
        $opd = DB::connection('sistem_admin')->table('perangkat_daerah')->where('id', $id)->first();

        if (!$opd) {
            return back()->with('error', 'Data OPD tidak ditemukan.');
        }

        // 2. Cek status sekarang dan balikkan (Toggle)
        $statusBaru = ($opd->status_input === 'buka') ? 'tutup' : 'buka';

        // 3. Update ke Database admin
        DB::connection('sistem_admin')->table('perangkat_daerah')
            ->where('id', $id)
            ->update([
                'status_input' => $statusBaru, 
                'updated_at' => now()
            ]);

        // 4. Pesan Notifikasi
        $pesan = ($statusBaru === 'tutup') 
            ? 'Akses input OPD dikunci (Staf tidak bisa input).' 
            : 'Akses input OPD dibuka kembali.';

        return back()->with('success', $pesan);
    }

    public function showPenggunaList()
    {
        $users = DB::connection('sistem_admin')->table('pengguna')
            ->join('perangkat_daerah', 'pengguna.id_perangkat_daerah', '=', 'perangkat_daerah.id')
            ->select('pengguna.*', 'perangkat_daerah.nama_perangkat_daerah')
            ->get();
        return view('admin.pengguna.index', compact('users'));
    }
}