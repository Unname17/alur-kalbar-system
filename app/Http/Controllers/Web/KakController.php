<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kinerja\PohonKinerja;
use App\Models\Kak\Kak;
use App\Models\Kak\KakTimPelaksana;
use Illuminate\Support\Facades\DB;

class KakController extends Controller
{
    /**
     * Menampilkan daftar Sub Kegiatan untuk disusun KAK-nya.
     */
    // app/Http/Controllers/Web/KakController.php

public function index()
{
    // Mengambil Sub Kegiatan beserta Rencana Aksi di bawahnya
    $listSubKegiatan = \App\Models\Kinerja\PohonKinerja::where('jenis_kinerja', 'sub_kegiatan')
                        ->with(['children' => function($query) {
                            $query->where('jenis_kinerja', 'rencana_aksi')->with('kak');
                        }]) 
                        ->get();

    return view('kak.index', compact('listSubKegiatan'));
}

// app/Http/Controllers/Web/KakController.php

public function storeTimeline(Request $request, $id)
{
    try {
        DB::connection('modul_kak')->beginTransaction();

        // Hapus jadwal lama agar bisa diganti dengan yang baru
        \App\Models\Kak\KakTimeline::where('kak_id', $id)->delete();

        if ($request->has('nama_tahapan')) {
            foreach ($request->nama_tahapan as $key => $nama) {
                if (!empty($nama)) {
                    $data = [
                        'kak_id' => $id,
                        'nama_tahapan' => $nama,
                    ];

                    // Loop untuk mengisi bulan b1 sampai b12
                    for ($i = 1; $i <= 12; $i++) {
                        $data['b' . $i] = isset($request->{"b$i"}[$key]) ? 1 : 0;
                    }

                    \App\Models\Kak\KakTimeline::create($data);
                }
            }
        }

        DB::connection('modul_kak')->commit();
        return back()->with('success', 'Jadwal pelaksanaan berhasil diperbarui.');

    } catch (\Exception $e) {
        DB::connection('modul_kak')->rollBack();
        return back()->with('error', 'Gagal simpan jadwal: ' . $e->getMessage());
    }
}

public function create($pohon_kinerja_id)
{
$subKegiatan = PohonKinerja::with(['indikators', 'children'])
                    ->findOrFail($pohon_kinerja_id);

    // Kirim dengan nama subKegiatan
    return view('kak.create', compact('subKegiatan'));
}

    /**
     * Menyimpan dokumen KAK baru ke database modul_kak.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pohon_kinerja_id' => 'required',
            'judul_kak' => 'required',
        ]);

        try {
            DB::connection('modul_kak')->beginTransaction();

            // Status otomatis diset ke 1 (Menunggu Verifikasi) saat pertama kali diajukan
            $inputData = $request->only([
                'pohon_kinerja_id', 'judul_kak', 'kode_proyek', 'dasar_hukum',
                'latar_belakang', 'maksud_tujuan', 'sasaran', 'metode_pelaksanaan',
                'lokasi', 'penerima_manfaat', 'waktu_mulai', 'waktu_selesai'
            ]);
            $inputData['status'] = 1; 

            $kak = Kak::create($inputData);

            // Simpan Tim Pelaksana jika ada input
            if ($request->has('nama_personil')) {
                foreach ($request->nama_personil as $key => $nama) {
                    if (!empty($nama)) {
                        KakTimPelaksana::create([
                            'kak_id' => $kak->id,
                            'nama_personil' => $nama,
                            'peran_dalam_tim' => $request->peran_dalam_tim[$key] ?? 'Anggota',
                            'nip' => $request->nip[$key] ?? null,
                        ]);
                    }
                }
            }

            DB::connection('modul_kak')->commit();
            return redirect()->route('kak.index')->with('success', 'KAK Berhasil disusun dan diajukan.');

        } catch (\Exception $e) {
            DB::connection('modul_kak')->rollBack();
            return back()->with('error', 'Gagal simpan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail dokumen KAK (Pratinjau Cetak).
     */
    public function show($id)
    {
        // Menarik data KAK beserta tim dan referensi pohon kinerjanya
        $kak = Kak::with(['timPelaksana', 'pohonKinerja.indikators', 'timelines'])->findOrFail($id);

        return view('kak.show', compact('kak'));
    }

    /**
     * Proses Verifikasi oleh Sekretariat/Bappeda.
     */
    public function verifikasi(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:2,3', // 2: Setuju, 3: Tolak
            'catatan_sekretariat' => 'nullable|string'
        ]);

        $kak = Kak::findOrFail($id);
        
        $updateData = [
            'status' => $request->status,
            'catatan_sekretariat' => $request->catatan_sekretariat
        ];

        // Penomoran otomatis saat dokumen disetujui
        if ($request->status == 2 && empty($kak->nomor_kak)) {
            $tahun = date('Y');
            $count = Kak::whereYear('created_at', $tahun)->whereNotNull('nomor_kak')->count() + 1;
            $nomorUrut = str_pad($count, 3, '0', STR_PAD_LEFT);
            $updateData['nomor_kak'] = "{$nomorUrut}/ALUR-KALBAR/KAK/{$tahun}";
        }

        $kak->update($updateData);

        return redirect()->route('kak.index')->with('success', 'Verifikasi berhasil diperbarui.');
    }

    /**
     * Menampilkan form perbaikan (Edit).
     */
    public function edit($id)
    {
        $kak = Kak::with(['timPelaksana', 'pohonKinerja.indikators'])->findOrFail($id);
        $subKegiatan = $kak->pohonKinerja;

        return view('kak.edit', compact('kak', 'subKegiatan'));
    }

    /**
     * Memproses pembaruan data (Update).
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'judul_kak' => 'required|string|max:255',
            'latar_belakang' => 'required',
        ]);

        try {
            DB::connection('modul_kak')->beginTransaction();

            $kak = Kak::findOrFail($id);
            
            // Status kembali ke 1 (Menunggu Verifikasi) setelah diperbaiki
            $kak->update(array_merge($request->all(), ['status' => 1]));

            // Refresh data Tim Pelaksana
            KakTimPelaksana::where('kak_id', $id)->delete();
            if ($request->has('nama_personil')) {
                foreach ($request->nama_personil as $key => $nama) {
                    if(!empty($nama)) {
                        KakTimPelaksana::create([
                            'kak_id' => $kak->id,
                            'nama_personil' => $nama,
                            'nip' => $request->nip[$key] ?? null,
                            'peran_dalam_tim' => $request->peran_dalam_tim[$key] ?? 'Anggota',
                        ]);
                    }
                }
            }

            DB::connection('modul_kak')->commit();
            return redirect()->route('kak.index')->with('success', 'Perubahan berhasil diajukan kembali.');

        } catch (\Exception $e) {
            DB::connection('modul_kak')->rollBack();
            return back()->with('error', 'Gagal memperbarui: ' . $e->getMessage());
        }
    }
}