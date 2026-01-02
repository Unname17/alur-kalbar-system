<?php

namespace App\Models\Kinerja;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LogAktivitas extends Model
{
    protected $connection = 'modul_kinerja';
    protected $table = 'log_aktivitas';

    protected $fillable = [
        'user_id', 'opd_id', 'aktivitas', 'modul', 
        'deskripsi', 'ip_address', 'user_agent', 'payload'
    ];

    // app/Models/Kinerja/LogAktivitas.php

public function getStatusLabel()
{
    $map = [
        'AKSES_MODUL'  => ['text' => 'Mengakses Modul', 'color' => '#3a3b45'], // Abu-abu gelap/hitam
        'TAMBAH_DATA'  => ['text' => 'Menambah Data', 'color' => '#0b4d17'], 
        'UBAH_DATA'    => ['text' => 'Mengubah Data', 'color' => '#855600'], 
        'HAPUS_DATA'   => ['text' => 'Menghapus Data', 'color' => '#6d0e0e'], 
        'SETUJU_DATA'  => ['text' => 'Menyetujui Data', 'color' => '#082d50'], 
        'TOLAK_DATA'   => ['text' => 'Menolak Data', 'color' => '#212529'],   
    ];

    $res = $map[$this->aktivitas] ?? ['text' => $this->aktivitas, 'color' => '#495057'];
    
    return "<span class='badge' style='background-color: {$res['color']}; color: white; padding: 6px 14px; border-radius: 4px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase;'>{$res['text']}</span>";
}

    public function getNamaUser()
    {
        return DB::connection('sistem_admin')->table('pengguna')
                 ->where('id', $this->user_id)->value('nama_lengkap') ?? 'Unknown';
    }

    public function getNamaOpd()
    {
        return DB::connection('sistem_admin')->table('perangkat_daerah')
                 ->where('id', $this->opd_id)->value('nama_perangkat_daerah') ?? 'Global';
    }
}