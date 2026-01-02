<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerangkatDaerahSeeder extends Seeder
{
    public function run()
    {
        // Pastikan menggunakan koneksi 'sistem_admin'
        DB::connection('sistem_admin')->table('perangkat_daerah')->insert([
            [
                'nama_perangkat_daerah' => 'Dinas Komunikasi dan Informatika',
                'kode_unit' => '2.10.01',
                'singkatan' => 'DISKOMINFO',
                'status_input' => 'buka',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_perangkat_daerah' => 'Badan Perencanaan Pembangunan Daerah',
                'kode_unit' => '4.01.01',
                'singkatan' => 'BAPPEDA',
                'status_input' => 'buka',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_perangkat_daerah' => 'Inspektorat Daerah',
                'kode_unit' => '5.01.01',
                'singkatan' => 'INSPEKTORAT',
                'status_input' => 'buka',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}