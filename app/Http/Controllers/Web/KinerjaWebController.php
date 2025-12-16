<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Kinerja\PohonKinerja; // Pastikan namespace Model benar
use App\Models\Kinerja\IndikatorKinerja;

class KinerjaWebController extends Controller
{
    /**
     * Menampilkan Halaman Utama Pohon Kinerja
     */
    public function showPohonKinerja()
{
    $user = Auth::user();
    
    // SESUAIKAN: Pastikan nama kolom 'opd_id' atau 'id_perangkat_daerah'
    // Untuk sementara, jika data tetap kosong, coba hapus baris ->where('opd_id', ...) untuk testing
    $pohons = PohonKinerja::whereNull('parent_id')
        ->where('opd_id', 5) // Hardcode ke 5 dulu untuk memastikan data seeder muncul
        ->with([
            'indikators', 
            'children.indikators', 
            'children.children.indikators', 
            'children.children.children.indikators', 
            'children.children.children.children.indikators',
            'children.children.children.children.children.indikators'
        ])
        ->get();
        
    $parents = PohonKinerja::where('opd_id', 5)
        ->where('jenis_kinerja', '!=', 'sub_kegiatan') 
        ->orderBy('id', 'desc')
        ->get();

    return view('kinerja.pohon.index', [
        'viewTitle' => 'Pohon Kinerja Digital',
        'pohons' => $pohons,
        'parents' => $parents
    ]);
}

    /**
     * Menyimpan Data Baru (Store)
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'nama_kinerja' => 'required|string',
            'parent_id'    => 'required|exists:modul_kinerja.pohon_kinerja,id',
            'jenis_kinerja'=> 'required',
            // Validasi Array Indikator
            'indikator.*'  => 'nullable|string', 
            'target.*'     => 'nullable',
            'satuan.*'     => 'nullable',
        ]);

        DB::beginTransaction(); // Pakai Transaction biar aman
        try {
            // 2. Simpan Node Pohon Utama
            $pohon = PohonKinerja::create([
                'nama_kinerja'     => $request->nama_kinerja,
                'parent_id'        => $request->parent_id,
                'jenis_kinerja'    => $request->jenis_kinerja,
                'opd_id'           => Auth::user()->opd_id, // Sesuaikan dengan kolom di tabel user Anda
                'created_by'       => Auth::id(),
                'status'           => 'pengajuan', // Default status pengajuan
                'anggaran'         => $request->anggaran ?? 0,
                'penanggung_jawab' => $request->penanggung_jawab,
            ]);

            // 3. Simpan Banyak Indikator (Looping)
            if ($request->has('indikator')) {
                foreach ($request->indikator as $key => $val) {
                    // Hanya simpan jika teks indikator tidak kosong
                    if (!empty($val)) {
                        $pohon->indikators()->create([
                            'indikator' => $val,
                            'target'    => $request->target[$key] ?? '-',
                            'satuan'    => $request->satuan[$key] ?? '-',
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil diajukan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mengupdate Data (Update)
     */
    public function update(Request $request, $id)
    {
        $node = PohonKinerja::findOrFail($id);

        // Cek Hak Akses (Opsional: Hanya pembuat yang boleh edit jika status ditolak/draft)
        if ($node->created_by != Auth::id() && Auth::user()->peran != 'admin_utama') {
             // return response()->json(['status' => 'error', 'message' => 'Anda tidak berhak mengedit data ini.'], 403);
        }

        DB::beginTransaction();
        try {
            // 1. Update Data Utama
            $node->update([
                'nama_kinerja'     => $request->nama_kinerja,
                // parent_id & jenis_kinerja biasanya tidak diubah saat edit untuk menjaga struktur
                'status'           => 'pengajuan', // Reset status jadi pengajuan lagi agar diperiksa ulang
                'catatan_penolakan'=> null, // Hapus catatan penolakan lama
                'anggaran'         => $request->anggaran ?? 0,
                'penanggung_jawab' => $request->penanggung_jawab,
                'updated_at'       => now()
            ]);

            // 2. LOGIKA INDIKATOR: Hapus Semua Lama -> Input Semua Baru
            // Ini lebih mudah daripada mengecek satu-satu mana yang berubah
            $node->indikators()->delete();

            // 3. Input Ulang Indikator dari Form
            if ($request->has('indikator')) {
                foreach ($request->indikator as $key => $val) {
                    if (!empty($val)) {
                        $node->indikators()->create([
                            'indikator' => $val,
                            'target'    => $request->target[$key] ?? '-',
                            'satuan'    => $request->satuan[$key] ?? '-',
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Perbaikan berhasil disimpan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Proses Approval (Setuju/Tolak)
     */
    public function approval(Request $request, $id)
    {
        // Pastikan hanya role tertentu yang bisa akses (Middleware atau cek manual)
        // if (Auth::user()->peran != 'sekretariat') abort(403);

        $node = PohonKinerja::findOrFail($id);
        
        $statusBaru = ($request->action == 'setuju') ? 'disetujui' : 'ditolak';
        $catatan = ($request->action == 'tolak') ? $request->catatan : null;

        $node->update([
            'status' => $statusBaru,
            'catatan_penolakan' => $catatan
        ]);

        return response()->json(['status' => 'success', 'message' => 'Status berhasil diperbarui menjadi ' . $statusBaru]);
    }

    /**
     * Hapus Data (Delete)
     */
    public function destroy($id)
    {
        $node = PohonKinerja::findOrFail($id);
        
        // Karena kita pakai onDelete('cascade') di migration,
        // Menghapus pohon akan otomatis menghapus indikator & anak-anaknya.
        $node->delete();

        return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    }
}