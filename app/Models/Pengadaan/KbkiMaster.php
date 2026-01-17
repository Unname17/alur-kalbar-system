<?php

namespace App\Models\Pengadaan;

use Illuminate\Database\Eloquent\Model;

class KbkiMaster extends Model
{
    // Pastikan koneksi ke database pengadaan
    protected $connection = 'modul_pengadaan'; 
    protected $table = 'kbki_masters';
    
    protected $fillable = ['kode_kbki', 'deskripsi_kbki'];
}