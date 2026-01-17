<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Rka\RkaMain;
use App\Models\Rka\RkaDetail;
use App\Models\Rka\MasterRekening;
use App\Models\Kinerja\SubActivity;

class RkaAptikaSeeder extends Seeder
{
    public function run()
    {
        // ---------------------------------------------------------
        // 1. BUAT MASTER REKENING (SSH) DUMMY
        // ---------------------------------------------------------
        $rekenings = [
            ['kode' => '5.1.02.01.01.0024', 'nama' => 'Belanja Alat/Bahan untuk Kegiatan Kantor-Alat Tulis Kantor'],
            ['kode' => '5.1.02.01.01.0026', 'nama' => 'Belanja Bahan Cetak'],
            ['kode' => '5.1.02.01.01.0052', 'nama' => 'Belanja Makanan dan Minuman Rapat'],
            ['kode' => '5.1.02.02.01.0013', 'nama' => 'Belanja Jasa Konsultansi Perencanaan'],
            ['kode' => '5.1.02.02.01.0061', 'nama' => 'Belanja Jasa Tenaga Ahli'],
            ['kode' => '5.2.02.05.01.0005', 'nama' => 'Belanja Modal Peralatan Komputer (Laptop/PC)'],
            ['kode' => '5.2.02.05.02.0001', 'nama' => 'Belanja Modal Peralatan Jaringan'],
        ];

        foreach ($rekenings as $rek) {
            MasterRekening::on('modul_anggaran')->firstOrCreate(
                ['kode_rekening' => $rek['kode']],
                ['nama_rekening' => $rek['nama']]
            );
        }

        // Ambil referensi rekening untuk dipakai di detail
        $rekATK     = MasterRekening::on('modul_anggaran')->where('kode_rekening', '5.1.02.01.01.0024')->first();
        $rekMakan   = MasterRekening::on('modul_anggaran')->where('kode_rekening', '5.1.02.01.01.0052')->first();
        $rekJasa    = MasterRekening::on('modul_anggaran')->where('kode_rekening', '5.1.02.02.01.0061')->first();
        $rekModalPC = MasterRekening::on('modul_anggaran')->where('kode_rekening', '5.2.02.05.01.0005')->first();

        // ---------------------------------------------------------
        // 2. DEFINISI SKENARIO RKA
        // ---------------------------------------------------------
        // Kita ambil 3 Sub Kegiatan dari KinerjaAptikaFullSeeder untuk variasi kasus
        
        $scenarios = [
            // KASUS 1: PENGADAAN BARANG (E-Purchasing Laptop)
            [
                'kode_sub' => '2.16.03.1.02.01', // Pengelolaan Nama Domain (Kita pinjam untuk beli server/laptop)
                'sumber_dana' => 'PENDAPATAN ASLI DAERAH (PAD)',
                'lokasi' => 'Dinas Kominfo Prov. Kalbar',
                'pptk_nama' => 'Budi Santoso, S.Kom',
                'pptk_nip' => '198501012010011001',
                'details' => [
                    [
                        'rekening_id' => $rekModalPC->id,
                        'uraian' => 'Laptop Spesifikasi Tinggi (Core i7, 16GB RAM)',
                        'spesifikasi' => 'Processor Intel Core i7 Gen 13, RAM 16GB DDR5, SSD 1TB, Layar 14 inch FHD, Win 11 Pro',
                        'koefisien' => 5,
                        'satuan' => 'Unit',
                        'harga' => 25000000,
                        'ppn' => 11
                    ],
                    [
                        'rekening_id' => $rekATK->id,
                        'uraian' => 'Tinta Printer Original Epson L-Series',
                        'spesifikasi' => 'Warna Hitam (003), Cyan, Magenta, Yellow. Set Lengkap.',
                        'koefisien' => 10,
                        'satuan' => 'Paket',
                        'harga' => 350000,
                        'ppn' => 11
                    ]
                ]
            ],

            // KASUS 2: JASA KONSULTANSI (Smart City)
            [
                'kode_sub' => '2.16.03.1.02.05', // Pengembangan Ekosistem Smart City
                'sumber_dana' => 'DAU (Dana Alokasi Umum)',
                'lokasi' => 'Kabupaten/Kota se-Kalbar',
                'pptk_nama' => 'Siti Aminah, M.T.',
                'pptk_nip' => '198005052005012005',
                'details' => [
                    [
                        'rekening_id' => $rekJasa->id,
                        'uraian' => 'Tenaga Ahli Penyusunan Masterplan Smart City',
                        'spesifikasi' => 'S2 Teknik Informatika/Perencanaan Wilayah, Pengalaman min 5 tahun, Sertifikat Keahlian.',
                        'koefisien' => 6, // 6 Bulan
                        'satuan' => 'OB (Orang Bulan)',
                        'harga' => 15000000,
                        'ppn' => 0 // Jasa Ahli Perorangan biasanya PPh 21 (bukan PPN)
                    ],
                    [
                        'rekening_id' => $rekMakan->id,
                        'uraian' => 'Makan Minum Rapat Koordinasi (Nasi Kotak)',
                        'spesifikasi' => 'Nasi, Lauk Utama (Ayam/Ikan), Sayur, Buah, Air Mineral.',
                        'koefisien' => 100,
                        'satuan' => 'Kotak',
                        'harga' => 45000,
                        'ppn' => 11
                    ]
                ]
            ],

            // KASUS 3: PENGELOLAAN DATA CENTER (Jasa Lainnya)
            [
                'kode_sub' => '2.16.03.1.02.03', // Pengelolaan Pusat Data
                'sumber_dana' => 'PAD',
                'lokasi' => 'Ruang Server Diskominfo',
                'pptk_nama' => 'Rahmat Hidayat, S.T.',
                'pptk_nip' => '198202022008011002',
                'details' => [
                    [
                        'rekening_id' => $rekModalPC->id, // Anggap beli server rack
                        'uraian' => 'Server Rackmount 2U',
                        'spesifikasi' => 'Dual Xeon Gold, 64GB ECC RAM, 2x2TB SSD SAS, Redundant PSU',
                        'koefisien' => 1,
                        'satuan' => 'Unit',
                        'harga' => 85000000,
                        'ppn' => 11
                    ],
                    [
                        'rekening_id' => $rekATK->id,
                        'uraian' => 'Kabel UTP Cat 6 Belden Original',
                        'spesifikasi' => '1 Roll (305 Meter), Warna Abu-abu',
                        'koefisien' => 2,
                        'satuan' => 'Roll',
                        'harga' => 2500000,
                        'ppn' => 11
                    ]
                ]
            ],
        ];

        // ---------------------------------------------------------
        // 3. EKSEKUSI SEEDING
        // ---------------------------------------------------------
        
        foreach ($scenarios as $scenario) {
            // A. Cari ID Sub Kegiatan di Modul Kinerja
            $subActivity = SubActivity::on('modul_kinerja')
                            ->where('kode_sub', $scenario['kode_sub'])
                            ->first();

            if (!$subActivity) {
                $this->command->warn("Skipping: Sub Kegiatan {$scenario['kode_sub']} tidak ditemukan di modul_kinerja.");
                continue;
            }

            // B. Buat RKA Main
            // Gunakan updateOrCreate agar tidak duplikat saat di-run ulang
            $rka = RkaMain::on('modul_anggaran')->updateOrCreate(
                ['sub_activity_id' => $subActivity->id],
                [
                    'sumber_dana' => $scenario['sumber_dana'],
                    'lokasi_kegiatan' => $scenario['lokasi'],
                    'waktu_pelaksanaan' => 'Januari - Desember',
                    'kelompok_sasaran' => 'Perangkat Daerah & Masyarakat',
                    
                    // Identitas PPTK
                    'nip_pptk' => $scenario['pptk_nip'],
                    'nama_pptk' => $scenario['pptk_nama'],
                    
                    // Data Tambahan (Sesuai Controller step 3)
                    'sub_unit_organisasi' => 'BIDANG APLIKASI INFORMATIKA',
                    'jenis_layanan' => 'Layanan Internal Pemerintah',
                    'spm' => 'Non-SPM',
                    
                    // Tim Anggaran Dummy (JSON)
                    'tim_anggaran' => json_encode([
                        ['nama' => 'Dr. H. Mulyadi, M.Si', 'nip' => '197001011995031001', 'jabatan' => 'Ketua TAPD'],
                        ['nama' => 'Ir. Budi Setiawan', 'nip' => '197505052000031005', 'jabatan' => 'Anggota TAPD']
                    ]),
                    
                    // Set Status langsung DITERIMA agar bisa lanjut ke KAK & Pengadaan
                    'status' => 'diterima', 
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // C. Buat RKA Details & Hitung Total
            $grandTotal = 0;
            
            // Hapus detail lama jika ada (reset)
            RkaDetail::on('modul_anggaran')->where('rka_main_id', $rka->id)->delete();

            foreach ($scenario['details'] as $det) {
                // Hitung Subtotal (Harga * Koefisien) + PPN
                $baseTotal = $det['harga'] * $det['koefisien'];
                $ppnValue = ($baseTotal * $det['ppn']) / 100;
                $subTotal = $baseTotal + $ppnValue;

                RkaDetail::on('modul_anggaran')->create([
                    'rka_main_id' => $rka->id,
                    'rekening_id' => $det['rekening_id'],
                    'uraian_belanja' => $det['uraian'],
                    'spesifikasi' => $det['spesifikasi'],
                    'koefisien' => $det['koefisien'],
                    'satuan' => $det['satuan'],
                    'harga_satuan' => $det['harga'],
                    'ppn_persen' => $det['ppn'],
                    'sub_total' => $subTotal,
                ]);

                $grandTotal += $subTotal;
            }

            // D. Update Total Anggaran di Header
            $rka->update(['total_anggaran' => $grandTotal]);
            
            $this->command->info("Berhasil Seed RKA: {$subActivity->nama_sub} (Rp " . number_format($grandTotal) . ")");
        }
    }
}