<?php

namespace App\Models\Kinerja;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailSubKegiatan extends Model
{
    use HasFactory;

    protected $connection = 'modul_kinerja';
    protected $table = 'detail_sub_kegiatan';
    protected $guarded = [];

    public function pohon()
    {
        return $this->belongsTo(PohonKinerja::class, 'pohon_id');
    }
}