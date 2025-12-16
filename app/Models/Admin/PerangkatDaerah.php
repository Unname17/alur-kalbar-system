<?php
namespace App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengguna extends Model
{
    protected $connection = 'sistem_admin'; 
    protected $table = 'pengguna'; 
    protected $guarded = [];

    // Relasi: Pengguna (banyak) dimiliki oleh satu Perangkat Daerah
    public function perangkatDaerah(): BelongsTo
    {
        return $this->belongsTo(PerangkatDaerah::class, 'id_perangkat_daerah');
    }
}