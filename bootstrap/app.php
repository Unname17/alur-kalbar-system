<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
// WAJIB: Import class middleware Anda di awal file
use App\Http\Middleware\SetDatabaseConnection; 

return Application::configure(basePath: dirname(__DIR__))
    
    // 1. BLOK KONFIGURASI ROUTING
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // Pastikan API juga didefinisikan
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    
    // 2. BLOK KONFIGURASI MIDDLEWARE (SATUKAN DI SINI)
    ->withMiddleware(function (Middleware $middleware) {
        
        // Daftarkan Middleware Route Alias Anda di sini:
        $middleware->alias([
            'db.set' => SetDatabaseConnection::class, // Ini adalah alias yang kita butuhkan
        ]);
        
        // Jika Anda ingin menambahkan middleware global, gunakan $middleware->web() atau $middleware->api() di sini
    })
    
    // 3. BLOK KONFIGURASI EXCEPTION
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    
    // 4. PEMBANGUNAN APLIKASI
    ->create();