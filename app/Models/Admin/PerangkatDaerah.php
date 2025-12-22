<?php
namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerangkatDaerah extends Model
{
    protected $connection = 'sistem_admin'; 
    protected $table = 'perangkat_daerah'; 
    protected $guarded = [];

    /**
     * Relasi: Satu Perangkat Daerah memiliki banyak Pengguna
     */
    public function penggunas(): HasMany
    {
        return $this->hasMany(Pengguna::class, 'id_perangkat_daerah');
    }

    /**
     * Helper: Mengecek apakah OPD ini sedang diizinkan menginput data
     * Sesuai arahan validator Bappeda di transkripsi
     */
    public function isInputOpen(): bool
    {
        return $this->status_input === 'buka';
    }
}