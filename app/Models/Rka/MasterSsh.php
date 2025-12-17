<?php
namespace App\Models\Rka;

use Illuminate\Database\Eloquent\Model;

class MasterSsh extends Model
{
    protected $connection = 'modul_anggaran'; // Blok 'modul_anggaran' di config
    protected $table = 'master_ssh';
    protected $fillable = ['kode_barang', 'nama_barang', 'satuan', 'harga_satuan', 'kategori'];
}