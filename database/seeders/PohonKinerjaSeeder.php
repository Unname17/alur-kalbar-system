<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kinerja\PohonKinerja;
use Illuminate\Support\Facades\DB;

class PohonKinerjaSeeder extends Seeder
{
    public function run()
    {
        // ==========================================
        // KONFIGURASI AWAL
        // ==========================================
        $opdId = 5;  // ID Diskominfo (Sesuaikan dengan data user Anda)
        $userId = 1; // ID Admin/Kadis yang menginput data awal

        // 1. BERSIHKAN DATABASE (Reset Total)
        // Kita matikan Foreign Key Check dulu agar bisa truncate tabel induk
        DB::connection('modul_kinerja')->statement('SET FOREIGN_KEY_CHECKS=0;');
        
        PohonKinerja::on('modul_kinerja')->truncate(); // Hapus Pohon
        DB::connection('modul_kinerja')->table('indikator_kinerja')->truncate(); // Hapus Indikator
        DB::connection('modul_kinerja')->table('akses_penambahan_kinerja')->truncate(); // Hapus Rule Akses
        
        DB::connection('modul_kinerja')->statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::beginTransaction();

        try {
            $this->command->info('Memulai Seeding Pohon Kinerja...');

            // ==========================================================
            // LEVEL 0: VISI & MISI GUBERNUR (AKAR POHON)
            // ==========================================================
            $visi = $this->createNode(null, 'Terwujudnya Masyarakat Kalimantan Barat yang Sejahtera dan Berdaya Saing', 'visi', $opdId, $userId);
            
            $misi = $this->createNode($visi->id, 'Mewujudkan Tata Kelola Pemerintahan yang Berkualitas dan Inovatif (Smart Province)', 'misi', $opdId, $userId);

            // ==========================================================
            // LEVEL 1: SASARAN OPD (ESELON II - KEPALA DINAS)
            // ==========================================================
            $sasaranUtama = $this->createNode($misi->id, 'Meningkatnya Kualitas Layanan Informasi dan Komunikasi Publik serta Tata Kelola SPBE', 'sasaran_opd', $opdId, $userId);
            
            $this->addIndikator($sasaranUtama, [
                ['Indeks SPBE', '3.5', 'Indeks'],
                ['Indeks Keterbukaan Informasi Publik', 'Sedang', 'Predikat'],
                ['Indeks Keamanan Informasi', 'Baik', 'Predikat']
            ]);

            // ==========================================================
            // BIDANG 1: IKP (INFORMASI KOMUNIKASI PUBLIK)
            // ==========================================================
            
            // PROGRAM IKP
            $progIKP = $this->createNode($sasaranUtama->id, 'PROGRAM PENGELOLAAN INFORMASI DAN KOMUNIKASI PUBLIK', 'program', $opdId, $userId);
            $this->addIndikator($progIKP, [
                ['Persentase tingkat kepuasan masyarakat terhadap akses info', '75', '%'],
                ['Persentase permohonan Informasi Publik diselesaikan', '100', '%']
            ]);

                // KEGIATAN 1: Pengelolaan Informasi Pemda
                $kegIKP1 = $this->createNode($progIKP->id, 'Pengelolaan Informasi dan Komunikasi Publik Pemda', 'kegiatan', $opdId, $userId);
                $this->addIndikator($kegIKP1, [
                    ['Jumlah isu dan opini publik yang diakomodir', '440', 'Opini'],
                    ['Persentase Kab/Kota yang membentuk PPID', '100', '%']
                ]);

                    // SUB KEGIATAN 1.1: Monitoring Opini
                    $subIKP1 = $this->createNode($kegIKP1->id, 'Monitoring Opini dan Aspirasi Publik', 'sub_kegiatan', $opdId, $userId, 141499000, 'Bidang IKP');
                    $this->addIndikator($subIKP1, [['Jumlah Dokumen Hasil Monitoring', '1', 'Dokumen']]);

                    // SUB KEGIATAN 1.2: Pelayanan Informasi
                    $subIKP2 = $this->createNode($kegIKP1->id, 'Pelayanan Informasi Publik', 'sub_kegiatan', $opdId, $userId, 361298000, 'Bidang IKP');
                    $this->addIndikator($subIKP2, [['Jumlah Dokumen Hasil Pelayanan', '1', 'Dokumen']]);

                // KEGIATAN 2: Humas & Kemitraan
                $kegIKP2 = $this->createNode($progIKP->id, 'Penyelenggaraan Hubungan Masyarakat, Media dan Kemitraan', 'kegiatan', $opdId, $userId);
                $this->addIndikator($kegIKP2, [
                    ['Jumlah Dokumen Kemitraan dengan Masyarakat', '1', 'Dokumen'],
                    ['Jumlah Media Komunikasi Publik milik Pemda', '2', 'Media'],
                    ['Jumlah Konten Informasi Publik (Kesehatan/Ekonomi)', '3', 'Konten'],
                    ['Persentase Khalayak terpapar informasi', '75', '%'],
                    ['Jumlah Strategi Komunikasi disusun', '1', 'Dokumen'],
                    ['Jumlah ASN Komunikasi ikut bimtek', '160', 'Orang']
                ]);

            // ==========================================================
            // BIDANG 2: APTIKA (APLIKASI INFORMATIKA)
            // ==========================================================
            
            // PROGRAM APTIKA
            $progAptika = $this->createNode($sasaranUtama->id, 'PROGRAM PENGELOLAAN APLIKASI INFORMATIKA', 'program', $opdId, $userId);
            $this->addIndikator($progAptika, [
                ['Persentase total bobot domain evaluasi SPBE', '73.5', 'Indeks'],
                ['Persentase pengelolaan Nama Domain Pemda', '100', '%']
            ]);

                // KEGIATAN: Pengelolaan e-Government
                $kegApt1 = $this->createNode($progAptika->id, 'Pengelolaan e-Government Di Lingkup Pemda', 'kegiatan', $opdId, $userId);
                $this->addIndikator($kegApt1, [['Persentase pengelolaan e-gov', '100', '%']]);

                    // SUB KEGIATAN: Pengembangan Aplikasi
                    $subApt1 = $this->createNode($kegApt1->id, 'Pengembangan Aplikasi dan Proses Bisnis Pemerintahan', 'sub_kegiatan', $opdId, $userId, 1108203400, 'Bidang Aptika');
                    $this->addIndikator($subApt1, [
                        ['Jumlah Aplikasi dan Proses Bisnis dikembangkan', '11', 'Unit']
                    ]);

            // ==========================================================
            // BIDANG 3: STATISTIK SEKTORAL
            // ==========================================================
            
            // PROGRAM STATISTIK
            $progStat = $this->createNode($sasaranUtama->id, 'PROGRAM PENYELENGGARAAN STATISTIK SEKTORAL', 'program', $opdId, $userId);
            $this->addIndikator($progStat, [['Jumlah Perangkat Daerah yang menyelenggarakan statistik', '35', 'PD']]);

                // KEGIATAN: Penyelenggaraan Statistik
                $kegStat = $this->createNode($progStat->id, 'Penyelenggaraan Statistik Sektoral di Daerah', 'kegiatan', $opdId, $userId);
                
                    // SUB KEGIATAN
                    $subStat1 = $this->createNode($kegStat->id, 'Membangun Metadata Statistik Sektoral', 'sub_kegiatan', $opdId, $userId, 63258100, 'Bidang Statistik');
                    $this->addIndikator($subStat1, [['Jumlah Metadata Statistik dihimpun', '1', 'Dokumen']]);

            // ==========================================================
            // SETUP RULES AKSES (WHITELIST INPUT)
            // ==========================================================
            $this->command->info('Membuat Rule Akses Input (Whitelist)...');
            
            // 1. Beri Izin OPD Input KEGIATAN di bawah Program IKP
            DB::connection('modul_kinerja')->table('akses_penambahan_kinerja')->insert([
                'opd_id' => $opdId,
                'role_target' => 'opd',
                'parent_id_allowed' => $progIKP->id,
                'jenis_kinerja_allowed' => 'kegiatan',
                'is_active' => true,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

             // 2. Beri Izin OPD Input SUB KEGIATAN di bawah Kegiatan IKP 1
             DB::connection('modul_kinerja')->table('akses_penambahan_kinerja')->insert([
                'opd_id' => $opdId,
                'role_target' => 'opd',
                'parent_id_allowed' => $kegIKP1->id, 
                'jenis_kinerja_allowed' => 'sub_kegiatan', 
                'is_active' => true,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3. Beri Izin OPD Input KEGIATAN di bawah Program Aptika
            DB::connection('modul_kinerja')->table('akses_penambahan_kinerja')->insert([
                'opd_id' => $opdId,
                'role_target' => 'opd',
                'parent_id_allowed' => $progAptika->id, 
                'jenis_kinerja_allowed' => 'kegiatan', 
                'is_active' => true,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            $this->command->info('SUKSES! Data Pohon Kinerja, Indikator, dan Hak Akses berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('GAGAL: ' . $e->getMessage());
        }
    }

    /**
     * HELPER: Membuat Node Pohon Kinerja
     */
    private function createNode($parentId, $nama, $jenis, $opdId, $userId, $anggaran = 0, $pj = null, $status = 'disetujui')
    {
        return PohonKinerja::create([
            'parent_id' => $parentId,
            'nama_kinerja' => $nama,
            'jenis_kinerja' => $jenis,
            'opd_id' => $opdId,
            'created_by' => $userId,
            'anggaran' => $anggaran, // Otomatis masuk jika ada nilainya, null jika 0
            'penanggung_jawab' => $pj,
            'status' => $status
        ]);
    }

    /**
     * HELPER: Menambahkan Banyak Indikator ke Node
     * Format array: [['Nama Indikator', 'Target', 'Satuan'], ...]
     */
    private function addIndikator($node, $indikators)
    {
        foreach ($indikators as $ind) {
            $node->indikators()->create([
                'indikator' => $ind[0],
                'target'    => $ind[1],
                'satuan'    => $ind[2]
            ]);
        }
    }
}