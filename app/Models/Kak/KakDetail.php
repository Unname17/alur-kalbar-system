<?php

namespace App\Models\Rka;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kak\Kak; 

class KakDetail extends Model
{
    // Arahkan ke koneksi modul_anggaran sesuai database alur_kalbar_anggaran
    protected $connection = 'modul_anggaran';
    
    // Nama tabel fisik di database
    protected $table = 'kak_details';

    protected $guarded = [];

    /**
     * Relasi ke Modul KAK (Perencanaan)
     */
    public function kak()
    {
        return $this->belongsTo(Kak::class, 'kak_id');
    }
}