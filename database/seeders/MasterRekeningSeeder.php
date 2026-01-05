<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Rka\MasterRekening;

class MasterRekeningSeeder extends Seeder
{
    public function run(): void
    {
        // Data diambil dari referensi RKA PDF
        $data = [
            ['kode_rekening' => '5.1.02.01.01.0024', 'nama_rekening' => 'Belanja Alat/Bahan untuk Kegiatan Kantor-Alat Tulis Kantor'],
            ['kode_rekening' => '5.1.02.01.01.0025', 'nama_rekening' => 'Belanja Alat/Bahan untuk Kegiatan Kantor-Kertas dan Cover'],
            ['kode_rekening' => '5.1.02.01.01.0026', 'nama_rekening' => 'Belanja Alat/Bahan untuk Kegiatan Kantor-Bahan Cetak'],
            ['kode_rekening' => '5.1.02.01.01.0029', 'nama_rekening' => 'Belanja Alat/Bahan untuk Kegiatan Kantor-Bahan Komputer'],
            ['kode_rekening' => '5.1.02.01.01.0052', 'nama_rekening' => 'Belanja Makanan dan Minuman Rapat'],
            ['kode_rekening' => '5.1.02.02.01.0003', 'nama_rekening' => 'Honorarium Narasumber atau Pembahas, Moderator, Pembawa Acara, dan Panitia'],
            ['kode_rekening' => '5.1.02.04.01.0001', 'nama_rekening' => 'Belanja Perjalanan Dinas Biasa'],
        ];

        // Memaksa penggunaan koneksi modul_anggaran sesuai skema migration
        foreach ($data as $item) {
            DB::connection('modul_anggaran')->table('master_rekenings')->updateOrInsert(
                ['kode_rekening' => $item['kode_rekening']],
                [
                    'nama_rekening' => $item['nama_rekening'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }
}