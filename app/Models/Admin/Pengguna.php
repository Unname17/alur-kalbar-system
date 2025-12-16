<?php
namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Laravel\Sanctum\HasApiTokens; // <-- WAJIB: Import Trait Sanctum

class Pengguna extends Authenticatable 
{
    use HasFactory, HasApiTokens; // <-- WAJIB: Gunakan Trait di sini

    protected $connection = 'sistem_admin'; 
    protected $table = 'pengguna';
    protected $guarded = [];

    // Jika kolom password di DB adalah 'kata_sandi'
    public function getAuthPassword()
    {
        return $this->kata_sandi;
    }
}