<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProcurementDoc3Seeder extends Seeder
{
    public function run()
    {
        $conn = 'modul_pengadaan';

        // 1. SEED KBKI MASTER (Rujukan SQL)
        DB::connection($conn)->table('kbki_masters')->updateOrInsert(
            ['kode_kbki' => '45220'],
            ['deskripsi_kbki' => 'Mesin pengolah data otomatis digital portabel (laptop, notebook, dan subnotebook)']
        );
        DB::connection($conn)->table('kbki_masters')->updateOrInsert(
            ['kode_kbki' => '83141'],
            ['deskripsi_kbki' => 'Jasa konsultansi piranti lunak (Software) dan sistem informasi']
        );

        // 2. SEED PAKET PENGADAAN (ID: 1)
        DB::connection($conn)->table('procurement_packages')->updateOrInsert(
            ['id' => 1],
            [
                'nama_paket' => 'Pengadaan Perangkat IT dan ATK Gabungan Sekretariat TA 2026',
                'status_tahapan' => 'strategi_selesai',
                'pagu_paket' => 234300000.00,
                'jenis_pengadaan' => 'Barang',
                'metode_pemilihan' => 'E-Purchasing',
                'kode_kbki' => '83141',
                'lokasi_pekerjaan' => 'Kantor Dinas Kominfo Prov. Kalbar',
                'jadwal_pelaksanaan' => 'Januari - Desember 2026',
                'tanggal_penyusunan' => '2026-01-07',
                'nama_pa_kpa' => 'Samuel, S.E., M.Si.',
                'nip_pa_kpa' => '197005121996031004',
                'created_at' => now(),
            ]
        );

        // 3. SEED ITEMS (Laptop, Tenaga Ahli, Kabel)
        DB::connection($conn)->table('procurement_items')->updateOrInsert(
            ['id' => 1, 'package_id' => 1],
            [
                'rka_detail_id' => 9,
                'nama_item' => 'Laptop Spesifikasi Tinggi (Core i7, 16GB RAM)',
                'volume' => 5.00,
                'satuan' => 'Unit',
                'harga_satuan_rka' => 25000000.00,
                'harga_satuan_hps' => 25000000.00,
                'total_hps' => 125000000.00,
            ]
        );

        // 4. SEED STRATEGI PERSIAPAN (Doc 2)
        DB::connection($conn)->table('procurement_preparations')->updateOrInsert(
            ['package_id' => 1],
            [
                'alasan_metode' => 'Produk tersedia secara lengkap di Katalog Elektronik. Metode E-Purchasing dipilih untuk efisiensi.',
                'kriteria_barang_jasa' => 'Kompleks',
                'jalur_prioritas' => 2,
                'jalur_strategis' => 'Negosiasi Harga',
                'justifikasi_pilihan' => 'Kategori Barang Kompleks membutuhkan optimalisasi aspek non-harga.',
                'target_strategis' => json_encode(["Spek di atas standar", "Garansi lebih lama", "Pelatihan gratis"]),
                'tanggal_analisis' => '2026-01-11',
            ]
        );

        // 5. SEED ANALISIS PERSIAPAN (Doc 3)
        DB::connection($conn)->table('procurement_preparation_analyses')->updateOrInsert(
            ['package_id' => 1],
            [
                'nama_calon_penyedia' => 'PT Borneo Digital Solusi',
                'produk_katalog' => 'LNV-TP-E14G5-PRO-2026',
                'harga_tayang_katalog' => 25000000.00, // FIX: Gunakan 25jt bukan 25.00
                'evaluasi_teknis' => json_encode([
                    "1.1" => ["status" => "Sesuai", "catatan" => "Spesifikasi dalam katalog sangat detail."],
                    "1.2" => ["status" => "Sesuai", "catatan" => "Sangat sesuai untuk mendukung tugas operasional."],
                    "1.3" => ["status" => "Sesuai", "catatan" => "Ukuran layar 14 inci memenuhi standar mobilitas."],
                    "1.4" => ["status" => "Sesuai", "catatan" => "Processor Core i7 menjamin performa jangka panjang."],
                    "1.5" => ["status" => "Sesuai", "catatan" => "Memiliki sertifikat TKDN di atas 40%."],
                    "1.6" => ["status" => "Sesuai", "catatan" => "Stok tersedia sesuai kebutuhan."]
                ]),
                'evaluasi_harga' => json_encode([
                    "2.1" => ["status" => "Cukup", "catatan" => "Tersedia pembanding di E-Katalog."],
                    "2.2" => ["status" => "Cukup", "catatan" => "Harga kompetitif dibandingkan retail."],
                    "2.3" => ["status" => "Cukup", "catatan" => "Berada di bawah pagu anggaran."]
                ]),
                'evaluasi_kontrak' => json_encode([
                    "3.1" => ["status" => "Sesuai", "catatan" => "Draf pokok kontrak lengkap."],
                    "3.2" => ["status" => "Sesuai", "catatan" => "Syarat umum sesuai standar."],
                    "3.3" => ["status" => "Sesuai", "catatan" => "Jenis kontrak harga satuan tepat."]
                ]),
                'evaluasi_katalog' => json_encode([
                    "4.1" => ["status" => "Sesuai", "catatan" => "Produk aktif di Katalog Nasional."],
                    "4.2" => ["catatan" => "Terdapat 3 penyedia pembanding."]
                ]),
                'updated_at' => '2026-01-11 16:55:42',
            ]
        );
    }
}