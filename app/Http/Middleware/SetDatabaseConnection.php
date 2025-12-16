<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class SetDatabaseConnection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $connection Nama koneksi database (e.g., 'sistem_admin', 'modul_kinerja')
     */
    public function handle(Request $request, Closure $next, $connection): Response
    {
        // 1. Validasi nama koneksi untuk keamanan dan pencegahan error
        if (!Config::has('database.connections.' . $connection)) {
            return response()->json([
                'message' => 'Koneksi database yang diminta tidak valid.'
            ], 500);
        }

        // 2. Menyetel koneksi yang diminta sebagai koneksi default untuk request ini
        Config::set('database.default', $connection);
        
        // 3. Lanjutkan ke request berikutnya (Controller)
        return $next($request);
    }
}