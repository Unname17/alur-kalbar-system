<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kinerja\PohonKinerja;
use App\Models\Kak\Kak;
use App\Models\Kak\KakTimPelaksana;

class KakSeeder extends Seeder
{
    public function run()
    {
        // 1. Ambil 3 data Sub Kegiatan agar bisa tes semua status
        $subKegiatans = PohonKinerja::where('jenis_kinerja', 'sub_kegiatan')->take(3)->get();

        if ($subKegiatans->count() > 0) {
            foreach ($subKegiatans as $key => $sub) {
                
                // --- LOGIKA STATUS & NOMOR ---
                // Index 0: Disetujui (2) -> WAJIB ADA NOMOR
                // Index 1: Ditolak (3) -> TIDAK ADA NOMOR
                // Index 2: Menunggu Verifikasi (1) -> TIDAK ADA NOMOR
                
                $status = 2;
                $catatan = 'Dokumen sudah sesuai standar dan siap dilaksanakan.';
                $nomorKak = '00' . ($key + 1) . '/ALUR-KALBAR/KAK/' . date('Y'); // Generate Nomor Dummy

                if ($key == 1) {
                    $status = 3;
                    $catatan = 'Mohon perbaiki bagian latar belakang agar lebih detail dan tambahkan dasar hukum terbaru.';
                    $nomorKak = null; // Ditolak belum punya nomor
                } elseif ($key == 2) {
                    $status = 1; // Menunggu Verifikasi
                    $catatan = null;
                    $nomorKak = null; // Menunggu belum punya nomor
                }

                // 2. Simpan ke database KAK
                $kak = Kak::create([
                    'pohon_kinerja_id' => $sub->id,
                    'judul_kak'        => 'KAK Strategis: ' . $sub->nama_kinerja,
                    'kode_proyek'      => 'PRJ-2025-00' . ($key + 1),
                    'latar_belakang'   => 'Latar belakang ini disimpan di database alur_kalbar_kak untuk mendukung transparansi kinerja.',
                    'maksud_tujuan'    => 'Meningkatkan efisiensi layanan publik di Kalimantan Barat.',
                    'metode_pelaksanaan' => 'Swakelola',
                    'lokasi'           => 'Pontianak, Kalimantan Barat',
                    'waktu_mulai'      => now(),
                    'waktu_selesai'    => now()->addMonths(6),
                    
                    // Field Status & Nomor
                    'status'              => $status, 
                    'nomor_kak'           => $nomorKak, // <--- INI TAMBAHAN PENTING
                    'catatan_sekretariat' => $catatan,
                ]);

                // 3. Simpan Tim ke database KAK
                KakTimPelaksana::create([
                    'kak_id'          => $kak->id,
                    'nama_personil'   => 'Personil Ke-' . ($key + 1),
                    'nip'             => '19880101201501100' . ($key + 1),
                    'peran_dalam_tim' => 'Koordinator Lapangan',
                ]);
            }

            $this->command->info("Seeder Berhasil! Data 'Disetujui' sudah memiliki Nomor KAK otomatis.");
        } else {
            $this->command->error("Gagal Seeding: Data Sub Kegiatan di database Kinerja tidak ditemukan.");
        }
    }
}