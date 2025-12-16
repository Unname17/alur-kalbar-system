<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // <-- WAJIB DITAMBAHKAN INI
use App\Models\Admin\Pengguna;

class AuthController extends Controller
{
    /**
     * Handle user login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nip' => 'required|string',
            'kata_sandi' => 'required|string',
        ]);

        // Catatan: Anda harus menyesuaikan guard dan autentikasi Laravel agar 
        // menggunakan tabel 'pengguna' di koneksi 'sistem_admin'.
        // Jika Anda menggunakan Sanctum/Passport, logika akan sedikit berbeda.

        // Logika autentikasi sederhana (Bukan untuk produksi tanpa Sanctum/Passport)
        $user = Pengguna::where('nip', $credentials['nip'])->first();

        if ($user && hash::check($credentials['kata_sandi'], $user->kata_sandi)) {
            // Jika berhasil, buat token (gunakan Laravel Sanctum/Passport)
            $token = $user->createToken('authToken')->plainTextToken;
            return response()->json([
                'message' => 'Login berhasil.',
                'user' => $user,
                'token' => $token
            ]);
        }
        
        return response()->json(['message' => 'NIP atau Kata Sandi salah.'], 401);
    }

    /**
     * Handle user logout request (Requires Sanctum/Passport token revocation).
     */
    public function logout(Request $request)
    {
        // $request->user()->currentAccessToken()->delete(); // Hanya jika pakai Sanctum
        return response()->json(['message' => 'Logout berhasil.']);
    }
}