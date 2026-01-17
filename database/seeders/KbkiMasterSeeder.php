<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KbkiMasterSeeder extends Seeder
{
    public function run()
    {
        $kbkis = [
            ['id' => 1, 'kode_kbki' => '45220', 'deskripsi_kbki' => 'Mesin pengolah data otomatis digital portabel (laptop, notebook, dan subnotebook)'],
            ['id' => 2, 'kode_kbki' => '45230', 'deskripsi_kbki' => 'Mesin pengolah data otomatis digital lainnya (Server, Desktop PC)'],
            ['id' => 3, 'kode_kbki' => '83141', 'deskripsi_kbki' => 'Jasa konsultansi piranti lunak (Software) dan sistem informasi'],
            ['id' => 4, 'kode_kbki' => '83131', 'deskripsi_kbki' => 'Jasa konsultansi konfigurasi perangkat keras (Hardware)'],
            ['id' => 5, 'kode_kbki' => '46211', 'deskripsi_kbki' => 'Perangkat transmisi untuk komunikasi radio/televisi dan kabel jaringan'],
            ['id' => 6, 'kode_kbki' => '32129', 'deskripsi_kbki' => 'Kertas dan karton lainnya yang digunakan untuk tulisan atau cetakan'],
            ['id' => 7, 'kode_kbki' => '32190', 'deskripsi_kbki' => 'Barang-barang dari kertas atau karton lainnya (Tinta, Toner)'],
        ];

        foreach ($kbkis as $kbki) {
            DB::connection('modul_pengadaan')->table('kbki_masters')->updateOrInsert(['id' => $kbki['id']], $kbki);
        }
    }
}