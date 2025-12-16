<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JadwalInputSeeder extends Seeder
{
    public function run()
    {
        DB::connection('sistem_admin')->table('jadwal_penginputan')->insert([
            [
                'nama_tahapan' => 'Perencanaan Murni 2025',
                'waktu_mulai' => '2025-01-01 00:00:00',
                'waktu_selesai' => '2025-03-31 23:59:59',
                'status_aktif' => 'buka',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_tahapan' => 'Perencanaan Perubahan 2025',
                'waktu_mulai' => '2025-08-01 00:00:00',
                'waktu_selesai' => '2025-09-30 23:59:59',
                'status_aktif' => 'tutup',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}