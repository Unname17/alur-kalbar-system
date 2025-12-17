<?php

namespace App\Models\Kak;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kinerja\PohonKinerja;

class Kak extends Model
{
    protected $connection = 'modul_kak'; // Terkunci ke database KAK
    protected $table = 'kak';
    protected $guarded = [];
    protected $fillable = [
    'pohon_kinerja_id', 'judul_kak', 'kode_proyek', 'dasar_hukum', 
    'latar_belakang', 'maksud_tujuan', 'sasaran', 'metode_pelaksanaan', 
    'lokasi', 'penerima_manfaat', 'waktu_mulai', 'waktu_selesai',
    'status', 'catatan_sekretariat','nomor_kak'
];

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
    public function timelines() {
        return $this->hasMany(KakTimeline::class, 'kak_id');
    }
}