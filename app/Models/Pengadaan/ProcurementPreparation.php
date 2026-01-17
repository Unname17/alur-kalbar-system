<?php
namespace App\Models\Pengadaan;
use Illuminate\Database\Eloquent\Model;

class ProcurementPreparation extends Model {
    protected $connection = 'modul_pengadaan';
    protected $guarded = ['id'];
}