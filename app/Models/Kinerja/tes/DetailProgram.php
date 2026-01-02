<?php

namespace App\Models\Kinerja;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailProgram extends Model
{
    use HasFactory;

    // Sesuaikan dengan nama koneksi database kinerja kamu di .env
    // Jika default-nya mysql biasa, hapus baris ini.
    protected $connection = 'modul_kinerja'; 

    protected $table = 'detail_program';
    
    // Kita gunakan guarded kosong agar semua kolom bisa diisi (biar tidak ribet fillable)
    protected $guarded = [];

    public function pohon()
    {
        return $this->belongsTo(PohonKinerja::class, 'pohon_id');
    }
}