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
        // 1. Ambil Misi (Parent Paling Atas)
        $mission = Mission::on('modul_kinerja')->first();
        if (!$mission) {
            $this->command->error('Misi tidak ditemukan! Harap jalankan seeder Misi terlebih dahulu.');
            return;
        }

        $pd_id = 1; // ID OPD (Sesuaikan dengan ID Diskominfo di tabel perangkat_daerah)

        // --- LEVEL 2: TUJUAN PD ---
        $goal = Goal::on('modul_kinerja')->updateOrCreate(
            ['nama_tujuan' => 'Meningkatnya Birokrasi yang akuntabel, berintegritas dan adaptif', 'pd_id' => $pd_id],
            [
                'mission_id' => $mission->id,
                'indikator' => 'Indeks SPBE',
                'status' => 'approved',
                'target_2025' => '3.82',
            ]
        );

        // --- LEVEL 3: SASARAN STRATEGIS ---
        $sasaran = SasaranStrategis::on('modul_kinerja')->updateOrCreate(
            ['nama_sasaran' => 'Optimalisasi pemanfaatan teknologi informasi dan komunikasi', 'goal_id' => $goal->id],
            [
                'indikator_sasaran' => 'Indeks Integrasi Layanan',
                'status' => 'approved',
                'target_2025' => '20',
            ]
        );

        // --- LEVEL 4: PROGRAM ---
        $program = Program::on('modul_kinerja')->updateOrCreate(
            ['nama_program' => 'PROGRAM APLIKASI INFORMATIKA', 'sasaran_id' => $sasaran->id],
            [
                'indikator_program' => 'Persentase Perangkat Daerah yang terlayani dengan layanan SPBE',
                'status' => 'approved',
                'target_2025' => '100',
                'satuan' => 'Persen'
            ]
        );

        // --- LEVEL 5: KEGIATAN ---
        $kegiatan = Activity::on('modul_kinerja')->updateOrCreate(
            ['nama_kegiatan' => 'PENGELOLAAN E-GOVERNMENT', 'program_id' => $program->id],
            [
                'indikator_kegiatan' => 'Jumlah Layanan SPBE yang dikembangkan dan terintegrasi',
                'status' => 'approved',
                'target_2025' => '15',
                'satuan' => 'Layanan'
            ]
        );

        // --- LEVEL 6: 15 SUB-KEGIATAN (DATA FULL) ---
        $subActivities = [
            [
                'kode' => '2.16.03.1.02.01',
                'nama' => 'Pengelolaan Nama Domain Pemerintah Daerah',
                'indikator' => 'Jumlah Nama Domain dan Sub Domain diperpanjang/dikelola',
                'target' => '50', 'satuan' => 'Domain'
            ],
            [
                'kode' => '2.16.03.1.02.02',
                'nama' => 'Pengelolaan e-government (Website SKPD)',
                'indikator' => 'Jumlah sub domain (website) SKPD yang aktif dan terupdate',
                'target' => '45', 'satuan' => 'Website'
            ],
            [
                'kode' => '2.16.03.1.02.03',
                'nama' => 'Pengelolaan Pusat Data (Data Center)',
                'indikator' => 'Persentase uptime/ketersediaan Pusat Data',
                'target' => '99.5', 'satuan' => 'Persen'
            ],
            [
                'kode' => '2.16.03.1.02.04',
                'nama' => 'Pengembangan Aplikasi Layanan Publik',
                'indikator' => 'Jumlah Aplikasi Layanan Publik yang dikembangkan',
                'target' => '5', 'satuan' => 'Aplikasi'
            ],
            [
                'kode' => '2.16.03.1.02.05',
                'nama' => 'Pengembangan Ekosistem Smart City',
                'indikator' => 'Jumlah Kabupaten/Kota yang mendapat pendampingan Smart City',
                'target' => '3', 'satuan' => 'Kab/Kota'
            ],
            [
                'kode' => '2.16.03.1.02.06',
                'nama' => 'Penerbitan dan Pengelolaan Sertifikat Elektronik',
                'indikator' => 'Persentase ASN yang memiliki Sertifikat Elektronik (Tanda Tangan Digital)',
                'target' => '30', 'satuan' => 'Persen'
            ],
            [
                'kode' => '2.16.03.1.02.07',
                'nama' => 'Penyelenggaraan Sistem Penghubung Layanan (SPL)',
                'indikator' => 'Jumlah Sistem Informasi yang terintegrasi melalui SPL',
                'target' => '10', 'satuan' => 'Sistem'
            ],
            [
                'kode' => '2.16.03.1.02.08',
                'nama' => 'Penanganan Insiden Keamanan Informasi (CSIRT)',
                'indikator' => 'Persentase insiden keamanan informasi yang tertangani',
                'target' => '100', 'satuan' => 'Persen'
            ],
            [
                'kode' => '2.16.03.1.02.09',
                'nama' => 'Pelaksanaan Audit TIK dan Keamanan Informasi',
                'indikator' => 'Jumlah Sistem Elektronik yang dilakukan audit keamanan',
                'target' => '2', 'satuan' => 'Sistem'
            ],
            [
                'kode' => '2.16.03.1.02.10',
                'nama' => 'Penyusunan Kebijakan dan Regulasi SPBE',
                'indikator' => 'Jumlah Dokumen Kebijakan/Regulasi SPBE yang disusun',
                'target' => '2', 'satuan' => 'Dokumen'
            ],
            [
                'kode' => '2.16.03.1.02.11',
                'nama' => 'Pengelolaan Jaringan Intra Pemerintah Daerah',
                'indikator' => 'Jumlah Perangkat Daerah yang terhubung jaringan intra',
                'target' => '40', 'satuan' => 'OPD'
            ],
            [
                'kode' => '2.16.03.1.02.12',
                'nama' => 'Penyediaan Layanan Video Conference Pemerintah',
                'indikator' => 'Jumlah fasilitas layanan video conference yang siap digunakan',
                'target' => '100', 'satuan' => 'Layanan'
            ],
            [
                'kode' => '2.16.03.1.02.13',
                'nama' => 'Sosialisasi dan Literasi Digital Sektor Pemerintahan',
                'indikator' => 'Jumlah ASN yang mengikuti sosialisasi literasi digital/SPBE',
                'target' => '100', 'satuan' => 'Orang'
            ],
            [
                'kode' => '2.16.03.1.02.14',
                'nama' => 'Pemeliharaan Perangkat Keras dan Lunak Data Center',
                'indikator' => 'Persentase perangkat yang terpelihara dengan baik',
                'target' => '100', 'satuan' => 'Persen'
            ],
            [
                'kode' => '2.16.03.1.02.15',
                'nama' => 'Koordinasi dan Sinkronisasi Penerapan SPBE',
                'indikator' => 'Jumlah laporan hasil koordinasi penerapan SPBE',
                'target' => '4', 'satuan' => 'Laporan'
            ],
        ];

        // Loop Simpan ke Database
        foreach ($subActivities as $sub) {
            SubActivity::on('modul_kinerja')->updateOrCreate(
                ['nama_sub' => $sub['nama'], 'activity_id' => $kegiatan->id],
                [
                    'kode_sub' => $sub['kode'], 
                    'indikator_sub' => $sub['indikator'],
                    'satuan' => $sub['satuan'],
                    'baseline_2024' => '0', 
                    'target_2025' => $sub['target'],
                    
                    // STATUS: VERIFIED (Agar muncul sebagai data yang siap divalidasi RKA)
                    'status' => 'verified', 
                    
                    'tipe_perhitungan' => 'Non-Akumulasi',
                    'klasifikasi' => 'IKK',
                    'created_by_nip' => '19880101XXXXXXXX' // Dummy NIP Staff
                ]
            );
        }

        $this->command->info('15 Sub-Kegiatan Aptika berhasil di-seed dengan status Verified!');
    }
}