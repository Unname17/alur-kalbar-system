<?php

namespace App\Http\Controllers\Api\Kinerja;

use App\Http\Controllers\Controller;
use App\Models\Kinerja\IndikatorKinerja;
use App\Models\Kinerja\TargetPeriode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndikatorController extends Controller
{
    public function index()
    {
        $data = IndikatorKinerja::with('pohonKinerja', 'targetPeriode')->get();
        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        // Kita menggunakan transaksi karena melibatkan dua tabel: indikator dan target_periode
        DB::connection('modul_kinerja')->transaction(function () use ($request) {
            
            $validated = $request->validate([
                'id_pohon_kinerja' => 'required|exists:pohon_kinerja,id',
                'tolok_ukur' => 'required|string',
                'satuan' => 'required|string',
                'target_tahunan' => 'required|numeric',
                'pagu_anggaran' => 'nullable|numeric',
                'targets' => 'required|array|size:4', // Harusnya 4 TW
            ]);

            $indikator = IndikatorKinerja::create($validated);

            foreach ($validated['targets'] as $targetData) {
                TargetPeriode::create([
                    'id_indikator_kinerja' => $indikator->id,
                    'periode' => $targetData['periode'],
                    'target' => $targetData['target'],
                    'satuan' => $validated['satuan'],
                ]);
            }
        });

        return response()->json(['message' => 'Indikator dan target berhasil ditambahkan'], 201);
    }

    // show, update, destroy
    // ...
}