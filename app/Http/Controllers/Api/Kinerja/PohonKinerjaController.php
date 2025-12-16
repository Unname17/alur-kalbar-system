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
    public function index()
    {
        try {
            // Batas kedalaman rekursif yang wajar (misalnya, 5 level di bawah root)
            $depth = 5; 
            
            // Siapkan Eager Loading Rekursif
            $relations = ['indikatorKinerja']; // Selalu ambil indikator
            $currentRelation = 'anak';

            // Membangun array ['anak', 'anak.anak', 'anak.anak.anak', ...]
            for ($i = 0; $i < $depth; $i++) {
                $relations[] = $currentRelation;
                $currentRelation .= '.anak';
            }
            
            // Eksekusi Query
            $data = PohonKinerja::whereNull('id_induk')
                                ->with($relations) // Load semua level sekaligus
                                ->get(); 
            
            // RESPON DIKEMBALIKAN KE FORMAT YANG DIHARAPKAN JAVASCRIPT FRONTEND
            return response()->json([
                'status' => 'success',
                'message' => 'Data pohon kinerja berhasil dimuat.',
                'data' => $data 
            ]);

        } catch (\Exception $e) {
            // Menangkap dan Log Error (Jika ada error koneksi atau skema DB)
            Log::error('API Kinerja Error: ' . $e->getMessage() . ' di ' . $e->getFile() . ' baris ' . $e->getLine());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat data: Error Performa/Skema. Cek log Laravel. Detail: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

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
    
    // Anda dapat menambahkan method update, destroy, ajukan, dan review di sini.
    // ...
}