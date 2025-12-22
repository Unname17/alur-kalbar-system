<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        // Gunakan koneksi khusus modul pengadaan
        $connection = DB::connection('modul_pengadaan');

        $vendors = [
            [
                'nama_perusahaan' => 'PT. Borneo Digital Solusi',
                'npwp' => '01.234.567.8-701.000',
                'nama_direktur' => 'Andi Setiawan',
                'alamat' => 'Jl. Ahmad Yani No. 12, Pontianak',
                'email' => 'info@borneodigital.id',
                'nomor_telepon' => '081234567890',
                'nama_bank' => 'Bank Kalbar',
                'nomor_rekening' => '1029384756',
                'nama_rekening' => 'PT Borneo Digital Solusi',
                'created_at' => now(),
            ],
            [
                'nama_perusahaan' => 'CV. Khatulistiwa Tech',
                'npwp' => '02.987.654.3-701.000',
                'nama_direktur' => 'Budi Pratama',
                'alamat' => 'Jl. Reformasi No. 5, Pontianak',
                'email' => 'kontak@khatulistiwa.tech',
                'nomor_telepon' => '085299887766',
                'nama_bank' => 'Bank Mandiri',
                'nomor_rekening' => '1122334455',
                'nama_rekening' => 'CV Khatulistiwa Tech',
                'created_at' => now(),
            ],
            [
                'nama_perusahaan' => 'PT. Inti Jasa Kalbar',
                'npwp' => '03.456.789.1-701.000',
                'nama_direktur' => 'Siti Aminah',
                'alamat' => 'Jl. Gajah Mada No. 88, Pontianak',
                'email' => 'admin@intijasa.co.id',
                'nomor_telepon' => '081122334455',
                'nama_bank' => 'Bank BNI',
                'nomor_rekening' => '5566778899',
                'nama_rekening' => 'PT Inti Jasa Kalbar',
                'created_at' => now(),
            ]
        ];

        foreach ($vendors as $vendor) {
            $connection->table('vendors')->insert($vendor);
        }
    }
}