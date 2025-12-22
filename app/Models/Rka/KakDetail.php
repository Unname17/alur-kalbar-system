<?php

namespace App\Models\Rka;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kak\Kak; // Relasi ke Modul Perencanaan

class KakDetail extends Model
{
    // Menggunakan koneksi database anggaran
    protected $connection = 'modul_anggaran';

    // Menghubungkan ke tabel yang Anda buat di migration
    protected $table = 'kak_details';

    // Mengizinkan pengisian masal
    protected $guarded = [];

    /**
     * Relasi ke Header KAK (Modul Perencanaan)
     */
    public function kak()
    {
        return $this->belongsTo(Kak::class, 'kak_id');
    }

    /**
     * Relasi ke Master SSH (Katalog Barang)
     */
    public function ssh()
    {
        return $this->belongsTo(MasterSsh::class, 'ssh_id');
    }
}