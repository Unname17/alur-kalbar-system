<?php

namespace App\Models\Kinerja;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IndikatorKinerja extends Model
{
    protected $connection = 'modul_kinerja';
    protected $table = 'indikator_kinerja';
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::updating(function ($model) {
            // Mencatat perubahan indikator ke tabel history
            DB::connection('modul_kinerja')->table('history_kinerja')->insert([
                'pohon_kinerja_id' => $model->pohon_kinerja_id,
                'data_lama' => json_encode(['indikator' => $model->getOriginal('indikator'), 'target' => $model->getOriginal('target')]),
                'data_baru' => json_encode(['indikator' => $model->indikator, 'target' => $model->target]),
                'user_id' => Auth::id() ?? 1,
                'created_at' => now(),
            ]);
        });
    }
}