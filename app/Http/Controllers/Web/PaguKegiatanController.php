<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Kinerja\Activity; // <--- UBAH KE ACTIVITY (KEGIATAN)
use Illuminate\Http\Request;

class PaguKegiatanController extends Controller
{
    /**
     * MENAMPILKAN DAFTAR KEGIATAN (Bukan Sub)
     */
    public function index()
    {
        // Ambil Kegiatan yang statusnya approved/disetujui
        // Kita juga meload relasi 'program' agar bisa menampilkan nama programnya di tabel (opsional tapi bagus)
        $data = Activity::with('program')
                        ->where('status', 'approved') 
                        ->orWhere('status', 'disetujui')
                        ->orderBy('updated_at', 'desc')
                        ->get();

        return view('kinerja.pagu.index', compact('data'));
    }

    /**
     * FORM EDIT PAGU KEGIATAN
     */
    public function edit($id)
    {
        // Cari data Kegiatan
        $kegiatan = Activity::findOrFail($id);
        return view('kinerja.pagu.edit', compact('kegiatan'));
    }

    /**
     * UPDATE PAGU KEGIATAN
     */
    public function update(Request $request, $id)
    {
        $kegiatan = Activity::findOrFail($id);
        
        $request->validate([
            'pagu_anggaran' => 'required|numeric|min:0',
        ]);

        $kegiatan->pagu_anggaran = $request->pagu_anggaran;
        $kegiatan->save();

        return redirect()->route('kinerja.pagu.index')
            ->with('success', 'Pagu Kegiatan berhasil ditetapkan: ' . $kegiatan->nama_kegiatan);
    }
}