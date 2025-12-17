<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kak\Kak;
use App\Models\Kak\KakTimeline;
use Illuminate\Support\Facades\DB;

class KakTimelineController extends Controller
{
    /**
     * Menyimpan atau Memperbarui Jadwal Timeline
     */
    public function store(Request $request, $kak_id)
    {
        $request->validate([
            'nama_tahapan.*' => 'required|string',
        ]);

        try {
            DB::connection('modul_kak')->beginTransaction();

            // Hapus jadwal lama untuk digantikan yang baru (Overwrite)
            KakTimeline::where('kak_id', $kak_id)->delete();

            if ($request->has('nama_tahapan')) {
                foreach ($request->nama_tahapan as $key => $nama) {
                    if (!empty($nama)) {
                        $data = [
                            'kak_id'       => $kak_id,
                            'nama_tahapan' => $nama,
                            'keterangan'   => $request->keterangan[$key] ?? null,
                        ];

                        // Mengisi boolean b1-b12 berdasarkan checkbox
                        for ($i = 1; $i <= 12; $i++) {
                            $data['b' . $i] = isset($request->input('b' . $i)[$key]) ? true : false;
                        }

                        KakTimeline::create($data);
                    }
                }
            }

            DB::connection('modul_kak')->commit();
            return back()->with('success', 'Jadwal Timeline berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::connection('modul_kak')->rollBack();
            return back()->with('error', 'Gagal menyimpan jadwal: ' . $e->getMessage());
        }
    }
}