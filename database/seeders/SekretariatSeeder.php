<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SekretariatSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cari ID di koneksi sistem_admin
        $pd = DB::connection('sistem_admin')->table('perangkat_daerah')
                ->where('nama_perangkat_daerah', 'like', '%Sekretariat%')
                ->first();

        // Fallback jika tidak ketemu
        if (!$pd) {
            $pd = DB::connection('sistem_admin')->table('perangkat_daerah')->first();
        }

        // Jika tabel kosong, buat dummy di koneksi sistem_admin
        if (!$pd) {
            $id_pd = DB::connection('sistem_admin')->table('perangkat_daerah')->insertGetId([
                'nama_perangkat_daerah' => 'Sekretariat Daerah (System)',
                'kode_perangkat_daerah' => 'SETDA-01',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $id_pd = $pd->id;
        }

        // 2. Insert Pengguna di koneksi sistem_admin
        DB::connection('sistem_admin')->table('pengguna')->insert([
            'id_perangkat_daerah' => $id_pd,
            'nama_lengkap'        => 'Verifikator Sekretariat',
            'nip'                 => '198501012010012009',
            'kata_sandi'          => Hash::make('password'),
            'peran'               => 'sekretariat',
            'status_input'        => 'buka',
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);
    }
}