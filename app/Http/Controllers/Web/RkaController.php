<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kak\Kak;            // Model KAK
use App\Models\Rka\MasterSsh;      // Model Katalog SSH
use App\Models\Rka\KakDetail;      // Model Rincian Belanja
use Maatwebsite\Excel\Facades\Excel; // <--- WAJIB ADA DI SINI (LUAR CLASS)
use App\Imports\RkaImport;         // <--- Import Class yang akan kita buat
use App\Exports\RkaTemplateExport; // <--- TAMBAHKAN INI
use Illuminate\Support\Facades\Auth; // <--- Pastikan ada ini

class RkaController extends Controller
{
    // 1. Halaman Pilih KAK (Level 1)
    public function pilihKak()
    {
        // --- LOGIKA REDIRECT ROLE ---
        $user = Auth::user();

        // Cek kolom 'peran' di tabel pengguna
        if ($user->peran == 'sekretariat') {
            // Jika Sekretariat, lempar ke halaman Verifikasi
            return redirect()->route('verifikasi.index');
        }

        // --- LOGIKA USER BIASA (KE BAWAH SINI) ---
        // Jika bukan sekretariat, dia akan lanjut membuka halaman Pilih KAK
        $listKak = Kak::with('pohonKinerja')
                      ->where('status', 2) // Hanya yang disetujui
                      ->get();
                      
        return view('rka.pilih_kak', compact('listKak'));
    }

    // 2. Halaman Belanja (Level 2)
    public function index($kak_id)
    {
        $kak = Kak::with('rincianBelanja')->where('status', 2)->findOrFail($kak_id);
        $katalog = MasterSsh::orderBy('nama_barang', 'asc')->get();
        
        return view('rka.index', compact('kak', 'katalog'));
    }

    // 3. Simpan Item (Bisa dari Katalog SSH atau Manual)
    public function store(Request $request, $kak_id)
    {
        $isManual = $request->has('is_manual') && $request->is_manual == 1;

        if ($isManual) {
            // Validasi Input Manual
            $request->validate([
                'nama_barang' => 'required|string|max:255',
                'harga_satuan' => 'required|numeric|min:0',
                'volume' => 'required|numeric|min:0.1',
                'satuan' => 'required|string',
                'kategori' => 'required|in:SSH,SBU',
            ]);

            $dataSave = [
                'kak_id'       => $kak_id,
                'ssh_id'       => null,
                'nama_barang'  => $request->nama_barang,
                'volume'       => $request->volume,
                'satuan'       => $request->satuan,
                'harga_satuan' => $request->harga_satuan,
                'total_harga'  => $request->harga_satuan * $request->volume,
                'keterangan'   => $request->keterangan,
                'is_manual'    => true,
                'is_verified'  => 1, // Status: Menunggu Verifikasi
                'kategori'     => $request->kategori,
            ];
        } else {
            // Validasi Katalog SSH
            $request->validate([
                'ssh_id' => 'required|exists:modul_anggaran.master_ssh,id', // Pastikan nama koneksi DB benar
                'volume' => 'required|numeric|min:0.1',
            ]);

            $ssh = MasterSsh::findOrFail($request->ssh_id);

            $dataSave = [
                'kak_id'       => $kak_id,
                'ssh_id'       => $ssh->id,
                'nama_barang'  => $ssh->nama_barang,
                'volume'       => $request->volume,
                'satuan'       => $ssh->satuan,
                'harga_satuan' => $ssh->harga_satuan,
                'total_harga'  => $ssh->harga_satuan * $request->volume,
                'is_manual'    => false,
                'is_verified'  => 2, // Otomatis Sah
                'kategori'     => $ssh->kategori,
            ];
        }

        KakDetail::create($dataSave);
        return back()->with('success', 'Item berhasil ditambahkan.');
    }

    // 4. Update Usulan Manual (Edit)
    public function updateManual(Request $request, $id)
    {
        $item = KakDetail::findOrFail($id);

        if (!$item->is_manual) {
            return back()->with('error', 'Item katalog tidak bisa diedit manual.');
        }

        $request->validate([
            'nama_barang' => 'required',
            'harga_satuan' => 'required|numeric',
            'volume' => 'required|numeric',
        ]);

        $item->update([
            'nama_barang' => $request->nama_barang,
            'harga_satuan' => $request->harga_satuan,
            'volume' => $request->volume,
            'total_harga' => $request->harga_satuan * $request->volume,
            'keterangan' => $request->keterangan,
            'is_verified' => 1 // Reset status jadi menunggu verifikasi
        ]);

        return back()->with('success', 'Usulan berhasil diperbarui.');
    }

    // 5. Hapus Item
    public function destroy($id)
    {
        $item = KakDetail::findOrFail($id);
        $item->delete();
        return back()->with('success', 'Item berhasil dihapus.');
    }

    // 6. Import Excel (Fitur Baru)
    public function importExcel(Request $request, $kak_id)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        // Memanggil Class Import yang kita buat di bawah
        Excel::import(new RkaImport($kak_id), $request->file('file'));

        return back()->with('success', 'Data berhasil diimport! Cek daftar usulan.');
    }

    // 7. Download Template Excel
    // 7. Download Template Excel (GENERATED AUTOMATICALLY)
    public function downloadTemplate()
    {
        // KODE LAMA (YANG ERROR):
        // $path = public_path('templates/template_rka.xlsx');
        // if (!file_exists($path)) { ... }
        
        // KODE BARU (SOLUSI):
        // Ini akan membuat file Excel secara otomatis saat diklik
        return Excel::download(new RkaTemplateExport, 'template_rka.xlsx');
    }
    /**
     * Kunci RKA dan Kirim ke Sekretariat
     */
    public function finalisasi($kak_id)
    {
        $kak = Kak::findOrFail($kak_id);

        // 1. Cek apakah masih ada usulan manual yang Pending (Status 1)
        // Kita tidak boleh membiarkan finalisasi jika masih ada item menggantung
        $pendingItems = KakDetail::where('kak_id', $kak_id)
                        ->where('is_manual', 1)
                        ->where('is_verified', 1) // 1 = Pending
                        ->exists();

        if ($pendingItems) {
            return back()->with('error', 'Gagal Finalisasi! Masih ada usulan item manual yang menunggu verifikasi admin. Harap tunggu atau hapus item tersebut.');
        }

        // 2. Cek apakah keranjang belanja kosong
        if ($kak->rincianBelanja()->count() == 0) {
            return back()->with('error', 'RKA masih kosong. Silakan isi rincian belanja terlebih dahulu.');
        }

        // 3. Update Status KAK (Misal: 3 = Menunggu Verifikasi RKA)
        // Pastikan Anda menyesuaikan logika status ini dengan sistem Anda
        $kak->update([
            'status' => 3 
        ]);

        return redirect()->route('rka.pilih_kak')->with('success', 'RKA Berhasil difinalisasi dan dikirim ke Sekretariat.');
    }
}