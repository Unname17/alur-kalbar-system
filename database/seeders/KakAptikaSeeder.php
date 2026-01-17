<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rka\RkaMain;
use App\Models\Kak\KakMain;
use App\Models\Kinerja\SubActivity; // Pastikan Model ini di-import
use Illuminate\Support\Facades\DB;

class KakAptikaSeeder extends Seeder
{
    public function run()
    {
        // Data Pejabat Penandatangan (Snapshot)
        $pejabat = [
            'nama' => 'Drs. H. Iskandar, M.Si',
            'nip' => '196801011995031002',
            'jabatan' => 'Kepala Dinas Komunikasi dan Informatika'
        ];

        // ----------------------------------------------------------------------
        // SKENARIO 1: PENGADAAN LAPTOP (Sub Kegiatan: Pengelolaan Nama Domain)
        // ----------------------------------------------------------------------
        $this->seedKak(
            '2.16.03.1.02.01', // Kode Sub Kegiatan (Target RKA)
            [
                'latar_belakang' => "Dalam rangka meningkatkan kinerja operasional pelayanan pendaftaran domain pemerintah daerah, diperlukan perangkat kerja yang memadai. Saat ini, kondisi perangkat komputer yang digunakan oleh tim teknis sudah berusia lebih dari 5 tahun sehingga sering mengalami kendala teknis dan kelambatan proses (lagging). \n\nOleh karena itu, diperlukan peremajaan perangkat berupa Laptop dengan spesifikasi tinggi yang mendukung multitasking dan pengelolaan server secara remote.",
                'dasar_hukum' => [
                    "Peraturan Presiden Nomor 95 Tahun 2018 tentang Sistem Pemerintahan Berbasis Elektronik.",
                    "Peraturan Menteri Kominfo Nomor 5 Tahun 2015 tentang Registrar Nama Domain Instansi Penyelenggara Negara.",
                    "DPA Dinas Komunikasi dan Informatika Tahun Anggaran 2026."
                ],
                'maksud' => "Melakukan pengadaan perangkat keras (hardware) berupa laptop spesifikasi tinggi dan bahan pendukung operasional kantor.",
                'tujuan' => [
                    "Meningkatkan kecepatan respon pelayanan pendaftaran domain.",
                    "Mendukung kinerja tim teknis dalam monitoring server secara realtime.",
                    "Menjamin ketersediaan bahan cetak dan alat tulis kantor untuk administrasi harian."
                ],
                'penerima_manfaat' => "Tim Teknis Pengelola Domain dan Administrator Sistem Pemerintah Daerah.",
                'metode_pelaksanaan' => "E-Purchasing", // Beli di Katalog
                'tempat_pelaksanaan' => "Kantor Dinas Kominfo Prov. Kalbar",
                'tahapan' => [
                    [
                        'uraian' => 'Identifikasi Kebutuhan Spesifikasi Teknis',
                        'output' => 'Dokumen Spesifikasi',
                        'months' => ['Jan']
                    ],
                    [
                        'uraian' => 'Survei Harga dan Pemilihan Penyedia di E-Katalog',
                        'output' => 'Tangkapan Layar Referensi Harga',
                        'months' => ['Jan', 'Feb']
                    ],
                    [
                        'uraian' => 'Proses Pemesanan (Creating Purchasing)',
                        'output' => 'Surat Pesanan',
                        'months' => ['Feb']
                    ],
                    [
                        'uraian' => 'Penerimaan dan Pemeriksaan Barang',
                        'output' => 'Berita Acara Serah Terima (BAST)',
                        'months' => ['Mar']
                    ],
                    [
                        'uraian' => 'Distribusi Perangkat ke Tim Teknis',
                        'output' => 'Tanda Terima Barang',
                        'months' => ['Mar']
                    ]
                ]
            ],
            $pejabat
        );

        // ----------------------------------------------------------------------
        // SKENARIO 2: JASA KONSULTANSI (Sub Kegiatan: Smart City)
        // ----------------------------------------------------------------------
        $this->seedKak(
            '2.16.03.1.02.05', 
            [
                'latar_belakang' => "Pengembangan Ekosistem Smart City di Provinsi Kalimantan Barat memerlukan perencanaan yang matang dan terintegrasi antar Kabupaten/Kota. Masterplan Smart City yang ada saat ini perlu dievaluasi dan diperbarui menyesuaikan dengan perkembangan teknologi terkini serta regulasi terbaru dari Pemerintah Pusat. \n\nKegiatan ini bertujuan untuk menyusun dokumen kajian dan pendampingan teknis kepada Pemerintah Kabupaten/Kota agar indeks SPBE dan maturitas Smart City dapat meningkat.",
                'dasar_hukum' => [
                    "Undang-Undang Nomor 23 Tahun 2014 tentang Pemerintahan Daerah.",
                    "Peraturan Presiden Nomor 95 Tahun 2018 tentang SPBE.",
                    "Peraturan Daerah Provinsi Kalbar tentang Penyelenggaraan Smart Province."
                ],
                'maksud' => "Menyediakan jasa tenaga ahli untuk pendampingan penyusunan Masterplan Smart City.",
                'tujuan' => [
                    "Tersusunnya dokumen revisi Masterplan Smart City Provinsi.",
                    "Terlaksananya bimbingan teknis implementasi dimensi Smart City.",
                    "Tercapainya sinkronisasi program pusat dan daerah."
                ],
                'penerima_manfaat' => "Pemerintah Provinsi dan 14 Kabupaten/Kota se-Kalimantan Barat.",
                'metode_pelaksanaan' => "Seleksi", // Jasa Konsultansi
                'tempat_pelaksanaan' => "Pontianak dan Kab/Kota Lokus Pendampingan",
                'tahapan' => [
                    [
                        'uraian' => 'Rapat Persiapan dan Pembentukan Tim Teknis',
                        'output' => 'SK Tim Teknis & Notulen',
                        'months' => ['Feb']
                    ],
                    [
                        'uraian' => 'Seleksi Tenaga Ahli Perorangan',
                        'output' => 'Kontrak Kerja Tenaga Ahli',
                        'months' => ['Feb', 'Mar']
                    ],
                    [
                        'uraian' => 'Pengumpulan Data Dasar (Baseline) Smart City',
                        'output' => 'Laporan Pendahuluan',
                        'months' => ['Apr', 'Mei']
                    ],
                    [
                        'uraian' => 'Focus Group Discussion (FGD) Penyusunan Masterplan',
                        'output' => 'Dokumen Draft Masterplan',
                        'months' => ['Jun', 'Jul', 'Agt']
                    ],
                    [
                        'uraian' => 'Finalisasi dan Sosialisasi Dokumen',
                        'output' => 'Laporan Akhir & Dokumen Masterplan',
                        'months' => ['Sep', 'Okt']
                    ]
                ]
            ],
            $pejabat
        );

        // ----------------------------------------------------------------------
        // SKENARIO 3: JASA LAINNYA/BARANG (Sub Kegiatan: Data Center)
        // ----------------------------------------------------------------------
        $this->seedKak(
            '2.16.03.1.02.03',
            [
                'latar_belakang' => "Pusat Data (Data Center) Pemerintah Provinsi Kalimantan Barat merupakan infrastruktur vital yang melayani ratusan aplikasi OPD. Saat ini kapasitas penyimpanan (storage) server fisik hampir mencapai batas maksimal (90% usage). Selain itu, sistem pengkabelan jaringan (cabling) di ruang server memerlukan penataan ulang (re-cabling) untuk memenuhi standar TIA-942 agar sirkulasi udara lebih optimal.",
                'dasar_hukum' => [
                    "Peraturan Badan Siber dan Sandi Negara Nomor 4 Tahun 2021 tentang Pedoman Manajemen Keamanan Informasi SPBE.",
                    "Standar Nasional Indonesia (SNI) 8799:2019 tentang Pusat Data."
                ],
                'maksud' => "Meningkatkan kapasitas infrastruktur server dan melakukan perapian jaringan fisik.",
                'tujuan' => [
                    "Menambah kapasitas penyimpanan data hingga 4 Terabyte.",
                    "Memastikan redundansi power supply server terjaga.",
                    "Meningkatkan efisiensi pendinginan ruang server melalui manajemen kabel yang rapi."
                ],
                'penerima_manfaat' => "Seluruh OPD Pengguna Layanan Pusat Data.",
                'metode_pelaksanaan' => "Pengadaan Langsung", // atau E-Purchasing
                'tempat_pelaksanaan' => "Ruang Server Diskominfo (Lt. 1 Gedung Utama)",
                'tahapan' => [
                    [
                        'uraian' => 'Analisis Kapasitas dan Kebutuhan Hardware',
                        'output' => 'Laporan Analisis Kapasitas',
                        'months' => ['Apr']
                    ],
                    [
                        'uraian' => 'Pembelian Perangkat Server & Kabel (E-Purchasing)',
                        'output' => 'Surat Pesanan',
                        'months' => ['Mei']
                    ],
                    [
                        'uraian' => 'Instalasi Server Rackmount dan Konfigurasi RAID',
                        'output' => 'Dokumentasi Instalasi',
                        'months' => ['Jun']
                    ],
                    [
                        'uraian' => 'Pekerjaan Penataan Kabel (Re-cabling)',
                        'output' => 'Foto Sebelum/Sesudah & Berita Acara',
                        'months' => ['Jun', 'Jul']
                    ],
                    [
                        'uraian' => 'Uji Fungsi (Commissioning Test)',
                        'output' => 'Laporan Uji Fungsi',
                        'months' => ['Agt']
                    ]
                ]
            ],
            $pejabat
        );
    }

    /**
     * Helper function untuk insert data KAK
     */
    private function seedKak($kodeSub, $data, $pejabat)
    {
        // --- PERBAIKAN DI SINI ---
        // 1. Cari Sub Activity dulu di Database Kinerja (Modul Kinerja)
        $subActivity = SubActivity::on('modul_kinerja')->where('kode_sub', $kodeSub)->first();

        if (!$subActivity) {
            $this->command->warn("Sub Kegiatan {$kodeSub} tidak ditemukan di modul_kinerja. Skipping...");
            return;
        }

        // 2. Cari RKA di Database Anggaran berdasarkan ID Sub Activity yang didapat
        $rka = RkaMain::on('modul_anggaran')
                    ->where('sub_activity_id', $subActivity->id)
                    ->where('status', 'diterima')
                    ->first();

        if (!$rka) {
            $this->command->warn("RKA untuk sub kegiatan {$kodeSub} tidak ditemukan atau belum diterima. Skipping...");
            return;
        }

        // 3. Buat Data KAK (Modul KAK)
        KakMain::on('modul_kak')->updateOrCreate(
            ['rka_main_id' => $rka->id],
            [
                'sub_activity_id' => $rka->sub_activity_id,
                'latar_belakang' => $data['latar_belakang'],
                'dasar_hukum' => $data['dasar_hukum'], // Otomatis jadi JSON karena $casts di model
                'penerima_manfaat' => $data['penerima_manfaat'],
                'maksud' => $data['maksud'],
                'tujuan' => $data['tujuan'], // Otomatis jadi JSON
                'metode_pelaksanaan' => $data['metode_pelaksanaan'],
                'tempat_pelaksanaan' => $data['tempat_pelaksanaan'],
                
                // Tahapan Pelaksanaan (Include Timeline)
                'tahapan_pelaksanaan' => $data['tahapan'], // Otomatis jadi JSON
                'jadwal_matriks' => null, // Sudah include di tahapan
                
                // Data Pejabat
                'nama_pa_kpa' => $pejabat['nama'],
                'nip_pa_kpa' => $pejabat['nip'],
                'jabatan_pa_kpa' => $pejabat['jabatan']
            ]
        );

        $this->command->info("KAK berhasil dibuat untuk: " . $kodeSub);
    }
}