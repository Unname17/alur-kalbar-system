<?php

namespace App\Http\Controllers\Api\Perencanaan;

use App\Http\Controllers\Controller;
use App\Models\Kak\Kak;
use App\Models\Rka\Rka;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RkaApiController extends Controller
{
    /**
     * Mendapatkan daftar KAK yang siap dibuatkan RKA-nya.
     * Hanya mengambil KAK dengan status 2 (Disetujui)
     */
    public function getApprovedKak()
    {
        try {
            $data = Kak::where('status', 2)->get();
            return response()->json([
                'status' => 'success',
                'data'   => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Menarik data KAK ke dalam draf RKA (Simulation of Data Transfer)
     */
    // app/Http/Controllers/Api/Perencanaan/RkaApiController.php

public function syncFromKak($kak_id)
{
    try {
        // 1. Ambil data dari Modul KAK
        $kak = \App\Models\Kak\Kak::with('pohonKinerja')->findOrFail($kak_id);

        if ($kak->status != 2) {
            return response()->json(['message' => 'KAK belum disetujui'], 403);
        }

        // 2. Sinkronisasi ke Modul Anggaran (rka_main)
        // Pastikan nama kolom di sini SAMA dengan di Migration
        $rka = \App\Models\Rka\Rka::updateOrCreate(
            ['kak_id' => $kak->id],
            [
                'nomor_rka'        => $kak->nomor_kak ?? 'RKA-' . $kak->id, // Menggunakan nomor_rka
                'total_anggaran'   => 0, // Menggunakan total_anggaran (bukan total_pagu)
                'status_anggaran'  => 'draft' // Menggunakan status_anggaran (bukan status_rka)
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Sync Berhasil',
            'data' => $rka
        ], 200);

    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }
}
/**
     * LANGKAH 3: Cek Pagu Anggaran (Verifikasi Akhir)
     * Mengambil ringkasan anggaran untuk KAK tertentu
     */
    public function checkBudgetAvailability($id)
    {
        try {
            // Mencari data RKA berdasarkan ID RKA atau ID KAK
            $rka = Rka::with('details')->find($id);

            if (!$rka) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data RKA tidak ditemukan. Pastikan Anda sudah menjalankan Sync.'
                ], 404);
            }

            // Menghitung total dari rincian belanja secara real-time
            $totalRincian = $rka->details->sum('total_harga');

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id_rka'          => $rka->id,
                    'kak_id'          => $rka->kak_id,
                    'nomor_rka'       => $rka->nomor_rka,
                    'total_anggaran'  => $rka->total_anggaran,
                    'realisasi_input' => $totalRincian,
                    'status'          => $rka->status_anggaran,
                    'jumlah_item'     => $rka->details->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
}