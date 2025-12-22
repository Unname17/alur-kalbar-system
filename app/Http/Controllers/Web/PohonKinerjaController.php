<?php

namespace App\Http\Controllers\Api\Kinerja;

use App\Http\Controllers\Controller;
use App\Models\Kinerja\PohonKinerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException; // Tambahkan ini

class PohonKinerjaController extends Controller
{
    /**
     * Menampilkan daftar Pohon Kinerja (termasuk hirarki anak).
     * SOLUSI PERFORMA: Menggunakan Nested Eager Loading untuk mengatasi N+1 Query.
     */


    /**
     * Menyimpan node baru Pohon Kinerja.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kinerja' => 'required|string',
            'jenis_kinerja' => 'required|in:visi,misi,tujuan,sasaran_strategis,sasaran,program,kegiatan,sub_kegiatan',
            'id_perangkat_daerah' => 'required|integer', 
            'id_penanggung_jawab' => 'required|integer', 
            'id_induk' => 'nullable|exists:pohon_kinerja,id', 
        ]);

        $pohon = PohonKinerja::create($validated);
        return response()->json(['message' => 'Pohon Kinerja berhasil ditambahkan', 'data' => $pohon], 201);
    }
    
    /**
     * Menampilkan detail satu node.
     */
    public function show($id)
    {
        try {
            // Mengambil detail node dengan semua relasi penting
            $node = PohonKinerja::with(['induk', 'anak', 'indikatorKinerja'])->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Detail node kinerja berhasil dimuat.',
                'data' => $node
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Node kinerja tidak ditemukan.'], 404);
        } catch (\Exception $e) {
             Log::error('API Kinerja Show Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal memuat detail node.'], 500);
        }
    }
    
    public function syncFromRka()
    {
        // 1. Ambil semua RKA yang statusnya 'disetujui' dari rka_main
        $rkaDisetujui = Rka::where('status_anggaran', 'disetujui')->get();

        $count = 0;
        foreach ($rkaDisetujui as $item) {
            // 2. Cek apakah data ini sudah pernah di-sync ke pengadaan sebelumnya agar tidak duplikat
            $exists = Pengadaan::where('rka_id', $item->id)->exists();

            if (!$exists) {
                DB::transaction(function () use ($item) {
                    // 3. Ambil data perencanaan untuk mendapatkan target_volume
                    $kak = RkaPerencanaan::with('rincianBelanja')->where('kak_id', $item->kak_id)->first();

                    // 4. Buat Header Pengadaan
                    $pengadaan = Pengadaan::create([
                        'rka_id'           => $item->id,
                        'kak_id'           => $item->kak_id,
                        'target_volume'    => $kak ? $kak->rincianBelanja->sum('volume') : 0,
                        'status_pengadaan' => 'berjalan', // Status awal
                    ]);

                    // 5. Otomatis buat 9 baris checklist dokumen
                    $listDokumen = [
                        'Spesifikasi Teknis', 'HPS', 'Nota Dinas Pengajuan', 
                        'Surat Pesanan/Kontrak', 'SPMK', 'Laporan Progres', 
                        'Berita Acara Pemeriksaan', 'BAST (Serah Terima)', 'Bukti Pembayaran'
                    ];

                    foreach ($listDokumen as $index => $nama) {
                        PengadaanDocument::create([
                            'pengadaan_id'   => $pengadaan->id,
                            'urutan_dokumen' => $index + 1,
                            'nama_dokumen'   => $nama,
                        ]);
                    }
                });
                $count++;
            }
        }

        return redirect()->back()->with('success', "$count data RKA baru berhasil disinkronkan ke Modul Pengadaan.");
    }

    public function index()
    {
        // Menampilkan daftar paket pengadaan yang sudah ditarik
        $daftarPengadaan = Pengadaan::with(['rka', 'rkaPerencanaan'])->get();
        return view('pengadaan.index', compact('daftarPengadaan'));
    }
    // Anda dapat menambahkan method update, destroy, ajukan, dan review di sini.
    // ...
}