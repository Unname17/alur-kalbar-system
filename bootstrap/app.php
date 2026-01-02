<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SetDatabaseConnection; 
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Storage;

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
            'check.input' => \App\Http\Middleware\CheckInputAkses::class,
            // 'logger' => \App\Http\Middleware\GlobalLoggerMiddleware::class,
        ]);
    })
    
// 3. BLOK KONFIGURASI JADWAL (SCHEDULE)
    ->withSchedule(function (Schedule $schedule) {
        // Otomatis hapus file arsip Excel di server yang sudah berumur lebih dari 1 tahun
        $schedule->call(function () {
            $folder = 'archives/logs';
            $files = Storage::files($folder);
            
            foreach ($files as $file) {
                // Cek umur file (365 hari / 1 tahun)
                if (Storage::lastModified($file) < now()->subYear()->getTimestamp()) {
                    Storage::delete($file);
                }
            }
        })->daily(); // Dijalankan setiap hari sekali
    })
    
    // 4. BLOK KONFIGURASI EXCEPTION
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    
    ->create();