<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogKinerja;
use Illuminate\Support\Facades\DB; // Tambahkan ini untuk Transaksi Database
use App\Models\Kinerja\{Mission, Goal, SasaranStrategis, Program, Activity, SubActivity, AccessSetting};

class KinerjaWizardController extends Controller
{
    /**
     * Tampilan Utama Wizard
     */
    public function index()
    {
        $user = auth()->user();
        
        // Cek aturan akses
        $access = AccessSetting::where(function($q) use ($user) {
                $q->where('user_nip', $user->nip)
                  ->orWhere(function($sq) use ($user) {
                      $sq->where('pd_id', $user->pd_id)
                         ->whereNull('user_nip');
                  });
            })->first();

        $isLocked = false;
        
        if ($access) {
            $now = now();
            $manualLock = (bool)$access->is_locked;
            $timeLocked = ($access->waktu_buka && $access->waktu_tutup) && 
                          ($now->lt($access->waktu_buka) || $now->gt($access->waktu_tutup));
            $isLocked = $manualLock || $timeLocked;
        }

        return view('kinerja.wizard.index', compact('isLocked', 'access'));
    }

    /**
     * Logika Simpan Per Step (Wizard)
     */
    /**
     * Logika Simpan Per Step (Wizard)
     */
    public function storeStep(Request $request)
    {
        $step = (int) $request->step;
        $targetColumn = 'target_' . ($request->tahun_input ?? '2025');
        $id = $request->existing_id;
        $user = auth()->user();

        // 1. Ambil Role User (Handle jika relation object atau string biasa)
        $userRole = strtolower(is_object($user->role) ? $user->role->name : $user->role);

        try {
            // 2. Siapkan Data Teknis (Target & Satuan)
            $updateData = [
                $targetColumn => $request->target_value,
                'satuan' => $request->satuan,
            ];

            // 3. LOGIKA PENENTUAN STATUS (Perbaikan di sini)
            if (!$id) {
                // KASUS A: Input Baru -> Wajib Pending
                $updateData['status'] = 'pending';
                $updateData['catatan_revisi'] = null;
            } else {
                // KASUS B: Edit Data Lama
                
                // Daftar Role Pejabat yang boleh edit TANPA mereset status
                $approvers = ['kabid', 'kadis', 'bappeda', 'admin_utama'];

                if (!in_array($userRole, $approvers)) {
                    // Jika yang edit adalah STAFF / Operator -> Reset jadi Pending (Minta verifikasi ulang)
                    $updateData['status'] = 'pending';
                    $updateData['catatan_revisi'] = null; // Hapus catatan revisi lama jika ada
                } 
                // ELSE: Jika yang edit adalah Pejabat, kita TIDAK menyertakan key 'status' 
                // ke dalam array $updateData, sehingga status di database tidak berubah.
            }

            // 4. Proses Simpan ke Database
            $data = match ($step) {
                1 => Goal::on('modul_kinerja')->updateOrCreate(['id' => $id], array_merge($updateData, [
                    'mission_id' => $request->parent_id, 'pd_id' => $user->pd_id, 'nama_tujuan' => $request->nama, 'indikator' => $request->indikator
                ])),
                2 => SasaranStrategis::on('modul_kinerja')->updateOrCreate(['id' => $id], array_merge($updateData, [
                    'goal_id' => $request->parent_id, 'nama_sasaran' => $request->nama, 'indikator_sasaran' => $request->indikator
                ])),
                3 => Program::on('modul_kinerja')->updateOrCreate(['id' => $id], array_merge($updateData, [
                    'sasaran_id' => $request->parent_id, 'nama_program' => $request->nama, 'indikator_program' => $request->indikator
                ])),
                4 => Activity::on('modul_kinerja')->updateOrCreate(['id' => $id], array_merge($updateData, [
                    'program_id' => $request->parent_id, 'nama_kegiatan' => $request->nama, 'indikator_kegiatan' => $request->indikator
                ])),
                5 => SubActivity::on('modul_kinerja')->updateOrCreate(['id' => $id], array_merge($updateData, [
                    'activity_id' => $request->parent_id, 'nama_sub' => $request->nama, 'indikator_sub' => $request->indikator,
                    'tipe_perhitungan' => $request->tipe_perhitungan, 'klasifikasi' => $request->klasifikasi, 'created_by_nip' => $user->nip
                ])),
            };

            // 5. Catat Log Aktivitas
            $actionType = $request->existing_id ? 'UPDATE' : 'CREATE';
            $levelName = match((int)$request->step) {
                1 => 'Tujuan PD', 2 => 'Sasaran', 3 => 'Program', 4 => 'Kegiatan', 5 => 'Sub-Kegiatan'
            };

            LogKinerja::record(
                $actionType, 
                "Melakukan $actionType data $levelName dengan nama: " . $request->nama,
                $data 
            );

            return response()->json([
                'success' => true, 
                'inserted_id' => $data->id, 
                'message' => 'Data berhasil disimpan.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    // --- Helper Fetch Data Wizard ---
    public function fetchAllRejected()
    {
        try {
            $pd_id = auth()->user()->pd_id;
            $results = [];

            $levels = [
                ['name' => 'Tujuan', 'step' => 1, 'model' => \App\Models\Kinerja\Goal::class],
                ['name' => 'Sasaran', 'step' => 2, 'model' => \App\Models\Kinerja\SasaranStrategis::class],
                ['name' => 'Program', 'step' => 3, 'model' => \App\Models\Kinerja\Program::class],
                ['name' => 'Kegiatan', 'step' => 4, 'model' => \App\Models\Kinerja\Activity::class],
                ['name' => 'Sub-Kegiatan', 'step' => 5, 'model' => \App\Models\Kinerja\SubActivity::class],
            ];

            foreach ($levels as $l) {
                $query = $l['model']::on('modul_kinerja')->where('status', 'rejected');

                if ($l['step'] == 1) {
                    $query->where('pd_id', $pd_id);
                } elseif ($l['step'] == 2) {
                    $query->whereHas('goal', fn($q) => $q->where('pd_id', $pd_id));
                } elseif ($l['step'] == 3) {
                    $query->whereHas('sasaranStrategis.goal', fn($q) => $q->where('pd_id', $pd_id));
                } elseif ($l['step'] == 4) {
                    $query->whereHas('program.sasaranStrategis.goal', fn($q) => $q->where('pd_id', $pd_id));
                } elseif ($l['step'] == 5) {
                    $query->whereHas('activity.program.sasaranStrategis.goal', fn($q) => $q->where('pd_id', $pd_id));
                }

                $data = $query->get();

                foreach ($data as $d) {
                    $results[] = [
                        'id' => $d->id,
                        'step' => $l['step'],
                        'level_name' => $l['name'],
                        'nama' => $d->nama_tujuan ?? $d->nama_sasaran ?? $d->nama_program ?? $d->nama_kegiatan ?? $d->nama_sub,
                        'catatan' => $d->catatan_revisi ?? 'Tidak ada catatan.',
                        'parent_id' => $d->mission_id ?? $d->goal_id ?? $d->sasaran_id ?? $d->program_id ?? $d->activity_id
                    ];
                }
            }
            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }    

    public function fetchParents($level)
    {
        $pd_id = auth()->user()->pd_id;
        $data = [];
        switch ($level) {
            case 1: $data = Mission::on('modul_kinerja')->get(['id', 'misi_text as text']); break;
            case 2: $data = Goal::on('modul_kinerja')->where('pd_id', $pd_id)->get(['id', 'nama_tujuan as text']); break;
            case 3: $data = SasaranStrategis::on('modul_kinerja')->whereHas('goal', fn($q) => $q->where('pd_id', $pd_id))->get(['id', 'nama_sasaran as text']); break;
            case 4: $data = Program::on('modul_kinerja')->whereHas('sasaranStrategis.goal', fn($q) => $q->where('pd_id', $pd_id))->get(['id', 'nama_program as text']); break;
            case 5: $data = Activity::on('modul_kinerja')->whereHas('program.sasaranStrategis.goal', fn($q) => $q->where('pd_id', $pd_id))->get(['id', 'nama_kegiatan as text']); break;
        }
        return response()->json($data);
    }

    public function fetchExisting($level, $parentId)
    {
        $pd_id = auth()->user()->pd_id;
        $data = [];
        switch ((int)$level) {
            case 1: $data = Goal::on('modul_kinerja')->where('mission_id', $parentId)->where('pd_id', $pd_id)->get()->map(fn($item) => ['id' => $item->id, 'text' => $item->nama_tujuan]); break;
            case 2: $data = SasaranStrategis::on('modul_kinerja')->where('goal_id', $parentId)->get()->map(fn($item) => ['id' => $item->id, 'text' => $item->nama_sasaran]); break;
            case 3: $data = Program::on('modul_kinerja')->where('sasaran_id', $parentId)->get()->map(fn($item) => ['id' => $item->id, 'text' => $item->nama_program]); break;
            case 4: $data = Activity::on('modul_kinerja')->where('program_id', $parentId)->get()->map(fn($item) => ['id' => $item->id, 'text' => $item->nama_kegiatan]); break;
            case 5: $data = SubActivity::on('modul_kinerja')->where('activity_id', $parentId)->get()->map(fn($item) => ['id' => $item->id, 'text' => $item->nama_sub]); break;
        }
        return response()->json($data);
    }

    public function fetchDetail($level, $id)
    {
        $model = match((int)$level) {
            1 => Goal::on('modul_kinerja'),
            2 => SasaranStrategis::on('modul_kinerja'),
            3 => Program::on('modul_kinerja'),
            4 => Activity::on('modul_kinerja'),
            5 => SubActivity::on('modul_kinerja'),
        };
        $data = $model->find($id);
        if ($data) {
            $data->nama = $data->nama_tujuan ?? $data->nama_sasaran ?? $data->nama_program ?? $data->nama_kegiatan ?? $data->nama_sub;
            $data->indikator = $data->indikator ?? $data->indikator_sasaran ?? $data->indikator_program ?? $data->indikator_kegiatan ?? $data->indikator_sub;
        }
        return response()->json($data);
    }

    // --- Page Monitoring ---
public function monitoring()
    {
        $pd_id = auth()->user()->pd_id;
        
        // Helper function untuk format data seragam
        $formatData = function($items, $level) {
            return $items->map(function($item) use ($level) {
                return (object) [
                    'nama' => $item->nama_tujuan ?? $item->nama_sasaran ?? $item->nama_program ?? $item->nama_kegiatan ?? $item->nama_sub,
                    'level' => $level,
                    'status' => $item->status,
                    // Kita butuh data ini untuk logika Stepper (Hijau/Merah)
                    'nip_verifier' => $item->nip_verifier ?? null,   // Kabid
                    'nip_validator' => $item->nip_validator ?? null, // Kadis
                    'nip_approver' => $item->nip_approver ?? null,   // Bappeda
                ];
            });
        };

        $goals = \App\Models\Kinerja\Goal::on('modul_kinerja')->where('pd_id', $pd_id)->get();
        $sasaran = \App\Models\Kinerja\SasaranStrategis::on('modul_kinerja')->whereHas('goal', fn($q) => $q->where('pd_id', $pd_id))->get();
        $program = \App\Models\Kinerja\Program::on('modul_kinerja')->whereHas('sasaranStrategis.goal', fn($q) => $q->where('pd_id', $pd_id))->get();
        $kegiatan = \App\Models\Kinerja\Activity::on('modul_kinerja')->whereHas('program.sasaranStrategis.goal', fn($q) => $q->where('pd_id', $pd_id))->get();
        $subKegiatan = \App\Models\Kinerja\SubActivity::on('modul_kinerja')->whereHas('activity.program.sasaranStrategis.goal', fn($q) => $q->where('pd_id', $pd_id))->get();

        $allData = collect()
            ->concat($formatData($goals, 'Tujuan PD'))
            ->concat($formatData($sasaran, 'Sasaran Strategis'))
            ->concat($formatData($program, 'Program'))
            ->concat($formatData($kegiatan, 'Kegiatan'))
            ->concat($formatData($subKegiatan, 'Sub-Kegiatan'));

        return view('kinerja.wizard.monitoring', compact('allData'));
    }

    // --- MANAJEMEN AKSES (Admin) ---

    public function manageAccess()
    {
        $pdList = \App\Models\Admin\PerangkatDaerah::get();
        // Eager load untuk performa
        $accessRules = AccessSetting::with(['perangkatDaerah', 'user', 'goal'])->get();
        return view('kinerja.admin.akses', compact('pdList', 'accessRules'));
    }

    public function fetchPegawaiByOpd($pd_id)
    {
        try {
            $users = \App\Models\Admin\User::on('sistem_admin')
                        ->where('pd_id', $pd_id)
                        ->get(['nip', 'nama_lengkap']);
            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function fetchGoalsByOpd($pd_id)
    {
        $goals = Goal::on('modul_kinerja')
                    ->where('pd_id', $pd_id)
                    ->get(['id', 'nama_tujuan']);
        return response()->json($goals);
    }

    // [DIPERBAIKI] Menyimpan Aturan Akses (Multi-Select)
public function storeAccess(Request $request)
    {
        // Validasi
        $request->validate([
            'pd_id' => 'required',
            'level_izin' => 'required|array',
            'level_izin.*' => 'string',
        ]);

        // Transaksi Database
        DB::transaction(function () use ($request) {
            $levels = $request->level_izin;

            foreach ($levels as $level) {
                AccessSetting::create([
                    'pd_id' => $request->pd_id,
                    'user_nip' => $request->user_nip,
                    'parent_id' => $request->parent_id,
                    'level_izin' => $level,
                    'waktu_buka' => $request->waktu_buka,
                    'waktu_tutup' => $request->waktu_tutup,
                    'pesan_blokir' => $request->pesan_blokir,
                    'is_locked' => $request->has('is_locked') ? 1 : 0,
                    
                    // Sesuai tabel: updated_by_nip diisi NIP user login
                    'updated_by_nip' => auth()->user()->nip 
                ]);
            }
        });

        // Di luar loop atau sesudah loop
    $targetOPD = \App\Models\Admin\PerangkatDaerah::find($request->pd_id);
    $levels = implode(', ', $request->level_izin);
    
    LogKinerja::record(
        'CONFIG', 
        "Membuka akses input untuk OPD: {$targetOPD->nama_pd} pada level: $levels"
    );

        return back()->with('success', 'Aturan akses berhasil ditambahkan.');
    }

    // [DITAMBAHKAN] Menghapus Aturan Akses
    public function destroyAccess($id)
    {
        $rule = AccessSetting::findOrFail($id);
        $rule->delete();
        return back()->with('success', 'Aturan akses berhasil dihapus.');
    }
}