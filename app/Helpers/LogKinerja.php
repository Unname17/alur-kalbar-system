<?php

namespace App\Helpers;

use App\Models\Kinerja\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LogKinerja
{
    /**
     * Catat aktivitas ke database
     * * @param string $aksi (Contoh: 'CREATE', 'ACCESS', 'DELETE')
     * @param string $deskripsi (Penjelasan detail)
     * @param mixed $model (Opsional: Objek data yang diubah)
     */
    public static function record($aksi, $deskripsi, $model = null)
    {
        $user = Auth::user();

        ActivityLog::create([
            'user_nip'      => $user ? $user->nip : 'SYSTEM',
            'user_nama'     => $user ? $user->nama_lengkap : 'System Automator',
            'pd_id'         => $user ? $user->pd_id : null,
            
            'aksi'          => strtoupper($aksi),
            'modul'         => self::detectModule(), // Deteksi otomatis berdasarkan URL
            'deskripsi'     => $deskripsi,
            
            'subject_type'  => $model ? get_class($model) : null,
            'subject_id'    => $model ? $model->id : null,
            
            'ip_address'    => Request::ip(),
            'user_agent'    => Request::header('User-Agent'),
        ]);
    }

    private static function detectModule()
    {
        $path = Request::path();
        if (str_contains($path, 'wizard')) return 'Wizard Cascading';
        if (str_contains($path, 'admin/access')) return 'Manajemen Akses';
        if (str_contains($path, 'pohon')) return 'Pohon Kinerja';
        if (str_contains($path, 'dashboard')) return 'Dashboard';
        return 'Modul Kinerja';
    }
}