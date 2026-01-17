<?php

namespace App\Models\Pengadaan;

use Illuminate\Database\Eloquent\Model;
use App\Models\Rka\RkaDetail; // Pastikan import model RkaDetail

class ProcurementItem extends Model
{
    protected $connection = 'modul_pengadaan';
    protected $table = 'procurement_items';
    protected $guarded = ['id'];

    /**
     * Relasi ke Rincian Belanja di Modul Anggaran
     */
    public function rkaDetail()
    {
        // Menghubungkan kolom rka_detail_id ke Model RkaDetail
        return $this->belongsTo(RkaDetail::class, 'rka_detail_id');
    }

    public function package()
    {
        return $this->belongsTo(ProcurementPackage::class, 'package_id');
    }
}