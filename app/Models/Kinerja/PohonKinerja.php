<?php

namespace App\Models\Kinerja;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Kak\Kak; // Import model Kak

class PohonKinerja extends Model
{
    use HasFactory;
    
    // Pastikan koneksi ke modul_kinerja
    protected $connection = 'modul_kinerja';
    protected $table = 'pohon_kinerja';
    protected $guarded = [];

    // TAMBAH KOLOM BARU DISINI
    protected $fillable = [
        'parent_id', 'opd_id', 'nama_kinerja', 'jenis_kinerja', 
        'status', 'catatan_penolakan', 'created_by' // <-- BARU
    ];

public function indikators()
{
    // Relasi One-to-Many
    return $this->hasMany(IndikatorKinerja::class, 'pohon_kinerja_id', 'id');
}

    // Relasi ke Anak (Children)
    public function children()
    {
        return $this->hasMany(PohonKinerja::class, 'parent_id')->with('children');
    }

    // Relasi ke Induk (Parent)
    public function parent()
    {
        return $this->belongsTo(PohonKinerja::class, 'parent_id');
    }

    // Relasi Detail (Detail Program/Kegiatan/SubKegiatan)
    public function detailProgram()
    {
        return $this->hasOne(DetailProgram::class, 'pohon_id');
    }
    public function detailKegiatan()
    {
        return $this->hasOne(DetailKegiatan::class, 'pohon_id');
    }
    public function detailSubKegiatan()
    {
        return $this->hasOne(DetailSubKegiatan::class, 'pohon_id');
    }
    public function kak()
    {
        // Hubungkan ke Model Kak yang berada di database/koneksi modul_kak
        return $this->hasOne(Kak::class, 'pohon_kinerja_id');
    }
}