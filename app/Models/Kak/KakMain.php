<?php

namespace App\Models\Kak;

use Illuminate\Database\Eloquent\Model;
use App\Models\Rka\RkaMain;
use App\Models\Kinerja\SubActivity;

class KakMain extends Model
{
    // Definisikan koneksi khusus
    protected $connection = 'modul_kak';
    protected $table = 'kak_mains';

    protected $fillable = [
        'rka_main_id',
        'sub_activity_id',
        'latar_belakang',
        'dasar_hukum',
        'penerima_manfaat',
        'maksud_tujuan',
        'metode_pelaksanaan',
        'tahapan_pelaksanaan',
        'tempat_pelaksanaan',
        'jadwal_matriks',
        'nama_pa_kpa',
        'nip_pa_kpa',
        'jabatan_pa_kpa'
    ];

    // Auto-convert JSON ke Array PHP
    protected $casts = [
        'dasar_hukum' => 'array',
        'tahapan_pelaksanaan' => 'array',
        'jadwal_matriks' => 'array',
        'tujuan' => 'array',
    ];

    /**
     * RELASI LINTAS DATABASE 1: Ke Modul Anggaran (RKA)
     */
    public function rka()
    {
        // Tentukan foreign key dan owner key secara eksplisit
        return $this->belongsTo(RkaMain::class, 'rka_main_id', 'id');
    }

    /**
     * RELASI LINTAS DATABASE 2: Ke Modul Kinerja (Untuk ambil Indikator)
     */
    public function subActivity()
    {
        return $this->belongsTo(SubActivity::class, 'sub_activity_id', 'id');
    }
}