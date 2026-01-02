<?php

namespace App\Models\Kinerja;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $connection = 'modul_kinerja';
    protected $table = 'log_aktivitas';

    protected $fillable = [
        'user_nip', 'user_nama', 'pd_id',
        'aksi', 'modul', 'deskripsi',
        'subject_type', 'subject_id',
        'ip_address', 'user_agent'
    ];

    // Relasi ke User (Sistem Admin)
    public function user()
    {
        return $this->belongsTo(\App\Models\Admin\User::class, 'user_nip', 'nip');
    }

    // Relasi ke OPD
    public function perangkatDaerah()
    {
        return $this->belongsTo(\App\Models\Admin\PerangkatDaerah::class, 'pd_id');
    }
}