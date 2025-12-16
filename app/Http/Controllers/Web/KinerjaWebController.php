<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// PERBAIKAN: Baris ini wajib ada agar DB::connection() jalan
use Illuminate\Support\Facades\DB; 
use App\Models\Kinerja\PohonKinerja;
use App\Models\Kinerja\AksesPenambahanKinerja;

class KinerjaWebController extends Controller
{
    /**
     * HALAMAN UTAMA POHON KINERJA (DENGAN FILTER PARENT)
     */
    public function showPohonKinerja()
    {
        $user = Auth::user();
        $isSekretariat = in_array($user->peran, ['sekretariat', 'admin_utama']);
        $opdId = $user->id_perangkat_daerah ?? 0;

        // 1. QUERY POHON (Deep Loading)
        $relations = [
            'children', 'children.children', 'children.children.children', 
            'children.children.children.children', 'children.children.children.children.children'
        ];
        
        $closureFilter = function($query) use ($opdId, $isSekretariat) {
            if (!$isSekretariat && $opdId) {
                $query->where(function($q) use ($opdId) {
                    $q->where('opd_id', $opdId)->orWhere('created_by', Auth::id());
                });
            }
        };

        $eagerLoad = [];
        foreach ($relations as $rel) { $eagerLoad[$rel] = $closureFilter; }

        $pohons = PohonKinerja::query()->whereNull('parent_id')->with($eagerLoad)->get();
        if ($pohons->isEmpty()) $pohons = [];

        // 2. LOGIKA DROPDOWN PARENT (FILTER AKSES)
        if ($isSekretariat) {
            $parents = PohonKinerja::where('status', 'disetujui')
                        ->orderBy('nama_kinerja', 'asc')->get();
        } else {
            $allowedParentIds = AksesPenambahanKinerja::where('opd_id', $opdId)
                                ->where('is_active', true)
                                ->pluck('parent_id_allowed');

            $parents = PohonKinerja::whereIn('id', $allowedParentIds)
                        ->where('status', 'disetujui')
                        ->orderBy('nama_kinerja', 'asc')->get();
        }

        return view('kinerja.pohon.index', [
            'viewTitle' => 'Pohon Kinerja',
            'pohons'    => $pohons,
            'parents'   => $parents,
            'user'      => $user
        ]);
    }

    /**
     * HALAMAN PENGATURAN AKSES (KHUSUS SEKRETARIAT)
     */
    public function indexAkses()
    {
        // 1. Cek Hak Akses
        if (!in_array(Auth::user()->peran, ['sekretariat', 'admin_utama'])) {
            return redirect()->route('kinerja.pohon')->with('error', 'Akses Ditolak.');
        }

        // 2. Ambil Rules yang sudah ada
        $rules = AksesPenambahanKinerja::with('parentNode')
                    ->orderBy('id', 'desc')
                    ->get();
        
        // 3. Ambil Node Pohon yang Disetujui (untuk dropdown parent)
        $allNodes = PohonKinerja::where('status', 'disetujui')
                    ->orderBy('nama_kinerja')
                    ->get();

        // 4. Ambil Daftar OPD dari database sistem_admin
        // PERBAIKAN: DB::connection() sekarang akan berhasil karena sudah di-use diatas
        $listOpd = DB::connection('sistem_admin')
                    ->table('perangkat_daerah')
                    ->where('nama_perangkat_daerah', 'not like', '%Sekretariat%') 
                    ->orderBy('nama_perangkat_daerah', 'asc')
                    ->get();

        return view('kinerja.akses.index', [
            'viewTitle' => 'Pengaturan Akses Input',
            'rules'     => $rules,
            'allNodes'  => $allNodes,
            'listOpd'   => $listOpd 
        ]);
    }

    public function storeAkses(Request $request)
    {
        $request->validate([
            'opd_id' => 'required|integer',
            'parent_id_allowed' => 'required',
            'jenis_kinerja_allowed' => 'required'
        ]);

        AksesPenambahanKinerja::create([
            'opd_id' => $request->opd_id,
            'role_target' => 'opd',
            'parent_id_allowed' => $request->parent_id_allowed,
            'jenis_kinerja_allowed' => $request->jenis_kinerja_allowed,
            'created_by' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Akses berhasil diberikan.');
    }

    public function deleteAkses($id)
    {
        AksesPenambahanKinerja::destroy($id);
        return redirect()->back()->with('success', 'Akses dicabut.');
    }

    /**
     * MENYIMPAN DATA BARU (STORE)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kinerja'  => 'required|string',
            'parent_id'     => 'required|exists:modul_kinerja.pohon_kinerja,id', 
            'jenis_kinerja' => 'required',
        ]);

        try {
            PohonKinerja::create([
                'nama_kinerja'  => $request->nama_kinerja,
                'parent_id'     => $request->parent_id,
                'jenis_kinerja' => $request->jenis_kinerja,
                'opd_id'        => Auth::user()->id_perangkat_daerah, 
                'created_by'    => Auth::id(),
                'status'        => 'pengajuan',
            ]);

            return response()->json(['message' => 'Berhasil disimpan!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    /**
     * UPDATE / REVISI DATA
     */
    public function update(Request $request, $id)
    {
        $request->validate(['nama_kinerja' => 'required|string']);
        
        $node = PohonKinerja::findOrFail($id);
        
        if ($node->created_by != Auth::id()) {
            return response()->json(['status' => 'error', 'message' => 'Anda tidak berhak mengedit data ini.'], 403);
        }

        $node->update([
            'nama_kinerja' => $request->nama_kinerja,
            'status' => 'pengajuan', 
            'catatan_penolakan' => null, 
            'updated_at' => now()
        ]);

        return response()->json(['status' => 'success', 'message' => 'Revisi berhasil dikirim ulang! Menunggu persetujuan Kabid.']);
    }

    /**
     * APPROVAL (KHUSUS KABID / ADMIN / SEKRETARIAT)
     */
    public function approval(Request $request, $id)
    {
        $userRole = Auth::user()->peran;
        if(!in_array($userRole, ['kepala_bidang', 'admin_utama', 'sekretariat'])) {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

        $node = PohonKinerja::findOrFail($id);
        
        if($request->action == 'setuju') {
            $node->update([
                'status' => 'disetujui', 
                'catatan_penolakan' => null,
                'updated_at' => now()
            ]);
            $msg = 'Pengajuan disetujui dan diterbitkan.';
        } else {
            if(empty($request->catatan)) {
                return response()->json(['status' => 'error', 'message' => 'Alasan penolakan wajib diisi!'], 422);
            }

            $node->update([
                'status' => 'ditolak', 
                'catatan_penolakan' => $request->catatan,
                'updated_at' => now()
            ]);
            $msg = 'Pengajuan ditolak. Staff akan menerima notifikasi revisi.';
        }

        return response()->json(['status' => 'success', 'message' => $msg]);
    }
}