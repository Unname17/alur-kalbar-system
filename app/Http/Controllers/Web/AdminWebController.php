<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\User;
use App\Models\Admin\PerangkatDaerah;
use App\Models\Kinerja\AccessSetting;

class AdminWebController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showPortal()
{
    // Mengambil status kunci untuk OPD tempat user bekerja
    $user = auth()->user();
    
    // Status Kunci Akses dari DB Modul Kinerja
    $lockStatus = \App\Models\Kinerja\AccessSetting::where('pd_id', $user->pd_id)->first();
    $is_locked = $lockStatus ? $lockStatus->is_locked : false;

    return view('portal.index', compact('is_locked'));
}

    public function loginAction(Request $request)
    {
        $credentials = $request->validate([
            'nip' => 'required|string',
            'kata_sandi' => 'required|string', 
        ]);

        // Laravel Auth secara default mencari kolom 'password'
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
        // Menggunakan Model agar otomatis menggunakan koneksi 'sistem_admin'
        $opds = PerangkatDaerah::all();
        
        // Kita ambil status kunci dari database kinerja untuk ditampilkan di view
        foreach ($opds as $opd) {
            $lockStatus = AccessSetting::where('pd_id', $opd->id)->first();
            $opd->is_locked = $lockStatus ? $lockStatus->is_locked : false;
        }

        return view('admin.opd.index', compact('opds'));
    }

    // --- FITUR KUNCI/BUKA INPUT (TOGGLE STATUS) ---
    public function toggleStatusInput($id)
    {
        // 1. Cari atau buat pengaturan akses di database 'modul_kinerja'
        $access = AccessSetting::firstOrCreate(
            ['pd_id' => $id],
            [
                'is_locked' => false,
                'updated_by_nip' => auth()->user()->nip
            ]
        );

        // 2. Toggle status boolean
        $access->is_locked = !$access->is_locked;
        $access->updated_by_nip = auth()->user()->nip;
        $access->save();

        // 3. Pesan Notifikasi
        $pesan = ($access->is_locked) 
            ? 'Akses input OPD dikunci (Staf tidak bisa input).' 
            : 'Akses input OPD dibuka kembali.';

        return back()->with('success', $pesan);
    }

    public function showPenggunaList()
    {
        // Sesuaikan dengan nama tabel 'users' dan kolom 'pd_id'
        $users = User::with(['perangkatDaerah', 'role'])->get();
        
        return view('admin.pengguna.index', compact('users'));
    }
}