<?php

namespace App\Models\Kinerja;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $connection = 'modul_kinerja';
    protected $table = 'goals';

    protected $fillable = [
        'mission_id', 'pd_id', 'nama_tujuan', 'indikator', 
        'satuan', 'baseline_2024', 'target_2025', 'target_2026', 
        'target_2027', 'target_2028', 'target_2029', 'target_2030'
    ];

    public function mission()
    {
        return $this->belongsTo(Mission::class, 'mission_id');
    }

    public function programs()
    {
        return $this->hasMany(Program::class, 'goal_id');
    }
    // ... di dalam class Goal
public function sasaranStrategis()
{
    return $this->hasMany(SasaranStrategis::class, 'goal_id');
}
}