<?php

namespace App\Models\Kak;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Rka\RkaMain;
use App\Models\Kinerja\SubActivity;

class KakMain extends Model
{
    use SoftDeletes;
    
    protected $connection = 'modul_kak';
    protected $table = 'kak_mains';

    protected $fillable = [
        'rka_main_id',
        'sub_activity_id',
        'latar_belakang',
        'dasar_hukum',
        'penerima_manfaat',
        'maksud',        // Fix: Maksud berdiri sendiri
        'tujuan',        // Fix: Tujuan berdiri sendiri
        'metode_pelaksanaan',
        'tahapan_pelaksanaan',
        'tempat_pelaksanaan',
        'jadwal_matriks',
        'nama_pa_kpa',
        'nip_pa_kpa',
        'jabatan_pa_kpa'
    ];

    // Auto-convert JSON ke Array PHP (Wajib agar AlpineJS bisa baca)
    protected $casts = [
        'dasar_hukum' => 'array',
        'tahapan_pelaksanaan' => 'array',
        'jadwal_matriks' => 'array',
        'tujuan' => 'array',
    ];

    public function rka()
    {
        return $this->belongsTo(RkaMain::class, 'rka_main_id', 'id');
    }

    public function subActivity()
    {
        return $this->belongsTo(SubActivity::class, 'sub_activity_id', 'id');
    }
}