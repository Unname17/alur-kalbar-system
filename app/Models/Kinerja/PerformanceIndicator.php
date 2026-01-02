<?php

namespace App\Models\Kinerja;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceIndicator extends Model
{
    // Mengarahkan ke database modul_kinerja
    protected $connection = 'modul_kinerja';

    protected $table = 'performance_indicators';

    protected $fillable = [
        'sub_activity_id',
        'nama_indikator',
        'satuan',
        'klasifikasi',
        'baseline_2024',
        'target_2025',
    ];

    /**
     * Relasi balik ke Sub Kegiatan
     */
    public function subActivity(): BelongsTo
    {
        return $this->belongsTo(SubActivity::class, 'sub_activity_id');
    }
}