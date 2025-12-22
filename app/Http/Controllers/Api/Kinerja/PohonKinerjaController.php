<?php

namespace App\Http\Controllers\Api\Kinerja;

use App\Http\Controllers\Controller;
use App\Models\Kinerja\PohonKinerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PohonKinerjaController extends Controller
{
    public function index()
    {
        try {
            $depth = 5; 
            $relations = ['indikators']; // Sesuai nama fungsi di model
            $currentRelation = 'children'; // Sesuai nama fungsi di model

            for ($i = 0; $i < $depth; $i++) {
                $relations[] = $currentRelation;
                $relations[] = $currentRelation . '.indikators';
                $currentRelation .= '.children';
            }
            
            // FIX: Ganti id_induk menjadi parent_id
            $data = PohonKinerja::whereNull('parent_id')
                                ->with($relations)
                                ->get(); 
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data pohon kinerja berhasil dimuat.',
                'data' => $data 
            ]);
        } catch (\Exception $e) {
            Log::error('API Kinerja Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            // FIX: Gunakan nama relasi yang benar
            $node = PohonKinerja::with(['children', 'indikators'])->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Detail node kinerja berhasil dimuat.',
                'data' => $node
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Node tidak ditemukan.'], 404);
        }
    }

    public function store(Request $request)
    {
        // FIX: Sesuaikan field dengan migration & seeder
        $validated = $request->validate([
            'nama_kinerja' => 'required|string',
            'jenis_kinerja' => 'required',
            'opd_id' => 'required|integer', 
            'penanggung_jawab' => 'nullable|string', 
            'parent_id' => 'nullable|exists:pohon_kinerja,id', 
        ]);

        $pohon = PohonKinerja::create($validated);
        return response()->json(['message' => 'Berhasil', 'data' => $pohon], 201);
    }
}