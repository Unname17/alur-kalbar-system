<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProcurementDoc6Seeder extends Seeder
{
    /**
     * Jalankan seeder untuk data Dokumen 6 berdasarkan SQL Dump.
     */
    public function run(): void
    {
        $connection = 'modul_pengadaan';

        // 1. DATA REFERENSI HARGA (procurement_price_references)
        $priceReferences = [
            [
                'id' => 1,
                'package_id' => 1,
                'type' => 'qualitative',
                'merek_model' => 'HP LaserJet Pro M404dw',
                'sumber_nama' => 'YouTube (Review & Setup)',
                'link_url' => 'https://youtu.be/6Hc83u4r6mY',
                'harga_satuan' => 0.00,
                'file_bukti' => '1768463829_Screenshot from 2026-01-15 14-28-49.png',
                'kelebihan' => 'Kecepatan 40 ppm & Auto-Duplex. Wi-Fi dual-band & HP Smart App. High-yield toner.',
                'kekurangan' => 'Grafik mono biasa-saja. Tray standar hanya 250 lembar.',
                'tanggal_akses' => '2026-01-15',
                'created_at' => '2026-01-15 00:57:09',
            ],
            [
                'id' => 2,
                'package_id' => 1,
                'type' => 'market',
                'merek_model' => 'HP LaserJet Pro M404dw',
                'sumber_nama' => 'SIPLAH Blibli (HP Official)',
                'link_url' => 'https://siplah.blibli.com/product/hp-printer-laserjet-pro-m404dw',
                'harga_satuan' => 7546890.00,
                'file_bukti' => '1768463892_Screenshot from 2026-01-15 14-28-17.png',
                'garansi_layanan' => 'Garansi resmi HP Indonesia 3 tahun (onsite).',
                'tanggal_akses' => '2026-01-15',
                'created_at' => '2026-01-15 00:58:12',
            ],
            [
                'id' => 6,
                'package_id' => 1,
                'type' => 'sbu',
                'sumber_nama' => 'SBU 2025 (Kategori Peralatan Pencetak Tipe Cepat)',
                'harga_satuan' => 8000000.00,
                'file_bukti' => '1768495314_Screenshot from 2026-01-12 13-42-30.png',
                'nomor_tanggal_dok' => 'Pergub No. 88 Tahun 2025',
                'catatan_relevansi' => 'Digunakan sebagai batas atas biaya menurut Permen PUPR 2025.',
                'tanggal_akses' => '2026-01-15',
                'created_at' => '2026-01-15 09:41:54',
            ],
            [
                'id' => 7,
                'package_id' => 1,
                'type' => 'contract',
                'sumber_nama' => 'Kontrak Lama 2022 (30 ppm)',
                'harga_satuan' => 4500000.00,
                'file_bukti' => '1768495665_Screenshot from 2026-01-12 12-49-37.png',
                'tahun_anggaran' => '2022',
                'catatan_penyesuaian' => 'Spesifikasi lebih rendah (30 ppm), perlu penyesuaian karena kebutuhan saat ini 40 ppm.',
                'tanggal_akses' => '2026-01-15',
                'created_at' => '2026-01-15 09:47:45',
            ]
        ];

        foreach ($priceReferences as $ref) {
            DB::connection($connection)->table('procurement_price_references')->updateOrInsert(
                ['id' => $ref['id']],
                $ref
            );
        }

        // 2. KEMASKINI RINGKASAN HARGA PADA TABLE PAKET
        DB::connection($connection)->table('procurement_packages')->where('id', 1)->update([
            'hps_terendah' => 4500000.00,
            'hps_tertinggi' => 8000000.00,
            'hps_hitung_rata_rata' => 6682296.67,
            'updated_at' => Carbon::now(),
        ]);
    }
}