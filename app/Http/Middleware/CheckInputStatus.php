<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckInputStatus
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Admin Utama & Sekretariat bebas kunci
        if (in_array($user->peran, ['admin_utama', 'sekretariat'])) {
            return $next($request);
        }

        $opd = DB::connection('sistem_admin')->table('perangkat_daerah')
                 ->where('id', $user->id_perangkat_daerah)->first();

        // Jika status tutup, tolak permintaan edit/simpan
        if ($opd && $opd->status_input === 'tutup' && !$request->isMethod('get')) {
            return response()->json(['message' => 'Penginputan dikunci oleh Bappeda.'], 403);
        }

        return $next($request);
    }
}