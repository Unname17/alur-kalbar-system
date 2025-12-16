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
        // 1. Ambil data dari modul kinerja (Database Terpisah)
        $subKegiatan = PohonKinerja::where('jenis_kinerja', 'sub_kegiatan')->first();

        if ($subKegiatan) {
            // 2. Simpan ke database KAK
            $kak = Kak::create([
                'pohon_kinerja_id' => $subKegiatan->id,
                'judul_kak'        => 'Contoh KAK Modular: ' . $subKegiatan->nama_kinerja,
                'kode_proyek'       => 'MODUL-KAK-001',
                'latar_belakang'   => 'Latar belakang ini disimpan di database alur_kalbar_kak.',
                'maksud_tujuan'    => 'Maksud tujuan operasional.',
                'waktu_mulai'      => now(),
                'waktu_selesai'    => now()->addMonths(6),
            ]);

            // 3. Simpan Tim ke database KAK
            KakTimPelaksana::create([
                'kak_id'          => $kak->id,
                'nama_personil'   => 'Admin Modular',
                'peran_dalam_tim' => 'Koordinator',
            ]);

            $this->command->info("Seeder Modul KAK Berhasil di Database Terpisah!");
        }
    }
}