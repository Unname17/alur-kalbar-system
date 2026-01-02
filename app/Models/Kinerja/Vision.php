<?php

namespace App\Models\Kinerja;

use Illuminate\Database\Eloquent\Model;

class Vision extends Model
{
    protected $connection = 'modul_kinerja';
    protected $table = 'visions';

    protected $fillable = ['tahun_awal', 'tahun_akhir', 'visi_text', 'is_active'];

    public function missions()
    {
        return $this->hasMany(Mission::class, 'vision_id');
    }
}