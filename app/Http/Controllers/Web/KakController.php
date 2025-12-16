<?php

namespace App\Http\Controllers\Kak;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kinerja\PohonKinerja;
use App\Models\Kak\Kak;
use App\Models\Kak\KakTimPelaksana;
use Illuminate\Support\Facades\DB;

class KakController extends Controller
{
    public function index()
{
    // Mengambil data Sub Kegiatan dari Modul Kinerja (Database alur_kalbar_kinerja)
    // untuk ditampilkan sebagai daftar KAK
    $listSubKegiatan = \App\Models\Kinerja\PohonKinerja::where('jenis_kinerja', 'sub_kegiatan')
                        ->with('kak') // Relasi ke database alur_kalbar_kak
                        ->get();

    return view('kak.index', compact('listSubKegiatan'));
}

    public function create($pohon_kinerja_id)
    {
        // Ambil data Sub Kegiatan dari database alur_kalbar_kinerja
        $subKegiatan = PohonKinerja::with('indikators')->findOrFail($pohon_kinerja_id);

        return view('kak.create', compact('subKegiatan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pohon_kinerja_id' => 'required',
            'judul_kak' => 'required',
        ]);

        try {
            DB::connection('modul_kak')->beginTransaction();

            // 1. Simpan ke database alur_kalbar_kak
            $kak = Kak::create($request->only([
                'pohon_kinerja_id', 'judul_kak', 'kode_proyek', 'dasar_hukum',
                'latar_belakang', 'maksud_tujuan', 'sasaran', 'metode_pelaksanaan',
                'lokasi', 'penerima_manfaat', 'waktu_mulai', 'waktu_selesai'
            ]));

            // 2. Simpan Tim Pelaksana jika ada (Logika sederhana)
            if ($request->has('nama_personil')) {
                foreach ($request->nama_personil as $key => $nama) {
                    KakTimPelaksana::create([
                        'kak_id' => $kak->id,
                        'nama_personil' => $nama,
                        'peran_dalam_tim' => $request->peran_dalam_tim[$key],
                        'nip' => $request->nip[$key],
                    ]);
                }
            }

            DB::connection('modul_kak')->commit();
            return redirect()->route('kak.show', $kak->id)->with('success', 'KAK Berhasil disimpan');

        } catch (\Exception $e) {
            DB::connection('modul_kak')->rollBack();
            return back()->with('error', 'Gagal simpan: ' . $e->getMessage());
        }
    }
}