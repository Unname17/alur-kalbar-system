<?php

namespace App\Models\Pengadaan;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    // Menggunakan koneksi database anggaran
    protected $connection = 'modul_pengadaan';
    protected $table = 'vendors';
    protected $guarded = [];

    /**
     * Relasi: Satu vendor bisa menangani banyak paket pengadaan.
     */
    public function pengadaans()
    {
        return $this->hasMany(Pengadaan::class, 'vendor_id');
    }
}