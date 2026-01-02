<?php
namespace App\Models\Admin;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $connection = 'sistem_admin';
    protected $table = 'users';

    protected $fillable = [
        'nip', 
        'nama_lengkap', 
        'password', 
        'role_id', 
        'pd_id', 
        'bidang_id', 
        'is_active'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // Beritahu Laravel untuk login menggunakan NIP, bukan email
    public function username()
    {
        return 'nip';
    }

    // RELASI
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function perangkatDaerah()
    {
        return $this->belongsTo(PerangkatDaerah::class, 'pd_id');
    }

    public function bidang()
    {
        return $this->belongsTo(Bidang::class, 'bidang_id');
    }
}