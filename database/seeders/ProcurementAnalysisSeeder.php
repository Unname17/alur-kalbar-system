<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProcurementAnalysisSeeder extends Seeder
{
    public function run()
    {
        // Data berdasarkan tabel procurement_preparation_analyses di SQL dump
        DB::connection('modul_pengadaan')->table('procurement_preparation_analyses')->updateOrInsert(
            ['id' => 1],
            [
                'package_id' => 1,
                'nama_calon_penyedia' => 'PT Borneo Digital Solusi',
                'produk_katalog' => 'LNV-TP-E14G5-PRO-2026',
                // FIX: Diubah dari 25.00 menjadi 25000000.00 agar tampilan cetak benar
                'harga_tayang_katalog' => 25000000.00, 
                'link_produk_katalog' => 'https://e-katalog.lkpp.go.id/katalog/produk/detail/LNV-TP-E14G5-PRO-2026',
                
                // Evaluasi Teknis (B.1)
                'evaluasi_teknis' => json_encode([
                    "1.1" => ["status" => "Sesuai", "catatan" => "Spesifikasi dalam katalog sangat detail mencakup tipe prosessor, RAM, dan kapasitas penyimpanan secara jelas."],
                    "1.2" => ["status" => "Sesuai", "catatan" => "Sangat sesuai untuk mendukung tugas operasional staf IT dalam pengembangan sistem informasi."],
                    "1.3" => ["status" => "Sesuai", "catatan" => "Ukuran layar 14 inci dan bobot ringan memenuhi standar mobilitas kerja di lingkungan Sekretariat."],
                    "1.4" => ["status" => "Sesuai", "catatan" => "Processor Core i7 generasi terbaru menjamin efisiensi dan ketahanan performa jangka panjang."],
                    "1.5" => ["status" => "Sesuai", "catatan" => "Produk memiliki sertifikat TKDN di atas 40% sesuai instruksi penggunaan produk dalam negeri."],
                    "1.6" => ["status" => "Sesuai", "catatan" => "Penyedia mengonfirmasi ketersediaan stok siap kirim."]
                ]),

                // Evaluasi Harga (B.2)
                'evaluasi_harga' => json_encode([
                    "2.1" => ["status" => "Cukup", "catatan" => "Tersedia referensi harga pembanding dari 3 penyedia di E-Katalog."],
                    "2.2" => ["status" => "Cukup", "catatan" => "Harga lebih kompetitif dibandingkan retail offline."],
                    "2.3" => ["status" => "Cukup", "catatan" => "Harga berada di bawah pagu anggaran RKA."]
                ]),

                // Evaluasi Kontrak (B.3)
                'evaluasi_kontrak' => json_encode([
                    "3.1" => ["status" => "Sesuai", "catatan" => "Draf surat pesanan telah memuat rincian paket, sumber dana, dan nilai kontrak."],
                    "3.2" => ["status" => "Sesuai", "catatan" => "Syarat umum dan khusus kontrak sudah sesuai standar LKPP."],
                    "3.3" => ["status" => "Sesuai", "catatan" => "Jenis kontrak Harga Satuan sangat tepat untuk item barang."]
                ]),

                // Evaluasi Katalog (B.4)
                'evaluasi_katalog' => json_encode([
                    "4.1" => ["status" => "Sesuai", "catatan" => "Produk berstatus aktif dan tayang di Katalog Elektronik Nasional."],
                    "4.2" => ["catatan" => "Terdapat lebih dari 3 penyedia yang menawarkan produk sejenis."]
                ]),

                'created_at' => now(),
                'updated_at' => '2026-01-11 16:55:42',
            ]
        );
    }
}