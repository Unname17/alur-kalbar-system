<?php
namespace App\Models\Rka;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kak\Kak;

class RkaDetail extends Model
{
    protected $connection = 'modul_anggaran';
    protected $table = 'kak_details';
    protected $fillable = ['kak_id', 'ssh_id', 'nama_barang', 'volume', 'satuan', 'harga_satuan', 'total_harga', 'is_manual','is_verified'];

    /**
     * Relasi ke Tabel KAK (Parent)
     * Setiap detail belanja pasti milik satu KAK
     */

    /**
     * Tambahan: Relasi ke SSH (Opsional, barangkali nanti butuh)
     */
    public function ssh()
    {
        return $this->belongsTo(MasterSsh::class, 'ssh_id');
    }
    public function rka()
    {
        return $this->belongsTo(Rka::class, 'rka_id');
    }
}