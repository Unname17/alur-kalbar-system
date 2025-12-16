<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

// PASTIKAN PATH INI SESUAI DENGAN STRUKTUR MODEL ANDA
use App\Models\Admin\PerangkatDaerah;
use App\Models\Admin\Pengguna;
use App\Models\Kinerja\PohonKinerja;
use App\Models\Kinerja\IndikatorKinerja;
use App\Models\Kinerja\TargetPeriode;

class SimulasiDataSeeder extends Seeder
{
    // ID yang akan digunakan di Seeder
    protected $idKepala = 2; 
    protected $idStaf = 3;
    protected $idOpd = 1; 

    public function run()
    {
        echo "🚀 Memulai Seeding Simulasi Data Pohon Kinerja (Sesuai PDF).\n";
        $this->bersihkanSemuaData(); // Selalu bersihkan dulu

        // ==============================================================
        // 1. ISI DATABASE ADMIN (Koneksi: sistem_admin)
        // ==============================================================
        
        $opd = PerangkatDaerah::create([
            'nama_perangkat_daerah' => 'Dinas Kominfo Prov. Kalbar',
            'kode_unit' => '2.10.01',
            'singkatan' => 'DISKOMINFO',
            'status_input' => 'buka' // Kolom ini sekarang sudah ada di migrasi Admin
        ]);

        $kadis = Pengguna::create([
            'id_perangkat_daerah' => $opd->id,
            'nama_lengkap' => 'Bapak Kepala Dinas',
            'nip' => '19800101',
            'kata_sandi' => Hash::make('password'),
            'peran' => 'kepala_dinas',
            'status_input' => 'buka' // Kolom ini sekarang sudah ada di migrasi Pengguna
        ]);
        $staf = Pengguna::create([
            'id_perangkat_daerah' => $opd->id,
            'nama_lengkap' => 'Mahasiswa Magang (Tim RPL)',
            'nip' => 'MHS001',
            'kata_sandi' => Hash::make('password'),
            'peran' => 'staf',
            'status_input' => 'buka'
        ]);

        $this->idKepala = $kadis->id;
        $this->idStaf = $staf->id;
        $this->idOpd = $opd->id;

        // ==============================================================
        // 2. ISI DATABASE KINERJA (Koneksi: modul_kinerja)
        // ==============================================================
        
        // LEVEL 1: VISI & MISI GUBERNUR (AKAR)
        $visi = $this->createNode(null, 'Visi Gubernur: Terwujudnya Tata Kelola Pemerintahan Berkualitas', 'visi', $this->idKepala);
        $misi = $this->createNode($visi->id, 'Misi 1: Mewujudkan Reformasi Birokrasi dan SPBE', 'misi', $this->idKepala);

        // LEVEL 2: SASARAN STRATEGIS (HIJAU)
        $ss = $this->createNode($misi->id, 'Mewujudkan Tata Kelola Pemerintahan Berbasis Elektronik yang Efektif', 'sasaran_strategis', $this->idKepala, [
            ['Indeks SPBE Provinsi', 3.5, 'Indeks']
        ]);

        // LEVEL 3: SASARAN OPD (BIRU)
        $sasaranA = $this->createNode($ss->id, 'Meningkatnya Kualitas Layanan Informasi Publik', 'sasaran', $this->idKepala, [
            ['Indeks Keterbukaan Informasi Publik (IKIP)', 75, 'Poin']
        ]);
        $sasaranB = $this->createNode($ss->id, 'Meningkatnya Penerapan SPBE', 'sasaran', $this->idKepala, [
            ['Persentase Layanan Publik Online Terintegrasi', 90, '%']
        ]);

        // LEVEL 4: PROGRAM (KUNING)
        $progKom = $this->createNode($sasaranA->id, 'Program Pengelolaan Informasi dan Komunikasi Publik', 'program', $this->idKepala, [
            ['Jumlah Kanal Komunikasi Aktif', 5, 'Kanal']
        ]);
        $progApt = $this->createNode($sasaranB->id, 'Program Aplikasi Informatika', 'program', $this->idKepala, [
            ['Jumlah Sistem Elektronik Terdaftar', 50, 'Sistem']
        ]);

        // LEVEL 5: KEGIATAN (ORANYE) - PJ Staf Magang (ID 3)
        $this->createNode($progKom->id, 'Pengelolaan Konten dan Media Komunikasi Publik', 'kegiatan', $this->idStaf, [
            ['Jumlah Konten Berita Dipublikasikan', 300, 'Konten'],
            ['Status validasi kontem (Contoh Status Draft)', 10, 'Dokumen']
        ]);
        
        $this->createNode($progApt->id, 'Pengembangan Ekosistem Smart Province', 'kegiatan', $this->idStaf, [
            ['Jumlah Modul Smart Province', 10, 'Modul']
        ]);

        echo "✅ SEEDER SELESAI! Data Admin & Kinerja Kompleks berhasil dibuat.\n";
    }

    // ====================================================================
    // HELPER FUNCTIONS (BLOK KRITIS UNTUK PEMBERSIHAN DATA)
    // ====================================================================

    private function bersihkanSemuaData()
    {
        echo "   [i] Membersihkan data lama menggunakan DELETE FROM...\n";
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // A. KOSONGKAN TABEL ADMIN (Menggunakan DELETE)
        DB::connection('sistem_admin')->table('pengguna')->delete();
        DB::connection('sistem_admin')->table('perangkat_daerah')->delete();
        
        // B. KOSONGKAN TABEL KINERJA (Menggunakan DELETE - Anti Foreign Key Error)
        try {
            // Ini mungkin gagal jika tabel belum dibuat, jadi kita bungkus try/catch
            DB::connection('modul_kinerja')->table('realisasi_kinerja')->delete();
            DB::connection('modul_kinerja')->table('analisis_kinerja')->delete(); 
        } catch (\Exception $e) {
            echo "   [!] Peringatan: Gagal DELETE dari tabel realisasi/analisis. Lanjut.\n";
        }

        DB::connection('modul_kinerja')->table('target_periode')->delete();
        DB::connection('modul_kinerja')->table('indikator_kinerja')->delete();
        DB::connection('modul_kinerja')->table('pohon_kinerja')->delete();

        // MENGATUR ULANG AUTO INCREMENT (Wajib)
        DB::connection('sistem_admin')->statement('ALTER TABLE `pengguna` AUTO_INCREMENT = 1');
        DB::connection('sistem_admin')->statement('ALTER TABLE `perangkat_daerah` AUTO_INCREMENT = 1');
        DB::connection('modul_kinerja')->statement('ALTER TABLE `pohon_kinerja` AUTO_INCREMENT = 1');
        DB::connection('modul_kinerja')->statement('ALTER TABLE `indikator_kinerja` AUTO_INCREMENT = 1'); 
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function createNode($parentId, $nama, $jenis, $userId, $indikators = [], $status = 'disetujui')
    {
        $node = PohonKinerja::create([
            'id_induk' => $parentId,
            'id_perangkat_daerah' => $this->idOpd,
            'id_penanggung_jawab' => $userId,
            'nama_kinerja' => $nama,
            'jenis_kinerja' => $jenis,
            'status_validasi' => $status
        ]);

        foreach ($indikators as $ind) {
            $indikatorBaru = IndikatorKinerja::create([
                'id_pohon_kinerja' => $node->id,
                'tolok_ukur' => $ind[0],
                'target_tahunan' => $ind[1],
                'satuan' => $ind[2],
                'pagu_anggaran' => rand(50000000, 200000000) 
            ]);

            $this->bagiTargetKeTriwulan($indikatorBaru->id, $ind[1], $ind[2]);
        }

        return $node;
    }

    private function bagiTargetKeTriwulan($indikatorId, $targetTahunan, $satuan)
    {
        $targetPerTw = $targetTahunan / 4;
        for ($i = 1; $i <= 4; $i++) {
            TargetPeriode::create([
                'id_indikator_kinerja' => $indikatorId,
                'periode' => 'TW ' . $i,
                'target' => $targetPerTw,
                'satuan' => $satuan // Kolom ini sekarang sudah ada di migrasi TargetPeriode
            ]);
        }
    }
}