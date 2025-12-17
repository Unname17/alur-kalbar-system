<?php
namespace App\Models\Rka;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kak\Kak;

class KakDetail extends Model
{
    protected $connection = 'modul_anggaran';
    protected $table = 'kak_details';
    protected $fillable = ['kak_id', 'ssh_id', 'nama_barang', 'volume', 'satuan', 'harga_satuan', 'total_harga', 'is_manual','is_verified'];

    /**
     * Relasi ke Tabel KAK (Parent)
     * Setiap detail belanja pasti milik satu KAK
     */
    public function kak()
    {
        // Parameter 2: 'kak_id' adalah nama kolom foreign key di tabel kak_details
        return $this->belongsTo(Kak::class, 'kak_id');
    }

    /**
     * Tambahan: Relasi ke SSH (Opsional, barangkali nanti butuh)
     */
    public function ssh()
    {
        return $this->belongsTo(MasterSsh::class, 'ssh_id');
    }
}