<?php

namespace App\Models\Kinerja;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Pengguna;

class HistoryKinerja extends Model
{
    protected $connection = 'modul_kinerja';
    protected $table = 'history_kinerja';
    protected $guarded = [];

    // Relasi ke User yang mengubah data
    public function user()
    {
        return $this->belongsTo(Pengguna::class, 'user_id');
    }
}