<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kinerja\{Goal, SasaranStrategis, Program, Activity, SubActivity};
use Illuminate\Support\Facades\Auth;

class KinerjaApprovalController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Cek apakah role itu objek (relasi) atau string biasa
        $role = strtolower(is_object($user->role) ? $user->role->name : $user->role);
        
        // Tentukan status berdasarkan role
        $targetStatus = match($role) {
            'kabid'   => 'pending',
            'kadis'   => 'verified',
            'bappeda' => 'validated',
            default   => 'none'
        };

        // Debugging: Jika data tidak muncul, hapus komentar dd di bawah ini untuk cek role & status
        // dd(['role_user' => $role, 'mencari_status' => $targetStatus]);

        $items = SubActivity::on('modul_kinerja')
                ->where('status', $targetStatus)
                ->with('activity') // Pastikan relasi ada agar tidak error di view
                ->get();

        return view('kinerja.inbox.index', compact('items', 'role'));
    }

    public function count()
    {
        $user = Auth::user();
        $role = strtolower(is_object($user->role) ? $user->role->name : $user->role);
        
        $targetStatus = match($role) {
            'kabid'   => 'pending',
            'kadis'   => 'verified',
            'bappeda' => 'validated',
            default   => 'none'
        };

        $count = SubActivity::on('modul_kinerja')->where('status', $targetStatus)->count();

        return response()->json(['count' => $count]);
    }

    public function approve(Request $request, $level, $id)
{
    $user = auth()->user();
    // Gunakan helper yang lebih aman untuk deteksi role
    $role = strtolower(is_object($user->role) ? $user->role->name : $user->role);
    $model = $this->getModel($level, $id);

    // Tentukan status selanjutnya
    $nextStatus = match($role) {
        'kabid'   => 'verified',
        'kadis'   => 'validated',
        'bappeda' => 'approved',
        default   => null // Kita set null agar tidak merusak data jika role salah
    };

    if (!$nextStatus) {
        return back()->with('error', 'Role Anda (' . $role . ') tidak diizinkan melakukan persetujuan.');
    }

    // Eksekusi Update
    $updateData = ['status' => $nextStatus];
    if($role == 'kabid')   $updateData['nip_verifier']  = $user->nip;
    if($role == 'kadis')   $updateData['nip_validator'] = $user->nip;
    if($role == 'bappeda') $updateData['nip_approver']  = $user->nip;

    $model->update($updateData);

    return back()->with('success', 'Berhasil menyetujui. Data kini berstatus: ' . $nextStatus);
}

    public function reject(Request $request, $level, $id)
    {
        $request->validate(['catatan' => 'required']);
        $model = $this->getModel($level, $id);

        $model->update([
            'status' => 'rejected',
            'catatan_revisi' => $request->catatan
        ]);

        return back()->with('warning', 'Data telah ditolak dan dikembalikan ke Staff.');
    }

    private function getModel($level, $id) {
        return match($level) {
            'sub_activity' => SubActivity::on('modul_kinerja')->findOrFail($id),
            // tambahkan level lain jika perlu
        };
    }
}