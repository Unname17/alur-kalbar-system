<?php

// app/Models/Pengadaan/PengadaanDocument.php
namespace App\Models\Pengadaan;

use Illuminate\Database\Eloquent\Model;

class PengadaanDocument extends Model
{
    protected $connection = 'modul_pengadaan';
    protected $guarded = [];

    public function pengadaan()
    {
        return $this->belongsTo(Pengadaan::class, 'pengadaan_id');
    }
}