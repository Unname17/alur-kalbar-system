<?php

namespace App\Models\Kinerja;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $connection = 'modul_kinerja';
    protected $table = 'programs';
    protected $fillable = [
        'sasaran_id', 'nama_program', 'sasaran_program', 'indikator_program', 
        'satuan', 'baseline_2024', 'target_2025', 'target_2026', 
        'target_2027', 'target_2028', 'target_2029', 'target_2030'
    ];

    public function activities() {
        return $this->hasMany(Activity::class, 'program_id');
    }
    public function sasaranStrategis() {
    return $this->belongsTo(SasaranStrategis::class, 'sasaran_id');
}

public function getTotalAnggaranAttribute()
    {
        return $this->activities->sum('total_anggaran');
    }
}