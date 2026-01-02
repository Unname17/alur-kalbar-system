<?php

namespace App\Models\Kinerja;

use Illuminate\Database\Eloquent\Model;

class SubActivity extends Model
{
    protected $connection = 'modul_kinerja';
    protected $table = 'sub_activities';
    protected $fillable = [
            'activity_id', 'kode_sub', 'nama_sub', 'indikator_sub', 'satuan', 
            'target_2025', 'target_2026', 'target_2027', 'target_2028', 'target_2029', 'target_2030',
            'tipe_perhitungan', 'klasifikasi', 
            'status',           // <--- Harus ada
            'catatan_revisi',   // <--- Harus ada
            'nip_verifier', 'nip_validator', 'nip_approver', 'created_by_nip'
        ];

    public function activity() 
{
    // SubActivity belongs to an Activity
    return $this->belongsTo(Activity::class, 'activity_id');
}
}