<?php
namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PerangkatDaerah extends Model
{
    protected $connection = 'sistem_admin';
    protected $table = 'perangkat_daerah';

    protected $fillable = ['kode_pd', 'nama_pd', 'singkatan'];

    // Relasi: Satu Dinas memiliki banyak Bidang
    public function bidang()
    {
        return $this->hasMany(Bidang::class, 'pd_id');
    }

    // Relasi: Satu Dinas memiliki banyak Pengguna
    public function users()
    {
        return $this->hasMany(User::class, 'pd_id');
    }
    public function accessSetting()
    {
        // Gunakan \App\Models\Kinerja\AccessSetting secara langsung 
        // agar tidak terjadi salah pencarian folder
        return $this->hasOne(\App\Models\Kinerja\AccessSetting::class, 'pd_id', 'id');
    }
}