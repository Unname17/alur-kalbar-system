<?php

namespace App\Models\Pengadaan;

use Illuminate\Database\Eloquent\Model;

class ProcurementPriceReference extends Model
{
    // Hubungkan ke koneksi database modul_pengadaan
    protected $connection = 'modul_pengadaan';

    protected $fillable = [
        'package_id', 'type', 'merek_model', 'sumber_nama', 
        'link_url', 'harga_satuan', 'kelebihan', 'kekurangan', 
        'garansi_layanan', 'nomor_tanggal_dok', 'tahun_anggaran', 'tanggal_akses'
    ];

    public function package()
    {
        return $this->belongsTo(ProcurementPackage::class, 'package_id');
    }
}