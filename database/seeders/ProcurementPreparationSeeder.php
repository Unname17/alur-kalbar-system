<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProcurementPreparationSeeder extends Seeder
{
    public function run()
    {
        // Data berdasarkan file SQL alur_kalbar_pengadaan (2).sql
        DB::connection('modul_pengadaan')->table('procurement_preparations')->updateOrInsert(
            ['id' => 1],
            [
                'package_id' => 1,
                'alasan_metode' => 'Produk tersedia secara lengkap di Katalog Elektronik. Mengingat adanya item perangkat IT spesifikasi tinggi dan kebutuhan tenaga ahli, metode E-Purchasing dengan Negosiasi dipilih untuk memastikan kesesuaian teknis dan efisiensi waktu.',
                'kriteria_barang_jasa' => 'Kompleks',
                'jalur_strategis' => 'Negosiasi Harga',
                'jalur_prioritas' => 2,
                'uji_pasar_ideal' => 0,
                'uji_non_kritikal' => 0,
                'uji_nol_value_added' => 0,
                'uji_spek_stabil' => 0,
                'uji_pengalaman_identik' => 0,
                'justifikasi_pilihan' => 'Metode Negosiasi Harga dipilih karena pengadaan ini termasuk kategori Barang Kompleks dan Jasa, di mana optimalisasi aspek non-harga seperti durasi garansi dan dukungan teknis menjadi prioritas utama organisasi.',
                'target_strategis' => json_encode([
                    "Spek di atas standar",
                    "Garansi lebih lama",
                    "Pelatihan gratis",
                    "Diskon kuantitas"
                ]),
                'tanggal_analisis' => '2026-01-11',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}