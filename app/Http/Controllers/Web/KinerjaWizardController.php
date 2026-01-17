<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogKinerja;
use Illuminate\Support\Facades\DB; 
use App\Models\Kinerja\{Vision,Mission, Goal, SasaranStrategis, Program, Activity, SubActivity, AccessSetting};

class KinerjaWizardController extends Controller
{
    /**
     * Tampilan Utama Wizard
     */


    public function index()
    {
        $user = auth()->user();
        
        $activeVision = Vision::on('modul_kinerja')->where('is_active', true)->first();
        $startYear = $activeVision ? (int)$activeVision->tahun_awal : date('Y');
        
        $years = [];
        for ($i = 0; $i < 5; $i++) {
            $years[] = $startYear + $i;
        }

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

        return view('kinerja.wizard.index', compact('isLocked', 'access', 'startYear', 'years'));
    }

    public function storeStep(Request $request)
    {
        $request->validate([
            'step' => 'required',
            'nama' => 'required|string',
        ]);

        $step = (int) $request->step; 

        // 1. Tentukan Model, Parent Column, Indikator Column, dan Name Column
        $modelClass = null;
        $parentIdColumn = null;
        $indicatorColumn = 'indikator'; // Default
        $nameColumn = 'nama'; // Default
        
        switch ($step) {
            case 2: // Tujuan PD
                $modelClass = Goal::class;
                $parentIdColumn = 'mission_id';
                $indicatorColumn = 'indikator'; // Sesuai Migration: table->text('indikator')
                $nameColumn = 'nama_tujuan';
                break;
            case 3: // Sasaran
                $modelClass = SasaranStrategis::class;
                $parentIdColumn = 'goal_id';
                $indicatorColumn = 'indikator_sasaran'; // Sesuai Migration
                $nameColumn = 'nama_sasaran';
                break;
            case 4: // Program
                $modelClass = Program::class;
                $parentIdColumn = 'sasaran_id';
                $indicatorColumn = 'indikator_program'; // Sesuai Migration
                $nameColumn = 'nama_program';
                break;
            case 5: // Kegiatan
                $modelClass = Activity::class;
                $parentIdColumn = 'program_id';
                $indicatorColumn = 'indikator_kegiatan'; // Sesuai Migration
                $nameColumn = 'nama_kegiatan';
                break;
            case 6: // Sub Kegiatan
                $modelClass = SubActivity::class;
                $parentIdColumn = 'activity_id';
                $indicatorColumn = 'indikator_sub'; // Sesuai Migration
                $nameColumn = 'nama_sub';
                break;
            default:
                return response()->json(['message' => 'Step tidak valid'], 422);
        }

        $vision = Vision::on('modul_kinerja')->where('is_active', true)->first();
        $startYear = $vision ? (int)$vision->tahun_awal : date('Y');

        // 2. Siapkan Data Dasar (HANYA Data Umum)
        $data = [
            'satuan'    => $request->satuan,
            'baseline'  => $request->baseline, 
        ];

        // 3. Masukkan Data Spesifik (Indikator & Nama) dengan Key yang BENAR
        $data[$indicatorColumn] = $request->indikator;
        $data[$nameColumn] = $request->nama;

        // 4. Parent ID
        if ($request->parent_id && $parentIdColumn) {
            $data[$parentIdColumn] = $request->parent_id;
        }

        // 5. PD ID (Khusus Tujuan PD)
        if ($step == 2) {
            $data['pd_id'] = auth()->user()->pd_id;
        }

        // 6. Mapping Target 5 Tahun
        if ($request->has('target_values') && is_array($request->target_values)) {
            foreach ($request->target_values as $year => $value) {
                $yearIndex = ((int)$year - $startYear) + 1;
                if ($yearIndex >= 1 && $yearIndex <= 5) {
                    $data["tahun_{$yearIndex}"] = $value;
                }
            }
        } 

        // 7. Status & Tambahan Sub Kegiatan
        $data['status'] = 'draft';

        if ($step == 6) {
            $data['tipe_perhitungan'] = $request->tipe_perhitungan ?? 'Non-Akumulasi';
            $data['klasifikasi'] = $request->klasifikasi ?? 'IKK';
            // created_by_nip ada di migration sub_activities
            $data['created_by_nip'] = auth()->user()->nip; 
        }

        // 8. Eksekusi
        if ($request->id) {
            $modelClass::on('modul_kinerja')->where('id', $request->id)->update($data);
        } else {
            $modelClass::on('modul_kinerja')->create($data);
        }

        return response()->json(['message' => 'Data berhasil disimpan.', 'success' => true]);
    }
    
    // --- Helper Fetch Data Wizard ---
    public function fetchAllRejected()
    {
        try {
            $pd_id = auth()->user()->pd_id;
            $results = [];

            $levels = [
                ['name' => 'Tujuan', 'step' => 2, 'model' => \App\Models\Kinerja\Goal::class],
                ['name' => 'Sasaran', 'step' => 3, 'model' => \App\Models\Kinerja\SasaranStrategis::class],
                ['name' => 'Program', 'step' => 4, 'model' => \App\Models\Kinerja\Program::class],
                ['name' => 'Kegiatan', 'step' => 5, 'model' => \App\Models\Kinerja\Activity::class],
                ['name' => 'Sub-Kegiatan', 'step' => 6, 'model' => \App\Models\Kinerja\SubActivity::class],
            ];

            foreach ($levels as $l) {
                $query = $l['model']::on('modul_kinerja')->where('status', 'rejected');

                if ($l['step'] == 2) {
                    $query->where('pd_id', $pd_id);
                } elseif ($l['step'] == 3) {
                    $query->whereHas('goal', fn($q) => $q->where('pd_id', $pd_id));
                } elseif ($l['step'] == 4) {
                    $query->whereHas('sasaranStrategis.goal', fn($q) => $q->where('pd_id', $pd_id));
                } elseif ($l['step'] == 5) {
                    $query->whereHas('program.sasaranStrategis.goal', fn($q) => $q->where('pd_id', $pd_id));
                } elseif ($l['step'] == 6) {
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
        $level = (int)$level;

        switch ($level) {
            case 2: $data = Mission::on('modul_kinerja')->get(['id', 'misi_text as text']); break;
            case 3: $data = Goal::on('modul_kinerja')->where('pd_id', $pd_id)->get(['id', 'nama_tujuan as text']); break;
            case 4: $data = SasaranStrategis::on('modul_kinerja')->whereHas('goal', fn($q) => $q->where('pd_id', $pd_id))->get(['id', 'nama_sasaran as text']); break;
            case 5: $data = Program::on('modul_kinerja')->whereHas('sasaranStrategis.goal', fn($q) => $q->where('pd_id', $pd_id))->get(['id', 'nama_program as text']); break;
            case 6: $data = Activity::on('modul_kinerja')->whereHas('program.sasaranStrategis.goal', fn($q) => $q->where('pd_id', $pd_id))->get(['id', 'nama_kegiatan as text']); break;
        }
        return response()->json($data);
    }

    public function fetchExisting($level, $parentId)
    {
        $pd_id = auth()->user()->pd_id;
        $data = [];
        $level = (int)$level;

        switch ($level) {
            case 2: $data = Goal::on('modul_kinerja')->where('mission_id', $parentId)->where('pd_id', $pd_id)->get()->map(fn($item) => ['id' => $item->id, 'text' => $item->nama_tujuan]); break;
            case 3: $data = SasaranStrategis::on('modul_kinerja')->where('goal_id', $parentId)->get()->map(fn($item) => ['id' => $item->id, 'text' => $item->nama_sasaran]); break;
            case 4: $data = Program::on('modul_kinerja')->where('sasaran_id', $parentId)->get()->map(fn($item) => ['id' => $item->id, 'text' => $item->nama_program]); break;
            case 5: $data = Activity::on('modul_kinerja')->where('program_id', $parentId)->get()->map(fn($item) => ['id' => $item->id, 'text' => $item->nama_kegiatan]); break;
            case 6: $data = SubActivity::on('modul_kinerja')->where('activity_id', $parentId)->get()->map(fn($item) => ['id' => $item->id, 'text' => $item->nama_sub]); break;
        }
        return response()->json($data);
    }

    public function fetchDetail($level, $id)
    {
        $level = (int)$level;
        $model = match($level) {
            2 => Goal::on('modul_kinerja'),
            3 => SasaranStrategis::on('modul_kinerja'),
            4 => Program::on('modul_kinerja'),
            5 => Activity::on('modul_kinerja'),
            6 => SubActivity::on('modul_kinerja'),
            default => null
        };

        if (!$model) return response()->json(['error' => 'Invalid level'], 400);

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
                    'nip_verifier' => $item->nip_verifier ?? null,   
                    'nip_validator' => $item->nip_validator ?? null, 
                    'nip_approver' => $item->nip_approver ?? null,   
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

    public function storeAccess(Request $request)
    {
        $request->validate([
            'pd_id' => 'required',
            'level_izin' => 'required|array',
            'level_izin.*' => 'string',
        ]);

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
                    'updated_by_nip' => auth()->user()->nip 
                ]);
            }
        });

    $targetOPD = \App\Models\Admin\PerangkatDaerah::find($request->pd_id);
    $levels = implode(', ', $request->level_izin);
    
    LogKinerja::record(
        'CONFIG', 
        "Membuka akses input untuk OPD: {$targetOPD->nama_pd} pada level: $levels"
    );

        return back()->with('success', 'Aturan akses berhasil ditambahkan.');
    }

    public function destroyAccess($id)
    {
        $rule = AccessSetting::findOrFail($id);
        $rule->delete();
        return back()->with('success', 'Aturan akses berhasil dihapus.');
    }
}