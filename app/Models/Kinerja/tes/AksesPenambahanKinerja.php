<?php

namespace App\Models\Kinerja;

use Illuminate\Database\Eloquent\Model;

class AksesPenambahanKinerja extends Model
{
    protected $connection = 'modul_kinerja';
    protected $table = 'akses_penambahan_kinerja';
    protected $guarded = ['id'];

    public function parentNode()
    {
        return $this->belongsTo(PohonKinerja::class, 'parent_id_allowed', 'id');
    }
}