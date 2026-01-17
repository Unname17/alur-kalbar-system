<?php

namespace App\Models\Rka;

use Illuminate\Database\Eloquent\Model;

class RkaDetail extends Model
{
    // Gunakan koneksi anggaran karena rincian disimpan di database anggaran
    protected $connection = 'modul_anggaran';
    
    // Nama tabel sesuai database Anda
    protected $table = 'rka_details';

protected $fillable = [
    'rka_main_id',
    'rekening_id',
    'uraian_belanja',
    'spesifikasi',
    'koefisien',
    'satuan',
    'harga_satuan',
    'sub_total'
];

    /**
     * Relasi balik ke Header RKA (RkaMain)
     */
    public function rkaMain()
    {
        return $this->belongsTo(RkaMain::class, 'rka_main_id');
    }
    public function rekening()
{
    return $this->belongsTo(MasterRekening::class, 'rekening_id');
}
}