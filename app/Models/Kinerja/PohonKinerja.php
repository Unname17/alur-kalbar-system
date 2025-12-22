<?php

namespace App\Models\Kinerja;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Kak\Kak;

class PohonKinerja extends Model
{
    protected $connection = 'modul_kinerja';
    protected $table = 'pohon_kinerja';
    protected $guarded = [];

    protected static function booted()
    {
        static::updating(function ($model) {
            DB::connection('modul_kinerja')->table('history_kinerja')->insert([
                'pohon_kinerja_id' => $model->id,
                'data_lama' => json_encode($model->getOriginal()),
                'data_baru' => json_encode($model->getAttributes()),
                'user_id' => Auth::id() ?? 1,
                'created_at' => now(),
            ]);
        });
    }

    public function indikators() {
        return $this->hasMany(IndikatorKinerja::class, 'pohon_kinerja_id');
    }

    public function children() {
        return $this->hasMany(PohonKinerja::class, 'parent_id');
    }

    public function kak() {
        return $this->hasOne(Kak::class, 'pohon_kinerja_id', 'id');
    }
}