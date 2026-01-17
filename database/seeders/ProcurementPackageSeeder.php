<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProcurementPackageSeeder extends Seeder
{
    public function run()
    {
        // 1. Seed Tabel Packages (Paket ID 1)
        DB::connection('modul_pengadaan')->table('procurement_packages')->insert([
            'id' => 1,
            'nama_paket' => 'Pengadaan Perangkat IT dan ATK Gabungan Sekretariat TA 2026',
            'status_tahapan' => 'identifikasi_selesai',
            'perubahan_ke' => 0,
            'tanggal_perubahan' => '2026-01-07',
            'pagu_paket' => 234300000.00,
            'opsi_pdn' => 1,
            'alasan_pdn' => 'Sesuai instruksi penggunaan produk dalam negeri',
            'jenis_pengadaan' => 'Barang',
            'alasan_pemilihan_jenis' => 'Barang merupakan kebutuhan operasional standar',
            'metode_pemilihan' => 'E-Purchasing',
            'alasan_metode_pemilihan' => 'Tersedia di Katalog Elektronik LKPP',
            'kode_kbki' => '83141',
            'is_pdn' => 1,
            'is_umkm' => 1,
            'lokasi_pekerjaan' => 'Kantor Dinas Kominfo Prov. Kalbar',
            'jadwal_pelaksanaan' => 'Januari - Desember 2026',
            'uraian_pekerjaan' => 'Pengadaan perangkat keras dan pendukung jaringan',
            'tanggal_penyusunan' => '2026-01-07',
            'nama_pa_kpa' => 'Samuel, S.E., M.Si.',
            'nip_pa_kpa' => '197005121996031004',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Seed Tabel Items untuk Paket ID 1
        DB::connection('modul_pengadaan')->table('procurement_items')->insert([
            [
                'package_id' => 1,
                'rka_detail_id' => 9,
                'nama_item' => 'Laptop Spesifikasi Tinggi (Core i7, 16GB RAM)',
                'volume' => 5.00,
                'satuan' => 'Unit',
                'harga_satuan_rka' => 25000000.00,
                'harga_satuan_hps' => 25000000.00,
                'total_hps' => 138750000.00,
            ],
            [
                'package_id' => 1,
                'rka_detail_id' => 11,
                'nama_item' => 'Tenaga Ahli Penyusunan Masterplan Smart City',
                'volume' => 6.00,
                'satuan' => 'OB (Orang Bulan)',
                'harga_satuan_rka' => 15000000.00,
                'harga_satuan_hps' => 15000000.00,
                'total_hps' => 90000000.00,
            ],
            [
                'package_id' => 1,
                'rka_detail_id' => 14,
                'nama_item' => 'Kabel UTP Cat 6 Belden Original',
                'volume' => 2.00,
                'satuan' => 'Roll',
                'harga_satuan_rka' => 2500000.00,
                'harga_satuan_hps' => 2500000.00,
                'total_hps' => 5550000.00,
            ],
        ]);
    }
}