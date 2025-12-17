<?php

namespace App\Models\Kak;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KakTimeline extends Model
{
    use HasFactory;

    protected $connection = 'modul_kak';
    protected $table = 'kak_timelines';

    protected $fillable = [
        'kak_id',
        'nama_tahapan',
        'b1', 'b2', 'b3', 'b4', 'b5', 'b6',
        'b7', 'b8', 'b9', 'b10', 'b11', 'b12',
        'keterangan'
    ];

    public function kak()
    {
        return $this->belongsTo(Kak::class, 'kak_id');
    }
}