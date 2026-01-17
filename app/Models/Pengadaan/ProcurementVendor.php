<?php

namespace App\Models\Pengadaan;

use Illuminate\Database\Eloquent\Model;

class ProcurementVendor extends Model
{
    protected $connection = 'modul_pengadaan';
    protected $table = 'procurement_vendors';
    protected $guarded = ['id'];
}