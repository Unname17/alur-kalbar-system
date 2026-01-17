<?php

namespace App\Models\Pengadaan;

use Illuminate\Database\Eloquent\Model;

class ProcurementContract extends Model
{
    protected $connection = 'modul_pengadaan';
    protected $table = 'procurement_contracts';
    protected $guarded = ['id'];

    public function package()
    {
        return $this->belongsTo(ProcurementPackage::class, 'package_id');
    }
}