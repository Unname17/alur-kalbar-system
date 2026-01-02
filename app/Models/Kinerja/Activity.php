<?php

namespace App\Models\Kinerja;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $connection = 'modul_kinerja';
    protected $table = 'activities';
    protected $fillable = [
        'program_id', 'nama_kegiatan', 'sasaran_kegiatan', 'indikator_kegiatan', 
        'satuan', 'baseline_2024', 'target_2025', 'target_2026', 
        'target_2027', 'target_2028', 'target_2029', 'target_2030'
    ];

    public function subActivities() {
        return $this->hasMany(SubActivity::class, 'activity_id');
    }
    public function program() {
    return $this->belongsTo(Program::class, 'program_id');
}
}