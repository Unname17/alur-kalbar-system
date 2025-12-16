<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    public function index()
    {
        $data = Pengguna::with('perangkatDaerah')->get();
        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_perangkat_daerah' => 'required|exists:perangkat_daerah,id', // Harus ada di DB Admin
            'nama_lengkap' => 'required|string',
            'nip' => 'required|string|unique:pengguna,nip',
            'kata_sandi' => 'required|string|min:6',
            'peran' => 'required|in:admin_utama,kepala_dinas,staf,ppk',
            'status_input' => 'required|in:buka,tutup',
        ]);

        $validated['kata_sandi'] = Hash::make($validated['kata_sandi']);
        $pengguna = Pengguna::create($validated);
        
        return response()->json(['message' => 'Pengguna berhasil ditambahkan', 'data' => $pengguna], 201);
    }
    
    // show, update, destroy diisi dengan logika findOrFail dan update/delete.
    // ...
}