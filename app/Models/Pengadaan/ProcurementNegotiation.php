<?php

namespace App\Models\Pengadaan;

use Illuminate\Database\Eloquent\Model;

class ProcurementNegotiation extends Model
{
    protected $connection = 'modul_pengadaan';
    protected $table = 'procurement_negotiations';
    protected $guarded = ['id'];

    public function package()
    {
        return $this->belongsTo(ProcurementPackage::class, 'package_id');
    }

    public function vendor()
    {
        return $this->belongsTo(ProcurementVendor::class, 'vendor_id');
    }
}