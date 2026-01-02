<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseAdminSeeder extends Seeder
{
    public function run()
    {
        $conn = DB::connection('sistem_admin');

        // 1. Seed Perangkat Daerah (Gunakan updateOrInsert agar tidak duplikat)
        $kodePd = '1.02.0.00.0.00.01.0000';
        $conn->table('perangkat_daerah')->updateOrInsert(
            ['kode_pd' => $kodePd], // Kunci pencarian
            [
                'nama_pd' => 'Dinas Komunikasi dan Informatika Prov. Kalbar',
                'singkatan' => 'DISKOMINFO',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
        $pdId = $conn->table('perangkat_daerah')->where('kode_pd', $kodePd)->value('id');

        // 2. Seed Bidang
        $kodeBidang = 'BID-001';
        $conn->table('bidang')->updateOrInsert(
            ['kode_bidang' => $kodeBidang, 'pd_id' => $pdId],
            [
                'nama_bidang' => 'Aplikasi Informatika (APTIKA)',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
        $bidangId = $conn->table('bidang')->where('kode_bidang', $kodeBidang)->value('id');

        // 3. Seed Roles
        $roles = [
            ['name' => 'bappeda', 'display_name' => 'Admin Bappeda'],
            ['name' => 'kadis', 'display_name' => 'Kepala Dinas'],
            ['name' => 'kabid', 'display_name' => 'Kepala Bidang'],
            ['name' => 'staff', 'display_name' => 'Staff Pelaksana'],
        ];

        foreach ($roles as $role) {
            $conn->table('roles')->updateOrInsert(
                ['name' => $role['name']],
                ['display_name' => $role['display_name'], 'updated_at' => now(), 'created_at' => now()]
            );
        }

        // Ambil ID roles untuk keperluan seed users
        $roleIds = $conn->table('roles')->pluck('id', 'name');

        // 4. Seed Users (Gunakan NIP sebagai kunci unik)
        $users = [
            [
                'nip' => '199001012024011001',
                'nama_lengkap' => 'Admin Bappeda Kalbar',
                'role_id' => $roleIds['bappeda'],
                'pd_id' => $pdId,
                'bidang_id' => null,
            ],
            [
                'nip' => '197501012000011001',
                'nama_lengkap' => 'Kepala Dinas Kominfo',
                'role_id' => $roleIds['kadis'],
                'pd_id' => $pdId,
                'bidang_id' => null,
            ],
            [
                'nip' => '198001012005011002',
                'nama_lengkap' => 'Kabid Aptika Kominfo',
                'role_id' => $roleIds['kabid'],
                'pd_id' => $pdId,
                'bidang_id' => $bidangId,
            ],
            [
                'nip' => '199801012024011004',
                'nama_lengkap' => 'Staff Aptika Kominfo',
                'role_id' => $roleIds['staff'],
                'pd_id' => $pdId,
                'bidang_id' => $bidangId,
            ]
        ];

        foreach ($users as $user) {
            $conn->table('users')->updateOrInsert(
                ['nip' => $user['nip']], // Kunci pencarian unik
                [
                    'nama_lengkap' => $user['nama_lengkap'],
                    'password' => Hash::make('password'),
                    'role_id' => $user['role_id'],
                    'pd_id' => $user['pd_id'],
                    'bidang_id' => $user['bidang_id'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $this->command->info('Database Admin berhasil di-seed (tanpa duplikasi)!');
    }
}