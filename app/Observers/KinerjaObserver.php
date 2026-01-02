<?php

namespace App\Observers;

use App\Models\Kinerja\PohonKinerja;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KinerjaObserver
{
    public function updated(PohonKinerja $node)
    {
        // Hanya catat jika ada perubahan pada status atau nama kinerja
        if ($node->isDirty()) {
            DB::connection('modul_kinerja')->table('history_kinerja')->insert([
                'pohon_kinerja_id' => $node->id,
                'data_lama' => json_encode($node->getOriginal()), // Snapshot sebelum
                'data_baru' => json_encode($node->getAttributes()), // Snapshot sesudah
                'user_id'   => Auth::id() ?? $node->created_by, // Pelaku aksi
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}