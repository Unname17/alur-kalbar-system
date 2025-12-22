<?php

namespace App\Http\Controllers\Web;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kinerja\PohonKinerja;

class KinerjaWebController extends Controller
{
    /**
     * Menampilkan Halaman Utama Pohon Kinerja
     * Mengatasi error "Call to undefined method showPohonKinerja"
     */
        public function showPohonKinerja()
        {
            $parents = collect(); 
            $opds = collect();
            $allUsers = collect();
            $user = Auth::user();
            // 1. Tentukan Hak Akses Pohon
            $isValidator = in_array($user->peran, ['admin_utama', 'sekretariat', 'validator_bappeda']);
            
            // LOGIKA BARU: Jika validator, tampilkan semua yang ditolak. Jika bukan, filter per OPD
    $rejected = PohonKinerja::where('status', 'ditolak')
        ->with('indikators')
        ->when(!$isValidator, function($q) use ($user) {
            return $q->where('opd_id', $user->opd_id);
        })->get();
            // 1. Ambil data Inbox (Khusus status 'pengajuan')
            // $inbox = PohonKinerja::where('status', 'pengajuan')
            //             ->with('indikators')
            //             ->when(!$isValidator, function($q) use ($user) {
            //                 return $q->where('opd_id', $user->opd_id);
            //             })->get();
            $inbox = PohonKinerja::where('status', 'pengajuan')
            ->with('indikators')
            ->when(!$isValidator, function($q) use ($user) {
                return $q->where('opd_id', $user->id_perangkat_daerah);
            })->get();
            
            
            if ($isValidator) {
                // Validator melihat dari akar (Visi) untuk SEMUA OPD
                $pohons = PohonKinerja::whereNull('parent_id')
                    ->with(['indikators', 'children.children.children.children.children']) 
                    ->get();
        
        // PERBAIKAN: Tambahkan groupBy di sini agar struktur sama dengan role OPD
        $parents = PohonKinerja::all()->groupBy('jenis_kinerja'); 
        
        $opds = DB::connection('sistem_admin')->table('perangkat_daerah')->get();
        $allUsers = DB::connection('sistem_admin')->table('pengguna')
                    ->join('perangkat_daerah', 'pengguna.id_perangkat_daerah', '=', 'perangkat_daerah.id')
                    ->select('pengguna.*', 'perangkat_daerah.nama_perangkat_daerah')
                    ->get();
        
        // SINKRONISASI NAMA: Ganti allUsers menjadi opds agar sesuai dengan foreach di view
        $opds = DB::connection('sistem_admin')->table('perangkat_daerah')->get();
            } else {
                // User OPD hanya melihat cabang miliknya sendiri
                // 1. Tampilan Visual (Hanya ambil level teratas, sisanya via 'with')
                // 1. Ambil data VISUAL (5 Kotak di jalur pertama)
        $pohons = PohonKinerja::where('opd_id', $user->id_perangkat_daerah)
                    ->where('jenis_kinerja', 'sasaran_opd')
                    ->with(['children.children.children.children']) // Load semua level
                    ->get();

        // 2. Ambil data DROPDOWN (Sinkronkan agar hanya 5 data jalur tersebut)
        // Kita filter hanya jalur [01] agar tidak muncul puluhan data seeder lainnya
        $parents = PohonKinerja::where('opd_id', $user->id_perangkat_daerah)
                    ->where('nama_kinerja', 'LIKE', '%[01]%') // Filter agar hanya jalur yang tampil di visual
                    ->get()
                    ->groupBy('jenis_kinerja'); // Dikelompokkan untuk UI yang rapi
            }

            // 2. Data Pengguna untuk Fitur Kunci Pengguna (Ditarik dari DB Admin)
            $allUsers = collect();
            if ($isValidator) {
                $allUsers = DB::connection('sistem_admin')->table('pengguna')
                            ->join('perangkat_daerah', 'pengguna.id_perangkat_daerah', '=', 'perangkat_daerah.id')
                            ->select('pengguna.*', 'perangkat_daerah.nama_perangkat_daerah')
                            ->get();
            }

            return view('kinerja.pohon.index', [
                'viewTitle' => 'Pohon Kinerja Visual',
                'pohons' => $pohons,
                'inbox' => $inbox, 
                'parents' => $parents, 
                'opds' => $opds,       
                'allUsers' => $allUsers, // Untuk fitur Kunci Pengguna
                'rejected' => $rejected, // PASTIKAN VARIABEL INI DIKIRIM
                'isValidator' => $isValidator
            ]);
        }

public function getApiDetail($id)
{
    // Mengambil data yang sudah DISETUJUI oleh Bappeda
    $data = \App\Models\Kinerja\PohonKinerja::with('indikators')
            ->where('id', $id)
            ->where('status', 'disetujui') 
            ->first();

    if (!$data) {
        return response()->json(['message' => 'Data tidak ditemukan atau belum divalidasi'], 404);
    }

    // Mengembalikan data JSON untuk dikonsumsi Modul KAK
    return response()->json([
        'nama_kinerja' => $data->nama_kinerja,
        'anggaran'     => $data->anggaran,
        'pj'           => $data->penanggung_jawab,
        'indikators'   => $data->indikators, // Menarik target & satuan otomatis
    ]);
}

        public function approval(Request $request, $id)
    {
        $node = PohonKinerja::findOrFail($id);
        
        // Cek jika user mencentang "Setujui Seluruh Ranting"
        $isBulk = $request->has('bulk') && $request->bulk == "1";

        if ($isBulk && $request->action == 'setuju') {
            // Validasi rekursif ke bawah (Massal)
            $this->approveRecursive($id);
            $message = 'Satu pohon kinerja berhasil disetujui secara massal.';
        } else {
            // Validasi Satuan
            $node->update([
                'status' => ($request->action == 'setuju') ? 'disetujui' : 'ditolak',
                'catatan_penolakan' => $request->catatan
            ]);
            $message = 'Status inputan berhasil diperbarui.';
        }

        return response()->json(['status' => 'success', 'message' => $message]);
    }

    private function approveRecursive($parentId)
    {
        PohonKinerja::where('id', $parentId)->update(['status' => 'disetujui']);
        $children = PohonKinerja::where('parent_id', $parentId)->get();
        foreach ($children as $child) {
            $this->approveRecursive($child->id);
        }
    }
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
// app/Http/Controllers/Web/KinerjaWebController.php

public function editDetail($id)
{
    // Mengambil data lengkap termasuk indikator tanpa mempedulikan status
    $data = PohonKinerja::with('indikators')->find($id);

    if (!$data) {
        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }

    return response()->json($data);
}
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
}