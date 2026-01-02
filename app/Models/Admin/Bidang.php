<?php

namespace App\Models\Admin;
use Illuminate\Database\Eloquent\Model;

class Bidang extends Model
{
    protected $connection = 'sistem_admin';
    protected $table = 'bidang';

    protected $fillable = ['pd_id', 'nama_bidang', 'kode_bidang'];

    // Relasi: Bidang dimiliki oleh satu Perangkat Daerah
    public function perangkatDaerah()
    {
        return $this->belongsTo(PerangkatDaerah::class, 'pd_id');
    }

    // Relasi: Satu Bidang memiliki banyak Staff/Kabid
    public function users()
    {
        return $this->hasMany(User::class, 'bidang_id');
    }
}