<?php

namespace App\Models\Kinerja;

use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{
    protected $connection = 'modul_kinerja';
    protected $table = 'missions';

    protected $fillable = ['vision_id', 'nomor_misi', 'misi_text'];

    public function vision()
    {
        return $this->belongsTo(Vision::class, 'vision_id');
    }

    public function goals()
    {
        return $this->hasMany(Goal::class, 'mission_id');
    }
}