<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kinerja\Mission;
use App\Models\Kinerja\Goal;
use App\Models\Kinerja\SasaranStrategis;
use App\Models\Kinerja\Program;
use App\Models\Kinerja\Activity;
use App\Models\Kinerja\SubActivity;

class KinerjaAptikaFullSeeder extends Seeder
{
    public function run()
    {
        // 1. Ambil Misi
        $mission = Mission::on('modul_kinerja')->first();
        if (!$mission) {
            $this->command->error('Misi tidak ditemukan!');
            return;
        }

        $pd_id = 1; // ID Diskominfo

        // === LEVEL 2: TUJUAN PD ===
        $goal = Goal::on('modul_kinerja')->updateOrCreate(
            ['nama_tujuan' => 'Meningkatnya Birokrasi yang akuntabel, berintegritas dan adaptif', 'pd_id' => $pd_id],
            ['mission_id' => $mission->id, 'indikator' => 'Indeks SPBE', 'satuan' => 'Indeks', 'baseline_2024' => '3.58', 'target_2025' => '3.82', 'status' => 'approved']
        );

        // === LEVEL 3: SASARAN STRATEGIS ===
        $sasaran = SasaranStrategis::on('modul_kinerja')->updateOrCreate(
            ['nama_sasaran' => 'Optimalisasi pemanfaatan teknologi informasi dan komunikasi', 'goal_id' => $goal->id],
            ['indikator_sasaran' => 'Indeks Integrasi Layanan', 'satuan' => 'Indeks', 'baseline_2024' => '1.79', 'target_2025' => '1.91', 'status' => 'approved']
        );

        // === LEVEL 4: PROGRAM ===
        $program = Program::on('modul_kinerja')->updateOrCreate(
            ['nama_program' => 'PROGRAM PENGELOLAAN APLIKASI INFORMATIKA', 'sasaran_id' => $sasaran->id],
            ['indikator_program' => 'Tersedianya aplikasi informatika standar', 'satuan' => 'Aplikasi', 'baseline_2024' => '5', 'target_2025' => '10', 'status' => 'approved']
        );

        // === LEVEL 5: 3 KEGIATAN UTAMA ===
        $act1 = Activity::on('modul_kinerja')->updateOrCreate(
            ['nama_kegiatan' => 'Pengelolaan Nama Domain dan Sub Domain', 'program_id' => $program->id],
            ['indikator_kegiatan' => 'Persentase perangkat daerah menggunakan domain resmi', 'satuan' => 'Persen', 'baseline_2024' => '100', 'target_2025' => '100', 'status' => 'approved']
        );

        $act2 = Activity::on('modul_kinerja')->updateOrCreate(
            ['nama_kegiatan' => 'Pengelolaan E-Government', 'program_id' => $program->id],
            ['indikator_kegiatan' => 'Jumlah aplikasi terintegrasi SPL', 'satuan' => 'Aplikasi', 'baseline_2024' => '5', 'target_2025' => '10', 'status' => 'approved']
        );

        $act3 = Activity::on('modul_kinerja')->updateOrCreate(
            ['nama_kegiatan' => 'Pengelolaan E-government di Lingkup Pemerintah Daerah Provinsi', 'program_id' => $program->id],
            ['indikator_kegiatan' => 'Jumlah ASN mendapat edukasi digital', 'satuan' => 'Orang', 'baseline_2024' => '0', 'target_2025' => '100', 'status' => 'approved']
        );

        // === LEVEL 6: SUB-KEGIATAN (FULL DATA EXCEL DENGAN STATUS CAMPURAN) ===
        // Saya memetakan manual parent activity-nya agar rapi
        
        $subActivities = [
            // --- GROUP 1: KEGIATAN DOMAIN (ACT 1) ---
            [
                'parent' => $act1->id, 'kode' => '2.16.03.1.02.01',
                'nama' => 'Pengelolaan Nama Domain Pemerintah Daerah',
                'indikator' => 'Jumlah Nama Domain dan Sub Domain diperpanjang',
                'baseline' => 10, 'target' => 50, 'satuan' => 'Domain',
                'status' => 'draft', 'klasifikasi' => 'IKK' // [TEST: Draft]
            ],
            [
                'parent' => $act1->id, 'kode' => '2.16.03.1.02.02',
                'nama' => 'Pengelolaan e-government (Website SKPD)',
                'indikator' => 'Jumlah sub domain (website) SKPD yang aktif',
                'baseline' => 30, 'target' => 45, 'satuan' => 'Website',
                'status' => 'pending', 'klasifikasi' => 'IKD' // [TEST: Pending]
            ],

            // --- GROUP 2: KEGIATAN E-GOV UMUM (ACT 2) ---
            [
                'parent' => $act2->id, 'kode' => '2.16.03.1.02.03',
                'nama' => 'Pengelolaan Pusat Data (Data Center)',
                'indikator' => 'Persentase uptime/ketersediaan Pusat Data',
                'baseline' => 99, 'target' => 99.5, 'satuan' => 'Persen',
                'status' => 'approved', 'klasifikasi' => 'IKU' // [TEST: Approved]
            ],
            [
                'parent' => $act2->id, 'kode' => '2.16.03.1.02.04',
                'nama' => 'Pengembangan Aplikasi Layanan Publik',
                'indikator' => 'Jumlah Aplikasi Layanan Publik yang dikembangkan',
                'baseline' => 0, 'target' => 5, 'satuan' => 'Aplikasi',
                'status' => 'rejected', 'klasifikasi' => 'IKD', // [TEST: Rejected]
                'catatan' => 'Mohon lampirkan daftar rancangan aplikasi prioritas.'
            ],
            [
                'parent' => $act2->id, 'kode' => '2.16.03.1.02.05',
                'nama' => 'Pengembangan Ekosistem Smart City',
                'indikator' => 'Jumlah Kabupaten/Kota pendampingan Smart City',
                'baseline' => 1, 'target' => 3, 'satuan' => 'Kab/Kota',
                'status' => 'verified', 'klasifikasi' => 'IKU' // [TEST: Verified]
            ],
            [
                'parent' => $act2->id, 'kode' => '2.16.03.1.02.07',
                'nama' => 'Penyelenggaraan Sistem Penghubung Layanan (SPL)',
                'indikator' => 'Jumlah Sistem Informasi yang terintegrasi SPL',
                'baseline' => 2, 'target' => 10, 'satuan' => 'Sistem',
                'status' => 'pending', 'klasifikasi' => 'IKU' // [TEST: Pending - Prioritas]
            ],
            [
                'parent' => $act2->id, 'kode' => '2.16.03.1.02.08',
                'nama' => 'Penanganan Insiden Keamanan Informasi (CSIRT)',
                'indikator' => 'Persentase insiden keamanan yang tertangani',
                'baseline' => 80, 'target' => 100, 'satuan' => 'Persen',
                'status' => 'draft', 'klasifikasi' => 'IKU' // [TEST: Draft - Prioritas]
            ],
            [
                'parent' => $act2->id, 'kode' => '2.16.03.1.02.09',
                'nama' => 'Pelaksanaan Audit TIK dan Keamanan Informasi',
                'indikator' => 'Jumlah Sistem Elektronik diaudit',
                'baseline' => 0, 'target' => 2, 'satuan' => 'Sistem',
                'status' => 'draft', 'klasifikasi' => 'IKK' // [TEST: Draft]
            ],
            [
                'parent' => $act2->id, 'kode' => '2.16.03.1.02.11',
                'nama' => 'Pengelolaan Jaringan Intra Pemerintah Daerah',
                'indikator' => 'Jumlah Perangkat Daerah terhubung jaringan intra',
                'baseline' => 35, 'target' => 40, 'satuan' => 'OPD',
                'status' => 'verified', 'klasifikasi' => 'IKK' // [TEST: Verified]
            ],
            [
                'parent' => $act2->id, 'kode' => '2.16.03.1.02.12',
                'nama' => 'Penyediaan Layanan Video Conference Pemerintah',
                'indikator' => 'Jumlah fasilitas vicon siap digunakan',
                'baseline' => 50, 'target' => 100, 'satuan' => 'Layanan',
                'status' => 'approved', 'klasifikasi' => 'IKK' // [TEST: Approved]
            ],
            [
                'parent' => $act2->id, 'kode' => '2.16.03.1.02.14',
                'nama' => 'Pemeliharaan Perangkat Keras dan Lunak Data Center',
                'indikator' => 'Persentase perangkat terpelihara',
                'baseline' => 90, 'target' => 100, 'satuan' => 'Persen',
                'status' => 'pending', 'klasifikasi' => 'IKK' // [TEST: Pending]
            ],

            // --- GROUP 3: KEGIATAN E-GOV PEMDA (ACT 3) ---
            [
                'parent' => $act3->id, 'kode' => '2.16.03.1.02.06',
                'nama' => 'Penerbitan dan Pengelolaan Sertifikat Elektronik',
                'indikator' => 'Persentase ASN memiliki Tanda Tangan Digital',
                'baseline' => 10, 'target' => 30, 'satuan' => 'Persen',
                'status' => 'verified', 'klasifikasi' => 'IKU' // [TEST: Verified - Prioritas]
            ],
            [
                'parent' => $act3->id, 'kode' => '2.16.03.1.02.10',
                'nama' => 'Penyusunan Kebijakan dan Regulasi SPBE',
                'indikator' => 'Jumlah Dokumen Kebijakan SPBE',
                'baseline' => 0, 'target' => 2, 'satuan' => 'Dokumen',
                'status' => 'verified', 'klasifikasi' => 'IKD' // [TEST: Verified]
            ],
            [
                'parent' => $act3->id, 'kode' => '2.16.03.1.02.13',
                'nama' => 'Sosialisasi dan Literasi Digital Sektor Pemerintahan',
                'indikator' => 'Jumlah ASN mengikuti literasi digital',
                'baseline' => 0, 'target' => 100, 'satuan' => 'Orang',
                'status' => 'draft', 'klasifikasi' => 'IKD' // [TEST: Draft]
            ],
            [
                'parent' => $act3->id, 'kode' => '2.16.03.1.02.15',
                'nama' => 'Koordinasi dan Sinkronisasi Penerapan SPBE',
                'indikator' => 'Jumlah laporan koordinasi penerapan SPBE',
                'baseline' => 1, 'target' => 4, 'satuan' => 'Laporan',
                'status' => 'rejected', 'klasifikasi' => 'IKK', // [TEST: Rejected]
                'catatan' => 'Perbaiki indikator agar lebih terukur (Outcome).'
            ],
            [
                'parent' => $act3->id, 'kode' => '2.16.03.1.02.0034',
                'nama' => 'Koordinasi penyusunan kebijakan tata kelola SPBE',
                'indikator' => 'Jumlah dokumen kebijakan tata kelola',
                'baseline' => 1, 'target' => 2, 'satuan' => 'Dokumen',
                'status' => 'pending', 'klasifikasi' => 'IKK' // [TEST: Pending]
            ],
        ];

        // Loop Simpan ke Database
        foreach ($subActivities as $s) {
            SubActivity::on('modul_kinerja')->updateOrCreate(
                ['kode_sub' => $s['kode']], 
                [
                    'activity_id' => $s['parent'], 
                    'nama_sub' => $s['nama'], 
                    'indikator_sub' => $s['indikator'],
                    'satuan' => $s['satuan'],
                    'baseline_2024' => $s['baseline'],
                    'target_2025' => $s['target'],
                    
                    'status' => $s['status'], 
                    'catatan_revisi' => $s['catatan'] ?? null,
                    
                    'tipe_perhitungan' => 'Non-Akumulasi',
                    'klasifikasi' => $s['klasifikasi'],
                    'created_by_nip' => '19880101XXXXXXXX'
                ]
            );
        }

        $this->command->info('SUKSES! 17 Data Real telah di-seed dengan Status Campuran (Draft, Pending, Verified, Rejected) untuk Testing Lengkap.');
    }
}