<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\Pengguna; // Model Pengguna

class AdminWebController extends Controller
{
    // 1. Menampilkan Form Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // 2. Aksi Login
    public function loginAction(Request $request)
    {
        $credentials = $request->validate([
            'nip' => 'required|string',
            // Gunakan 'password' di sini karena Auth::attempt mencari kolom password secara default, 
            // dan kita akan mengandalkan getAuthPassword() di Model Pengguna jika kolomnya bernama 'kata_sandi'.
            'kata_sandi' => 'required|string', 
        ]);

        // Karena kita sudah mengkonfigurasi config/auth.php:
        // 1. Auth::attempt otomatis menggunakan 'nip' sebagai username (karena kita menyediakannya)
        // 2. Auth::attempt otomatis menggunakan provider 'pengguna_provider' (yang terhubung ke sistem_admin)
        
        // PENTING: Auth::attempt harus menerima kunci 'password', meskipun input form Anda bernama 'kata_sandi'.
        // Kita petakan input 'kata_sandi' ke kunci 'password' di array credentials:
        $loginData = [
            'nip' => $credentials['nip'],
            'password' => $credentials['kata_sandi'],
        ];

        // Coba autentikasi
        if (Auth::attempt($loginData)) {
            $request->session()->regenerate();
            
            // REDIRECT KE HALAMAN PORTAL
            return redirect()->intended(route('dashboard')); 
        }

        // Jika gagal
        return back()->withErrors([
            'nip' => 'Kredensial (NIP/Password) tidak valid.',
        ])->onlyInput('nip');
    }
    
    // Anda perlu menambahkan kembali method logout dan method Web lainnya di sini
    public function logoutAction(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
    
    // ... showOpdList, showPenggunaList, showPohonKinerja, dll. ...
}