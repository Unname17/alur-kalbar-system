<?php

namespace App\Models\Kak;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kinerja\PohonKinerja;

class Kak extends Model
{
    protected $connection = 'modul_kak'; // Terkunci ke database KAK
    protected $table = 'kak';
    protected $guarded = [];

    // Relasi Cross-Database ke Modul Kinerja
    public function pohonKinerja()
    {
        // Laravel mendukung relasi antar database selama host & user-nya sama
        return $this->belongsTo(PohonKinerja::class, 'pohon_kinerja_id');
    }

    public function timPelaksana()
    {
        return $this->hasMany(KakTimPelaksana::class, 'kak_id');
    }
}