<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // 1. Bersihkan Data Lama (Urutan: Child dulu baru Parent)
        DB::connection('sistem_admin')->statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::connection('sistem_admin')->table('pengguna')->truncate();
        DB::connection('sistem_admin')->table('perangkat_daerah')->truncate();
        DB::connection('sistem_admin')->statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. INPUT PERANGKAT DAERAH (Pastikan ID 1, 2, dan 5 tersedia)
        DB::connection('sistem_admin')->table('perangkat_daerah')->insert([
            [
                'id' => 1, 
                'nama_perangkat_daerah' => 'Sekretariat Daerah Prov. Kalbar',
                'singkatan' => 'SETDA',
                'status_input' => 'buka',
                'created_at' => now(),
            ],
            [
                'id' => 2, 
                'nama_perangkat_daerah' => 'Badan Perencanaan Pembangunan Daerah',
                'singkatan' => 'BAPPEDA',
                'status_input' => 'buka',
                'created_at' => now(),
            ],
            [
                'id' => 5, 
                'nama_perangkat_daerah' => 'Dinas Kominfo Prov. Kalbar',
                'singkatan' => 'DISKOMINFO',
                'status_input' => 'buka',
                'created_at' => now(),
            ]
        ]);

        // 3. INPUT PENGGUNA (NIP harus sesuai dengan tabel di login.blade.php)
        $users = [
            [
                'id_perangkat_daerah' => 1, // Sekarang ID 1 sudah ada
                'nama_lengkap' => 'Super Administrator',
                'nip' => 'admin', 
                'peran' => 'admin_utama',
            ],
            [
                'id_perangkat_daerah' => 2,
                'nama_lengkap' => 'Validator Bappeda',
                'nip' => '19850101', 
                'peran' => 'sekretariat',
            ],
            [
                'id_perangkat_daerah' => 5,
                'nama_lengkap' => 'Kepala Dinas Kominfo',
                'nip' => '19800101', 
                'peran' => 'kepala_dinas',
            ],
            [
                'id_perangkat_daerah' => 5,
                'nama_lengkap' => 'Staf Perencana Aptika',
                'nip' => '19950101', 
                'peran' => 'staf',
            ],
            [
                'id_perangkat_daerah' => 5,
                'nama_lengkap' => 'PPK Aptika',
                'nip' => '19900101', 
                'peran' => 'ppk',
            ],
        ];

        foreach ($users as $user) {
            DB::connection('sistem_admin')->table('pengguna')->insert(array_merge($user, [
                'kata_sandi' => Hash::make('password'), 
                'status_input' => 'buka',
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        echo "✅ Sukses! Database Admin berhasil di-reset dan disinkronkan.\n";
    }
}