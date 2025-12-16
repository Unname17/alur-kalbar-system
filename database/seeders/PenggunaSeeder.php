<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        // PENTING: Gunakan connection('sistem_admin')
        $all_pd = DB::connection('sistem_admin')->table('perangkat_daerah')->get();

        foreach ($all_pd as $pd) {
            // Cek di koneksi admin
            $exists = DB::connection('sistem_admin')->table('pengguna')
                        ->where('id_perangkat_daerah', $pd->id)
                        ->exists();

            if (!$exists) {
                DB::connection('sistem_admin')->table('pengguna')->insert([
                    'id_perangkat_daerah' => $pd->id,
                    'nama_lengkap'        => 'Admin ' . $pd->nama_perangkat_daerah,
                    'nip'                 => '19900101' . str_pad($pd->id, 3, '0', STR_PAD_LEFT) . '1001',
                    'kata_sandi'          => Hash::make('password'),
                    'peran'               => 'opd',
                    'status_input'        => 'buka',
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            }
        }
    }
}