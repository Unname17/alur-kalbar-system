<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterSshSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data dummy untuk katalog belanja
        $data = [
            [
                'kode_barang' => 'SSH-2025-001',
                'nama_barang' => 'Kertas HVS A4 80gr',
                'satuan' => 'Rim',
                'harga_satuan' => 55000,
                'kategori' => 'SSH',
                'spesifikasi' => 'Sinar Dunia / Setara'
            ],
            [
                'kode_barang' => 'SSH-2025-002',
                'nama_barang' => 'Laptop Core i5 RAM 8GB',
                'satuan' => 'Unit',
                'harga_satuan' => 12500000,
                'kategori' => 'SSH',
                'spesifikasi' => 'SSD 512GB, Windows 11'
            ],
            [
                'kode_barang' => 'SBU-2025-001',
                'nama_barang' => 'Honorarium Narasumber Ahli',
                'satuan' => 'Orang/Jam',
                'harga_satuan' => 900000,
                'kategori' => 'SBU',
                'spesifikasi' => 'Minimal Pendidikan S2 / Praktisi'
            ],
            [
                'kode_barang' => 'SBU-2025-002',
                'nama_barang' => 'Konsumsi Rapat (Nasi Kotak)',
                'satuan' => 'Kotak',
                'harga_satuan' => 35000,
                'kategori' => 'SBU',
                'spesifikasi' => 'Menu Lengkap + Air Mineral'
            ],
        ];

        foreach ($data as $item) {
            // PENTING: Gunakan koneksi 'modul_anggaran'
            DB::connection('modul_anggaran')->table('master_ssh')->updateOrInsert(
                ['kode_barang' => $item['kode_barang']], // Cek berdasarkan kode agar tidak double
                $item
            );
        }
    }
}