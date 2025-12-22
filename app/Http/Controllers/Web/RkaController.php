<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http; // Wajib import untuk API call
use Illuminate\Http\Request;
use App\Models\Rka\RkaPerencanaan;
use App\Models\Rka\MasterSsh;      // Model Katalog SSH
use App\Models\Rka\KakDetail;      // Model Rincian Belanja
use Maatwebsite\Excel\Facades\Excel; // <--- WAJIB ADA DI SINI (LUAR CLASS)
use App\Imports\RkaImport;         // <--- Import Class yang akan kita buat
use App\Exports\RkaTemplateExport; // <--- TAMBAHKAN INI
use Illuminate\Support\Facades\Auth; // <--- Pastikan ada ini
use App\Models\Rka\Rka; // Tambahkan ini di deretan 'use'

class RkaController extends Controller
{
    // app/Http/Controllers/Web/RkaController.php

    // app/Http/Controllers/Web/RkaController.php

// app/Http/Controllers/Web/RkaController.php

public function syncAllFromKak()
{
    // 1. Ambil semua KAK yang sudah disetujui (Status 2) dari database KAK
    $kaks = \App\Models\Kak\Kak::on('modul_kak')->where('status', 2)->get();

    if ($kaks->isEmpty()) {
        return back()->with('error', 'Tidak ada data KAK baru untuk disinkronkan.');
    }

    // 2. Lakukan perulangan untuk menyimpan ke tabel lokal rka_perencanaan
    foreach ($kaks as $kak) {
        // Simpan ke Mirror Table Lokal
        \App\Models\Rka\RkaPerencanaan::updateOrCreate(
            ['kak_id' => $kak->id],
            [
                'judul_kak'    => $kak->judul_kak,
                'kode_proyek'  => $kak->kode_proyek ?? '-',
                'nama_pembuat' => $kak->user->nama_lengkap ?? 'User',
                'status_internal' => 'baru', // Status awal di Modul RKA
                'updated_at'   => now(),
            ]
        );

        // Pastikan Header RKA juga terbuat
        \App\Models\Rka\Rka::updateOrCreate(
            ['kak_id' => $kak->id],
            ['status_anggaran' => 'draft']
        );
    }

    return back()->with('success', count($kaks) . ' data KAK berhasil disinkronkan ke Modul RKA.');
}

// app/Http/Controllers/Web/RkaController.php

public function pilihKak()
{
    $user = Auth::user();
    if ($user->peran == 'sekretariat') {
        return redirect()->route('verifikasi.index');
    }

// Ambil data lokal untuk cek mana yang sudah ditarik
    $localKaks = \App\Models\Rka\RkaPerencanaan::all();

    // Ambil semua KAK yang disetujui dari database perencanaan
    $listKak = \App\Models\Kak\Kak::on('modul_kak')
                  ->with('pohonKinerja')
                  ->where('status', 2) 
                  ->get();

    return view('rka.pilih_kak', compact('localKaks', 'listKak'));
}

    // 2. Halaman Belanja (Level 2)
    // app/Http/Controllers/Web/RkaController.php

public function index($kak_id)
{
    /** * SEKARANG: Ambil data dari tabel lokal rka_perencanaan, bukan dari database KAK.
     * Ini memastikan Modul RKA bekerja secara mandiri.
     */
    $kak = \App\Models\Rka\RkaPerencanaan::where('kak_id', $kak_id)->firstOrFail();

    /** * Karena rincian_belanja (KakDetail) memang berada di database modul_anggaran, 
     * kita bisa langsung mengambilnya lewat relasi atau query manual.
     */
    $rincian = \App\Models\Rka\KakDetail::where('kak_id', $kak_id)->get();

    // Ambil katalog SSH untuk keperluan input belanja
    $katalog = \App\Models\Rka\MasterSsh::orderBy('nama_barang', 'asc')->get();
    
    return view('rka.index', compact('kak', 'rincian', 'katalog'));
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
    // app/Http/Controllers/Web/RkaController.php


    /**
     * Kunci RKA dan Kirim ke Sekretariat
     */
    // app/Http/Controllers/Web/RkaController.php

public function finalisasi($kak_id)
{
    // 1. Ambil data dari tabel lokal rka_perencanaan
    $kak = \App\Models\Rka\RkaPerencanaan::where('kak_id', $kak_id)->firstOrFail();

    /** * 2. PERBAIKAN VALIDASI:
     * Cukup cek apakah ada rincian belanja yang sudah diinput.
     * Kita tidak perlu filter 'status_anggaran' di sini karena kolom itu tidak ada di kak_details.
     */
    $itemCount = $kak->rincianBelanja()->count();

    if ($itemCount == 0) {
        return back()->with('error', 'Gagal Finalisasi! Anda belum menginput rincian belanja sama sekali.');
    }

    /**
     * 3. Update Status Internal di Modul Anggaran (Tabel Lokal)
     * Ini akan mengirim data ke verifikasi Sekretariat.
     */
    $kak->update([
        'status_internal' => 'pengajuan',
        'updated_at' => now(),
    ]);

    // Update juga di tabel rka_main agar statusnya sinkron
    \App\Models\Rka\Rka::where('kak_id', $kak_id)->update([
        'status_anggaran' => 'pengajuan'
    ]);

    return redirect()->route('rka.pilih_kak')->with('success', 'RKA berhasil diajukan ke Sekretariat.');
}

// app/Http/Controllers/Web/RkaController.php

public function cetak($kak_id)
{
    // 1. Ambil data Header RKA dari rka_main
    // Kita gunakan rka_id yang sudah dibinding di RkaVerifikasiController
    $rka = \App\Models\Rka\Rka::where('kak_id', $kak_id)->firstOrFail();
    
    // 2. Ambil rincian belanja yang sudah mengakar ke rka_id tersebut
    $rincian = \App\Models\Rka\KakDetail::where('rka_id', $rka->id)->get();
    
    // 3. Ambil informasi KAK dari tabel mirror lokal
    $kak = \App\Models\Rka\RkaPerencanaan::where('kak_id', $kak_id)->firstOrFail();

    return view('rka.cetak', compact('rka', 'rincian', 'kak'));
}
}