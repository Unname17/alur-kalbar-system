<?php

namespace App\Models\Rka;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kak\Kak;

class Rka extends Model
{
    // Menggunakan koneksi modul_anggaran (Database: alur_kalbar_anggaran)
    protected $connection = 'modul_anggaran';
    protected $table = 'rka_main'; // Tabel utama penampung pagu RKA
    protected $guarded = [];

    /**
     * Relasi ke KAK (Cross-Database)
     */
    public function details() {
        // Relasi ke tabel kak_details yang Anda buat
        return $this->hasMany(RkaDetail::class, 'rka_id');
    }


}