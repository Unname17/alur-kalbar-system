<?php

namespace App\Http\Controllers\Web; // Sesuaikan dengan folder fisiknya
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kinerja\PohonKinerja;
use App\Models\Kak\Kak;
use App\Models\Kak\KakTimPelaksana;
use Illuminate\Support\Facades\DB;

class KakController extends Controller
{
    public function index()
{
    // Mengambil data Sub Kegiatan dari Modul Kinerja (Database alur_kalbar_kinerja)
    // untuk ditampilkan sebagai daftar KAK
    $listSubKegiatan = \App\Models\Kinerja\PohonKinerja::where('jenis_kinerja', 'sub_kegiatan')
                        ->with('kak') // Relasi ke tabel kak di DB alur_kalbar_kak
                        ->get();

    return view('kak.index', compact('listSubKegiatan'));
}

    public function create($pohon_kinerja_id)
    {
        // Ambil data Sub Kegiatan dari database alur_kalbar_kinerja
        $subKegiatan = PohonKinerja::with('indikators')->findOrFail($pohon_kinerja_id);

        return view('kak.create', compact('subKegiatan'));
    }

    public function store(Request $request)
{
    $request->validate([
        'pohon_kinerja_id' => 'required',
        'judul_kak' => 'required',
    ]);

    try {
        DB::connection('modul_kak')->beginTransaction();

        // --- BAGIAN YANG DIUBAH ---
        // Ambil semua input, lalu paksa status menjadi 1 (Menunggu Verifikasi)
        $inputData = $request->only([
            'pohon_kinerja_id', 'judul_kak', 'kode_proyek', 'dasar_hukum',
            'latar_belakang', 'maksud_tujuan', 'sasaran', 'metode_pelaksanaan',
            'lokasi', 'penerima_manfaat', 'waktu_mulai', 'waktu_selesai'
        ]);
        $inputData['status'] = 1; // Paksa jadi status verifikasi

        $kak = Kak::create($inputData);
        // --------------------------

        if ($request->has('nama_personil')) {
            foreach ($request->nama_personil as $key => $nama) {
                // Tambahkan pengecekan agar tidak menyimpan baris kosong
                if (!empty($nama)) {
                    KakTimPelaksana::create([
                        'kak_id' => $kak->id,
                        'nama_personil' => $nama,
                        'peran_dalam_tim' => $request->peran_dalam_tim[$key],
                        'nip' => $request->nip[$key],
                    ]);
                }
            }
        }

        DB::connection('modul_kak')->commit();
        
        // Ubah redirect ke index agar langsung melihat status "Menunggu Verifikasi"
        return redirect()->route('kak.index')->with('success', 'KAK Berhasil disusun dan diajukan.');

    } catch (\Exception $e) {
        DB::connection('modul_kak')->rollBack();
        return back()->with('error', 'Gagal simpan: ' . $e->getMessage());
    }
}

    public function show($id)
{
    // Mengambil data KAK beserta tim pelaksananya
    // Dan tetap menarik data Sub Kegiatan sebagai referensi
    $kak = \App\Models\Kak\Kak::with(['timPelaksana', 'pohonKinerja'])->findOrFail($id);

    return view('kak.show', compact('kak'));
}

    public function verifikasi(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:2,3',
        'catatan_sekretariat' => 'nullable|string'
    ]);

    $kak = \App\Models\Kak\Kak::findOrFail($id);
    
    $updateData = [
        'status' => $request->status,
        'catatan_sekretariat' => $request->catatan_sekretariat
    ];

    // Jika disetujui dan belum punya nomor, generate nomor otomatis
    if ($request->status == 2 && empty($kak->nomor_kak)) {
        $tahun = date('Y');
        $count = \App\Models\Kak\Kak::whereYear('created_at', $tahun)->whereNotNull('nomor_kak')->count() + 1;
        $nomorUrut = str_pad($count, 3, '0', STR_PAD_LEFT);
        
        // Format: 001/ALUR-KALBAR/KAK/2025
        $updateData['nomor_kak'] = "{$nomorUrut}/ALUR-KALBAR/KAK/{$tahun}";
    }

    $kak->update($updateData);

    return redirect()->route('kak.index')->with('success', 'Verifikasi berhasil. Nomor KAK: ' . ($updateData['nomor_kak'] ?? 'N/A'));
}

public function cetakPdf($id)
{
    $kak = \App\Models\Kak\Kak::with(['timPelaksana', 'pohonKinerja'])->findOrFail($id);
    $pdf = \PDF::loadView('kak.print_pdf', compact('kak'))->setPaper('a4', 'portrait');
    
    return $pdf->stream("KAK-{$kak->id}.pdf");
}

// Menampilkan Form Edit
public function edit($id)
{
    // Ambil data KAK beserta relasi tim dan data Sub Kegiatan dari DB Kinerja
    $kak = \App\Models\Kak\Kak::with(['timPelaksana', 'pohonKinerja'])->findOrFail($id);
    
    // Pastikan Sub Kegiatan juga membawa indikator untuk panduan user
    $subKegiatan = \App\Models\Kinerja\PohonKinerja::with('indikators')->findOrFail($kak->pohon_kinerja_id);

    return view('kak.edit', compact('kak', 'subKegiatan'));
}

// Memproses Pembaruan Data
public function update(Request $request, $id)
{
    $request->validate([
        'judul_kak' => 'required|string|max:255',
        'latar_belakang' => 'required',
    ]);

    try {
        \DB::connection('modul_kak')->beginTransaction();

        $kak = \App\Models\Kak\Kak::findOrFail($id);
        
        // Update data utama KAK
        // Status dikembalikan ke 1 (Menunggu Verifikasi) setiap kali ada perbaikan
        $kak->update(array_merge($request->all(), ['status' => 1]));

        // Update Tim Pelaksana (Hapus lama, simpan baru)
        \App\Models\Kak\KakTimPelaksana::where('kak_id', $id)->delete();
        
        if ($request->has('nama_personil')) {
            foreach ($request->nama_personil as $key => $nama) {
                if(!empty($nama)) {
                    \App\Models\Kak\KakTimPelaksana::create([
                        'kak_id' => $kak->id,
                        'nama_personil' => $nama,
                        'nip' => $request->nip[$key],
                        'peran_dalam_tim' => $request->peran_dalam_tim[$key],
                    ]);
                }
            }
        }

        \DB::connection('modul_kak')->commit();
        return redirect()->route('kak.index')->with('success', 'KAK berhasil diperbarui dan diajukan kembali.');

    } catch (\Exception $e) {
        \DB::connection('modul_kak')->rollBack();
        return back()->with('error', 'Gagal memperbarui: ' . $e->getMessage());
    }
}
}