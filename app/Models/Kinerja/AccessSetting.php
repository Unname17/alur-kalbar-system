<?php

namespace App\Models\Kinerja;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessSetting extends Model
{
    use HasFactory;

    protected $connection = 'modul_kinerja';
    protected $table = 'pengaturan_akses_modul';

    // Sesuai persis dengan kolom tabel Anda
    protected $fillable = [
        'pd_id',
        'user_nip',
        'parent_id',
        'level_izin',
        'waktu_buka',
        'waktu_tutup',
        'is_locked',
        'pesan_blokir',
        'updated_by_nip' // <--- Ini wajib ada
    ];

    // --- TAMBAHKAN BAGIAN INI ---
    protected $casts = [
        'waktu_buka' => 'datetime',
        'waktu_tutup' => 'datetime',
        'is_locked' => 'boolean',
    ];
    // ----------------------------

    // Relasi Perangkat Daerah
    public function perangkatDaerah()
    {
        return $this->belongsTo(\App\Models\Admin\PerangkatDaerah::class, 'pd_id');
    }

    // Relasi User (Admin/Staff)
    public function user()
    {
        return $this->belongsTo(\App\Models\Admin\User::class, 'user_nip', 'nip');
    }

    // Relasi ke Goal (Akar Pohon)
    public function goal()
    {
        return $this->belongsTo(Goal::class, 'parent_id');
    }
}