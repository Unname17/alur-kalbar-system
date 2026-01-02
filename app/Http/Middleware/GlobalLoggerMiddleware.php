<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Kinerja\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class GlobalLoggerMiddleware
{
    // app/Http/Middleware/GlobalLoggerMiddleware.php

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Deteksi aksi perubahan (POST/PUT/DELETE)
        $isWriteAction = in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']);
        
        // PERBAIKAN: Gunakan 'kinerja.pohon*' (tanpa titik sebelum bintang) agar rute utama terdeteksi
        $isModuleAccess = $request->method() === 'GET' && (
            $request->routeIs('kinerja.pohon*') || 
            $request->routeIs('kinerja.akses*') || 
            $request->routeIs('kinerja.log*') ||
            $request->routeIs('kinerja.inbox*')
        );

        if (Auth::check() && ($isWriteAction || $isModuleAccess)) {
            // Jangan catat jika akses lewat AJAX (seperti select2/cascading)
            if ($request->ajax()) return $response;

            $routeName = $request->route() ? $request->route()->getName() : 'unknown';
            
            LogAktivitas::create([
                'user_id'    => Auth::id(),
                'opd_id'     => Auth::user()->id_perangkat_daerah,
                'aktivitas'  => $this->translateAction($request, $routeName),
                'modul'      => $this->getModulName($request),
                'deskripsi'  => $this->generateDescription($request, $routeName),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'payload'    => $isWriteAction ? json_encode($request->except(['_token', '_method', 'password'])) : null,
            ]);
        }

        return $response;
    }

    private function translateAction($request, $routeName) {
        if ($request->method() === 'GET') return 'AKSES_MODUL'; // Label baru untuk melihat data

        $actionInput = $request->input('action'); 
        if ($actionInput === 'tolak') return 'TOLAK_DATA';
        if ($actionInput === 'setuju' || $actionInput === 'approve') return 'SETUJU_DATA';

        if (str_contains($routeName, '.store')) return 'TAMBAH_DATA';
        if (str_contains($routeName, '.update')) return 'UBAH_DATA';
        if (str_contains($routeName, '.delete')) return 'HAPUS_DATA';
        
        return $request->method() . '_DATA';
    }

    private function generateDescription($request, $routeName) {
        if ($request->method() === 'GET') {
            return "Membuka dan melihat data pada modul " . $this->getModulName($request);
        }

        if ($request->input('action') === 'tolak') {
            return "Menolak pengajuan data. Catatan: " . ($request->input('catatan') ?? 'Tidak ada');
        }
        
        $map = [
            'kinerja.pohon.store'  => 'Membuat entri baru pada Pohon Kinerja',
            'kinerja.akses.store'  => 'Membuka kunci akses input OPD',
        ];

        return $map[$routeName] ?? "Melakukan perubahan data pada " . $this->getModulName($request);
    }

    private function getModulName($request) {
        $path = $request->path();
        if (str_contains($path, 'pohon')) return 'POHON_KINERJA';
        if (str_contains($path, 'akses')) return 'MANAJEMEN_AKSES';
        if (str_contains($path, 'inbox') || str_contains($path, 'validasi')) return 'INBOX_PENGAJUAN';
        return 'ADMIN_KINERJA';
    }
}