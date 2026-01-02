<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class SetDatabaseConnection
{
    public function handle(Request $request, Closure $next, $connection)
    {
        // 1. Set koneksi database secara dinamis
        Config::set('database.default', $connection);

        // 2. Jika ingin mencatat log, gunakan seperti ini (Argumen ke-2 harus array atau kosongkan)
        logger("Database sekarang menggunakan koneksi: " . $connection);

        // 3. WAJIB: Kembalikan $next($request) secara mandiri
        return $next($request);
    }
}