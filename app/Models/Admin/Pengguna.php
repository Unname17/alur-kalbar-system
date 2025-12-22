<?php
namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Laravel\Sanctum\HasApiTokens;

class Pengguna extends Authenticatable 
{
    use HasFactory, HasApiTokens;

    protected $connection = 'sistem_admin'; 
    protected $table = 'pengguna';
    protected $guarded = [];

    // Jika kolom password di DB adalah 'kata_sandi'
    public function getAuthPassword()
    {
        return $this->kata_sandi;
    }

    /**
     * Relasi balik ke Perangkat Daerah
     */
    public function perangkatDaerah(): BelongsTo
    {
        return $this->belongsTo(PerangkatDaerah::class, 'id_perangkat_daerah');
    }

    /**
     * Helper: Mengecek apakah user adalah Validator (Bappeda/Sekretariat)
     * Sesuai peran validator di transkripsi
     */
    public function isValidator(): bool
    {
        return in_array($this->peran, ['admin_utama', 'sekretariat']);
    }
}