<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // PENTING: Untuk enkripsi password

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Gunakan connection('sistem_admin') sesuai migration
        
        // 1. BERSIHKAN DATA LAMA (Urutan: Anak dulu baru Induk)
        DB::connection('sistem_admin')->statement('SET FOREIGN_KEY_CHECKS=0;');
        
        DB::connection('sistem_admin')->table('pengguna')->truncate();        // Hapus User dulu
        DB::connection('sistem_admin')->table('perangkat_daerah')->truncate(); // Baru hapus OPD
        
        DB::connection('sistem_admin')->statement('SET FOREIGN_KEY_CHECKS=1;');

        // ==========================================
        // 2. INSERT PERANGKAT DAERAH (OPD)
        // ==========================================
        
        // ID 1-4 (Dummy)
        for ($i = 1; $i <= 4; $i++) {
            DB::connection('sistem_admin')->table('perangkat_daerah')->insert([
                'id' => $i,
                'nama_perangkat_daerah' => 'OPD Dummy ' . $i,
                'kode_unit' => '0.0.' . $i,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // ID 5 (DISKOMINFO - PENTING)
        DB::connection('sistem_admin')->table('perangkat_daerah')->insert([
            'id' => 5,
            'nama_perangkat_daerah' => 'Dinas Kominfo Prov. Kalbar',
            'kode_unit' => '2.10.01',
            'singkatan' => 'DISKOMINFO',
            'status_input' => 'buka',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // ID 6-7 (Dummy)
        DB::connection('sistem_admin')->table('perangkat_daerah')->insert(['id' => 6, 'nama_perangkat_daerah' => 'Dummy 6', 'kode_unit' => '0.0.6', 'created_at' => now(), 'updated_at' => now()]);
        DB::connection('sistem_admin')->table('perangkat_daerah')->insert(['id' => 7, 'nama_perangkat_daerah' => 'Dummy 7', 'kode_unit' => '0.0.7', 'created_at' => now(), 'updated_at' => now()]);

        // ID 8 (DINSOS - PENTING)
        DB::connection('sistem_admin')->table('perangkat_daerah')->insert([
            'id' => 8,
            'nama_perangkat_daerah' => 'Dinas Sosial',
            'kode_unit' => '1.02.01',
            'singkatan' => 'DINSOS',
            'status_input' => 'buka',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // ==========================================
        // 3. INSERT PENGGUNA (USERS)
        // ==========================================

        // A. ADMIN UTAMA (SUPER ADMIN) - Login Saya?
        // Biasanya Admin Utama ditaruh di OPD Setda atau Bappeda, disini kita taruh di Dummy 1 saja
        DB::connection('sistem_admin')->table('pengguna')->insert([
            'id_perangkat_daerah' => 1, // Nempel di OPD Dummy 1
            'nama_lengkap' => 'Super Administrator',
            'nip' => 'admin',           // USERNAME LOGIN
            'kata_sandi' => Hash::make('password'), // PASSWORD LOGIN
            'peran' => 'admin_utama',
            'status_input' => 'buka',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // B. KEPALA DINAS KOMINFO (Untuk Validasi Pohon Kinerja)
        DB::connection('sistem_admin')->table('pengguna')->insert([
            'id_perangkat_daerah' => 5, // ID Diskominfo
            'nama_lengkap' => 'Kepala Dinas Kominfo',
            'nip' => '19800101',         // NIP KADIS
            'kata_sandi' => Hash::make('password'),
            'peran' => 'kepala_dinas',
            'status_input' => 'buka',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // C. STAF PERENCANAAN KOMINFO (Untuk Input Pohon Kinerja)
        DB::connection('sistem_admin')->table('pengguna')->insert([
            'id_perangkat_daerah' => 5, // ID Diskominfo
            'nama_lengkap' => 'Staf Perencana Kominfo',
            'nip' => '19900101',         // NIP STAF
            'kata_sandi' => Hash::make('password'),
            'peran' => 'staf',
            'status_input' => 'buka',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        echo "✅ Berhasil mengisi OPD & Users (Admin, Kadis, Staf) di koneksi 'sistem_admin'.\n";
    }
}