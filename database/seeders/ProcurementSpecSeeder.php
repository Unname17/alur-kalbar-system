<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProcurementSpecSeeder extends Seeder
{
    public function run()
    {
        $conn = 'modul_pengadaan';

        // Update data teknis untuk Item 1: Laptop
        DB::connection($conn)->table('procurement_items')->where('id', 1)->update([
            'merk_tipe' => 'Lenovo ThinkPad E14 Gen 5',
            'masa_garansi' => '3 Tahun On-Site Service',
            'standar_mutu' => 'TKDN + BMP > 40%, Sertifikat SNI & ISO 9001',
            'fungsi_kinerja' => 'Perangkat komputasi mobile performa tinggi untuk operasional pengembangan sistem informasi.',
            'aspek_pemeliharaan' => 'Pembersihan internal rutin 6 bulan sekali dan dukungan update driver resmi.',
            'deskripsi_spesifikasi' => "Intel Core i7-1355U, RAM 16GB DDR4, SSD 512GB NVMe, Layar 14 inch FHD IPS Antiglare, Windows 11 Pro Original.",
            'link_produk_katalog' => 'https://e-katalog.lkpp.go.id/katalog/produk/detail/LNV-TP-E14G5-PRO-2026',
            'updated_at' => now(),
        ]);

        // Update data teknis untuk Item 2: Tenaga Ahli
        DB::connection($conn)->table('procurement_items')->where('id', 2)->update([
            'merk_tipe' => 'Tenaga Ahli Madya Perencanaan Kota',
            'masa_garansi' => '6 Bulan (Selama Masa Kontrak)',
            'standar_mutu' => 'Sertifikat Keahlian (SKA) IAP Utama / Madya',
            'fungsi_kinerja' => 'Melakukan analisis teknis, koordinasi antar instansi, dan penyusunan dokumen final Masterplan Smart City.',
            'aspek_pemeliharaan' => 'Asistensi teknis rutin dan pelaporan progres bulanan secara tertulis.',
            'deskripsi_spesifikasi' => "Pendidikan minimal S2 Perencanaan Wilayah & Kota, pengalaman kerja profesional minimal 10 tahun di bidang Smart City.",
            'link_produk_katalog' => 'Input Manual (Penyedia Non-Katalog)',
            'updated_at' => now(),
        ]);

        // Update data teknis untuk Item 3: Kabel UTP
        DB::connection($conn)->table('procurement_items')->where('id', 3)->update([
            'merk_tipe' => 'Belden Cat6 7814A Original',
            'masa_garansi' => '1 Tahun Jaminan Distributor',
            'standar_mutu' => 'Standar ISO/IEC 11801, UL Listed & Sertifikat Keaslian Produk',
            'fungsi_kinerja' => 'Media transmisi data kecepatan tinggi (Gigabit Ethernet) untuk jaringan LAN internal kantor.',
            'aspek_pemeliharaan' => 'Pengecekan integritas konektor dan pengujian throughput berkala.',
            'deskripsi_spesifikasi' => "4-Pair, 23 AWG, Solid Bare Copper Conductors, Polyolefin Insulation, PVC Jacket, 305 Meter per Roll.",
            'link_produk_katalog' => 'https://e-katalog.lkpp.go.id/katalog/produk/detail/BELDEN-CAT6-7814A',
            'updated_at' => now(),
        ]);
    }
}