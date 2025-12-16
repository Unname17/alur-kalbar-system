<?php

namespace App\Models\Kak;

use Illuminate\Database\Eloquent\Model;

class KakTimPelaksana extends Model
{
    protected $connection = 'modul_kak';
    protected $table = 'kak_tim_pelaksana';
    protected $guarded = [];

    public function kak()
    {
        return $this->belongsTo(Kak::class, 'kak_id');
    }
}