<?php

namespace App\Models\Kinerja;

use Illuminate\Database\Eloquent\Model;

class SasaranStrategis extends Model
{
    protected $connection = 'modul_kinerja';
    protected $table = 'sasaran_strategis';
    protected $fillable = [
        'goal_id', 'nama_sasaran', 'indikator_sasaran', 
        'satuan', 'baseline_2024', 'target_2025', 'target_2026', 
        'target_2027', 'target_2028', 'target_2029', 'target_2030'
    ];

    public function programs() {
        return $this->hasMany(Program::class, 'sasaran_id');
    }
    public function goal() {
    return $this->belongsTo(Goal::class, 'goal_id');
}
}