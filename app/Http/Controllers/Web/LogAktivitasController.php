<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Kinerja\ActivityLog;
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    public function index()
    {
        // Hanya Bappeda/Admin yang boleh lihat
        // Ambil 50 log terakhir, urutkan terbaru
        $logs = ActivityLog::with(['user', 'perangkatDaerah'])
                    ->latest()
                    ->paginate(20);

        return view('kinerja.admin.log_aktivitas', compact('logs'));
    }
}