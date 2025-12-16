<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\PerangkatDaerah;
use Illuminate\Http\Request;

class PerangkatDaerahController extends Controller
{
    public function index()
    {
        $data = PerangkatDaerah::all();
        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_perangkat_daerah' => 'required|string|max:255',
            'kode_unit' => 'nullable|string|max:50',
            'singkatan' => 'nullable|string|max:10',
            'status_input' => 'required|in:buka,tutup',
        ]);

        $opd = PerangkatDaerah::create($validated);
        return response()->json(['message' => 'OPD berhasil ditambahkan', 'data' => $opd], 201);
    }

    public function show($id)
    {
        $opd = PerangkatDaerah::findOrFail($id);
        return response()->json(['data' => $opd]);
    }

    public function update(Request $request, $id)
    {
        $opd = PerangkatDaerah::findOrFail($id);
        $opd->update($request->all());
        return response()->json(['message' => 'OPD berhasil diperbarui', 'data' => $opd]);
    }

    public function destroy($id)
    {
        PerangkatDaerah::findOrFail($id)->delete();
        return response()->json(['message' => 'OPD berhasil dihapus'], 204);
    }
}