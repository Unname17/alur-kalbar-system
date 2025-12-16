<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kinerja\PohonKinerja;
use App\Models\Kinerja\DetailProgram;
use App\Models\Kinerja\DetailKegiatan;
use App\Models\Kinerja\DetailSubKegiatan;
use Illuminate\Support\Facades\DB;

class PohonKinerjaSeeder extends Seeder
{
    public function run()
    {
        // Konstanta
        $idDiskominfo = 5;
        $idAdminAwal = 1; // Asumsi ID user yang membuat data awal/resmi adalah 1 (Admin/Kadis)

        // 1. Reset Database (PENTING: Gunakan koneksi yang benar)
        DB::connection('modul_kinerja')->statement('SET FOREIGN_KEY_CHECKS=0;');
        PohonKinerja::on('modul_kinerja')->truncate();
        DetailProgram::on('modul_kinerja')->truncate();
        DetailKegiatan::on('modul_kinerja')->truncate();
        DetailSubKegiatan::on('modul_kinerja')->truncate();
        DB::connection('modul_kinerja')->statement('SET FOREIGN_KEY_CHECKS=1;');

        // ==========================================================
        // LEVEL 0 & 1: ROOT (HIJAU) & INDUK DINAS (BIRU TUA)
        // ==========================================================
        $sasaranStrategis = $this->createNode('Mewujudkan Tata Kelola Pemerintahan Berbasis Elektronik yang Efektif dan Terintegrasi', 'sasaran_daerah', null, null, 'disetujui', $idAdminAwal);

        $sasaranUtama = $this->createNode('Meningkatkan Tata Kelola Sistem Pemerintahan Berbasis Elektronik Yang Aman dan Terintegrasi', 'sasaran_opd', $sasaranStrategis->id, $idDiskominfo, 'disetujui', $idAdminAwal);

        // ==========================================================
        // BIDANG 1: IKP (INFORMASI KOMUNIKASI PUBLIK)
        // ==========================================================
        $sasaranIKP = $this->createNode('Meningkatnya Kualitas Layanan Informasi Publik', 'sasaran_opd', $sasaranUtama->id, $idDiskominfo, 'disetujui', $idAdminAwal);

            // --- CROSS CUTTING (MERAH) ---
            $this->createCrossCutting($sasaranIKP->id, 'KOMISI INFORMASI', $idDiskominfo, $idAdminAwal);
            $this->createCrossCutting($sasaranIKP->id, 'KOMISI PENYIARAN INDONESIA DAERAH', $idDiskominfo, $idAdminAwal);

            // PROGRAM
            $progIKP = $this->createNode('Program Pengelolaan Informasi dan Komunikasi Publik', 'program', $sasaranIKP->id, $idDiskominfo, 'disetujui', $idAdminAwal);
            DetailProgram::create(['pohon_id' => $progIKP->id, 'sasaran_program' => 'Terselenggaranya pengelolaan informasi publik', 'indikator_program' => 'Persentase kepuasan masyarakat terhadap akses informasi', 'target_program' => '75', 'satuan_target' => 'Persen']);

                // KEGIATAN
                $keg1 = $this->createNode('Pengelolaan Informasi dan Komunikasi Publik Pemda', 'kegiatan', $progIKP->id, $idDiskominfo, 'disetujui', $idAdminAwal);
                DetailKegiatan::create(['pohon_id' => $keg1->id, 'indikator_kegiatan' => 'Persentase Kabupaten/Kota yang membentuk PPID', 'target_kegiatan' => '100', 'satuan_target' => 'Persen']);

                    // SUB KEGIATAN (DETAIL ANGGARAN DARI TABEL PDF HAL 2)
                    $this->createSub($keg1->id, 'Monitoring Opini dan Aspirasi Publik', 'Jumlah Dokumen Monitoring', '1', 'Dokumen', 141499000, $idDiskominfo, $idAdminAwal);
                    $this->createSub($keg1->id, 'Pelayanan Informasi Publik', 'Jumlah Dokumen Pelayanan', '1', 'Dokumen', 361298000, $idDiskominfo, $idAdminAwal);
                    
        // --- CONTOH NODE PENGAJUAN (DRAFT/PENDING) ---
        // Node ini HANYA akan tampil jika created_by = Auth::id() ATAU jika Anda login sebagai Admin/Kabid
        $this->createSub($keg1->id, 'Pengajuan Kegiatan Baru (Revisi)', 'Target Pengajuan Baru', '2', 'Dokumen', 50000000, $idDiskominfo, 2, 'ditolak', 'Anggaran tidak rasional. Revisi anggaran menjadi 50 juta.');
        $this->createSub($keg1->id, 'Pengajuan Kegiatan Baru (Menunggu)', 'Target Ajuan Pending', '1', 'Dokumen', 120000000, $idDiskominfo, 2, 'pengajuan');


        // ==========================================================
        // BIDANG 2: APTIKA (APLIKASI INFORMATIKA)
        // ==========================================================
        $sasaranAptika = $this->createNode('Meningkatnya Penerapan SPBE', 'sasaran_opd', $sasaranUtama->id, $idDiskominfo, 'disetujui', $idAdminAwal);

            $this->createCrossCutting($sasaranAptika->id, 'DINAS PERPUSTAKAAN DAN KEARSIPAN', $idDiskominfo, $idAdminAwal);
            $this->createCrossCutting($sasaranAptika->id, 'SELURUH PERANGKAT DAERAH (Implementasi SPBE)', $idDiskominfo, $idAdminAwal);

            $progAptika = $this->createNode('Program Aplikasi Informatika', 'program', $sasaranAptika->id, $idDiskominfo, 'disetujui', $idAdminAwal);
            DetailProgram::create(['pohon_id' => $progAptika->id, 'sasaran_program' => 'Implementasi Tata Kelola SPBE Optimal', 'indikator_program' => 'Indeks SPBE', 'target_program' => '3.5', 'satuan_target' => 'Indeks']);

                $kegApt1 = $this->createNode('Pengelolaan Nama Domain Pemerintah Daerah', 'kegiatan', $progAptika->id, $idDiskominfo, 'disetujui', $idAdminAwal);
                DetailKegiatan::create(['pohon_id' => $kegApt1->id, 'indikator_kegiatan' => 'Persentase Pengelolaan Domain', 'target_kegiatan' => '100', 'satuan_target' => 'Persen']);
                
                $kegApt2 = $this->createNode('Pengelolaan e-Government', 'kegiatan', $progAptika->id, $idDiskominfo, 'disetujui', $idAdminAwal);
                DetailKegiatan::create(['pohon_id' => $kegApt2->id, 'indikator_kegiatan' => 'Persentase Pengelolaan e-Gov', 'target_kegiatan' => '100', 'satuan_target' => 'Persen']);

                    $this->createSub($kegApt2->id, 'Pengembangan Aplikasi dan Proses Bisnis', 'Jumlah Aplikasi Dikembangkan', '11', 'Unit', 1108203400, $idDiskominfo, $idAdminAwal);
                    $this->createSub($kegApt2->id, 'Penyelenggaraan Sistem Penghubung Layanan', 'Jumlah Layanan Terhubung', '10', 'Layanan', 71381400, $idDiskominfo, $idAdminAwal);

        // ==========================================================
        // BIDANG 3: STATISTIK
        // ==========================================================
        $sasaranStat = $this->createNode('Meningkatnya Kualitas Data Statistik Sektoral', 'sasaran_opd', $sasaranUtama->id, $idDiskominfo, 'disetujui', $idAdminAwal);

            $this->createCrossCutting($sasaranStat->id, 'BAPPEDA & BPS (Badan Pusat Statistik)', $idDiskominfo, $idAdminAwal);

            $progStat = $this->createNode('Program Penyelenggaraan Statistik Sektoral', 'program', $sasaranStat->id, $idDiskominfo, 'disetujui', $idAdminAwal);
            DetailProgram::create(['pohon_id' => $progStat->id, 'sasaran_program' => 'Integrasi Data Statistik Sektoral', 'indikator_program' => 'Jml PD yang mendukung statistik', 'target_program' => '35', 'satuan_target' => 'PD']);

                $kegStat = $this->createNode('Penyelenggaraan Statistik Sektoral di Daerah', 'kegiatan', $progStat->id, $idDiskominfo, 'disetujui', $idAdminAwal);
                
                    $this->createSub($kegStat->id, 'Membangun Metadata Statistik Sektoral', 'Jumlah Metadata', '35', 'PD', 63258100, $idDiskominfo, $idAdminAwal);
                    $this->createSub($kegStat->id, 'Peningkatan Kapasitas SDM Statistik', 'Jumlah SDM Dilatih', '104', 'Orang', 98057976, $idDiskominfo, $idAdminAwal);

        // ==========================================================
        // BIDANG 4: PERSANDIAN
        // ==========================================================
        $sasaranSandi = $this->createNode('Meningkatnya Keamanan Informasi Pemda', 'sasaran_opd', $sasaranUtama->id, $idDiskominfo, 'disetujui', $idAdminAwal);

            $this->createCrossCutting($sasaranSandi->id, 'INSPEKTORAT, BIRO ORGANISASI, BKD', $idDiskominfo, $idAdminAwal);

            $progSandi = $this->createNode('Program Persandian untuk Keamanan Informasi', 'program', $sasaranSandi->id, $idDiskominfo, 'disetujui', $idAdminAwal);
            DetailProgram::create(['pohon_id' => $progSandi->id, 'sasaran_program' => 'Pelaksanaan Persandian Aman', 'indikator_program' => 'Indeks KAMI', 'target_program' => 'Baik', 'satuan_target' => 'Predikat']);

                $kegSandi = $this->createNode('Persandian untuk Pengamanan Informasi', 'kegiatan', $progSandi->id, $idDiskominfo, 'disetujui', $idAdminAwal);
                
                    $this->createSub($kegSandi->id, 'Pelaksanaan Keamanan Informasi (Pen-Test)', 'Jumlah Laporan', '1', 'Laporan', 81410340, $idDiskominfo, $idAdminAwal);
                    $this->createSub($kegSandi->id, 'Penyediaan Layanan Keamanan Informasi', 'Perangkat Daerah Terlayani', '35', 'PD', 225694590, $idDiskominfo, $idAdminAwal);
    }

    // --- HELPER FUNCTIONS ---
    private function createNode($nama, $jenis, $parentId, $opdId, $status = 'draft', $createdBy = null) {
        return PohonKinerja::on('modul_kinerja')->create([
            'nama_kinerja' => $nama, 
            'jenis_kinerja' => $jenis, 
            'parent_id' => $parentId, 
            'opd_id' => $opdId, 
            'status' => $status, // Gunakan status dinamis
            'created_by' => $createdBy
        ]);
    }

    private function createCrossCutting($parentId, $nama, $opdId, $createdBy) {
        // Trik: Tambahkan tag [CROSS_CUTTING] di nama agar dideteksi JS
        return PohonKinerja::on('modul_kinerja')->create([
            'nama_kinerja' => '[CROSS_CUTTING] ' . $nama,
            'jenis_kinerja' => 'program', // Levelnya disejajarkan dengan Program
            'parent_id' => $parentId,
            'opd_id' => $opdId,
            'status' => 'disetujui',
            'created_by' => $createdBy
        ]);
    }

    private function createSub($parentId, $nama, $ind, $trg, $sat, $ang, $opdId, $createdBy, $status = 'disetujui', $catatanPenolakan = null) {
        $node = $this->createNode($nama, 'sub_kegiatan', $parentId, $opdId, $status, $createdBy);
        DetailSubKegiatan::on('modul_kinerja')->create([
            'pohon_id' => $node->id, 
            'indikator_sub_kegiatan' => $ind, 
            'target_sub_kegiatan' => $trg, 
            'satuan_target' => $sat, 
            'anggaran' => $ang, 
            'penanggung_jawab' => 'Bidang Terkait'
        ]);
        
        // Update catatan penolakan jika ada (khusus node yang ditolak)
        if ($catatanPenolakan) {
            $node->update(['catatan_penolakan' => $catatanPenolakan]);
        }
    }
}