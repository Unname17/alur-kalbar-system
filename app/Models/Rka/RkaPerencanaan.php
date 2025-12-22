<?php

namespace App\Models\Rka;

use Illuminate\Database\Eloquent\Model;

// app/Models/Rka/RkaPerencanaan.php

namespace App\Models\Rka;

use Illuminate\Database\Eloquent\Model;

class RkaPerencanaan extends Model
{
    protected $connection = 'modul_anggaran';
    protected $table = 'rka_perencanaan';
    protected $guarded = [];

    // Tambahkan relasi ini agar Blade baris 150 bisa membacanya
    public function rincianBelanja()
    {
        // Menghubungkan ke tabel kak_details di database modul_anggaran
        return $this->hasMany(KakDetail::class, 'kak_id', 'kak_id');
    }
}