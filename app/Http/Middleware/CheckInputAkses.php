<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Kinerja\AccessSetting;

class CheckInputAkses
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        if (!$user) return $next($request);

        // PERBAIKAN: Mapping Langkah Wizard ke Level Database yang lebih spesifik
        $stepMap = [
            1 => 'Goal',              // Langkah 1: Tujuan PD
            2 => 'SasaranStrategis',  // Langkah 2: Sasaran Strategis
            3 => 'Program',           // Langkah 3: Program
            4 => 'Activity',          // Langkah 4: Kegiatan
            5 => 'SubActivity'        // Langkah 5: Sub-Kegiatan
        ];

        // Ambil step dari request
        $requestedStep = $request->input('step');
        $requestedLevel = $stepMap[$requestedStep] ?? null;

        // Query Aturan Akses
        $query = AccessSetting::where(function($q) use ($user) {
            $q->where('user_nip', $user->nip)
              ->orWhere(function($sq) use ($user) {
                  $sq->where('pd_id', $user->pd_id)
                     ->whereNull('user_nip');
              });
        });

        // Filter berdasarkan level yang sedang diakses
        if ($requestedLevel) {
            $query->where('level_izin', $requestedLevel);
        }

        $rule = $query->first();

        // LOGIKA BLOKIR
        
        // 1. Jika User mencoba SIMPAN data, tapi tidak punya izin untuk level tersebut -> BLOKIR
        if (!$rule && !$request->isMethod('get') && $requestedLevel) {
            return response()->json([
                'message' => "Akses input untuk level $requestedLevel belum dibuka oleh Bappeda."
            ], 403);
        }

        // 2. Jika Aturan ada, tapi statusnya Terkunci / Jadwal Tutup
        if ($rule) {
            $now = now();
            $isLockedManual = (bool)$rule->is_locked;
            $isTimeLocked = ($rule->waktu_buka && $rule->waktu_tutup) && 
                            ($now->lt($rule->waktu_buka) || $now->gt($rule->waktu_tutup));

            if ($isLockedManual || $isTimeLocked) {
                if (!$request->isMethod('get')) {
                    return response()->json([
                        'message' => $rule->pesan_blokir ?? "Akses $requestedLevel sedang ditutup saat ini."
                    ], 403);
                }
                view()->share('isLocked', true);
                view()->share('access', $rule);
            }
        }

        return $next($request);
    }
}