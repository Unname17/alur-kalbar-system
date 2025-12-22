<?php

namespace App\Models\Kak; // Namespace harus menyertakan nama folder

use Illuminate\Database\Eloquent\Model;
use App\Models\Kinerja\PohonKinerja;
use App\Models\Rka\KakDetail;


class Kak extends Model
{
    protected $connection = 'modul_kak';
    protected $table = 'kak';
    protected $guarded = [];

    public function pohonKinerja()
    {
        return $this->belongsTo(PohonKinerja::class, 'pohon_kinerja_id');
    }
    public function timPelaksana()
    {
        return $this->hasMany(KakTimPelaksana::class, 'kak_id');
    }

    public function rincianBelanja()
    {
        return $this->hasMany(KakDetail::class, 'kak_id');
    }

    public function timelines() {
        return $this->hasMany(KakTimeline::class, 'kak_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

}