<?php

namespace App\Http\Controllers\Api\Perencanaan;

use App\Http\Controllers\Controller;
use App\Models\Kak\Kak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class KakApiController extends Controller
{
    /**
     * Mengambil daftar KAK yang sudah valid (Disetujui).
     * Digunakan oleh Modul RKA untuk menarik data kegiatan yang siap dianggarkan.
     */
    // app/Http/Controllers/Api/Perencanaan/KakApiController.php

public function getValidKak(Request $request)
{
    // Jika ada parameter ID, kirim detail satu KAK. Jika tidak, kirim semua yang statusnya 2.
    if ($request->has('id')) {
        $kak = \App\Models\Kak\Kak::with('user')->findOrFail($request->id);
        return response()->json(['data' => $kak]);
    }

    $kaks = \App\Models\Kak\Kak::where('status', 2)->get();
    return response()->json(['data' => $kaks]);
}

    /**
     * Menampilkan detail satu dokumen KAK secara lengkap.
     * Digunakan untuk pratinjau data lintas modul (Service Discovery).
     */
    public function show($id)
    {
        try {
            // Mengambil KAK beserta seluruh data teknis pendukungnya
            $kak = Kak::with([
                'pohonKinerja.indikators', 
                'timPelaksana', 
                'timelines'
            ])->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Detail data KAK berhasil ditemukan.',
                'data' => $kak
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dokumen KAK dengan ID tersebut tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('API KAK Error (show): ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server.',
                'detail' => $e->getMessage()
            ], 500);
        }
    }
}