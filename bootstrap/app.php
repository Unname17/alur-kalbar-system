<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SetDatabaseConnection; 

return Application::configure(basePath: dirname(__DIR__))
    
    // 1. BLOK KONFIGURASI ROUTING
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    
    // 2. BLOK KONFIGURASI MIDDLEWARE
    ->withMiddleware(function (Middleware $middleware) {
        
        // A. IZINKAN PROXY (PENTING AGAR BISA DIAKSES TEMAN)
        // Mengizinkan semua proxy (VS Code Tunnel, Ngrok, dll) agar tidak dianggap "untrusted"
        $middleware->trustProxies(at: '*');

        // B. Daftarkan Middleware Alias
        $middleware->alias([
            'db.set' => SetDatabaseConnection::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'check.input' => \App\Http\Middleware\CheckInputStatus::class,
        ]);
    })
    
    // 3. BLOK KONFIGURASI EXCEPTION
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    
    ->create();