<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProcurementVendorSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk tabel procurement_vendors.
     */
    public function run(): void
    {
        $connection = 'modul_pengadaan';

        $vendors = [
            [
                'id' => 1,
                'nama_perusahaan' => 'SIPLAH Blibli (HP Official)',
                'bentuk_usaha' => 'PT',
                'npwp' => '01.234.567.8-901.000',
                'alamat' => 'Jl. Letjen S. Parman No. 28, Tanjung Duren Selatan, Jakarta Barat',
                'email' => 'support@siplahblibli.com',
                'no_telepon' => '021-12345678',
                'nama_direktur' => 'Budi Santoso',
                'jabatan_direktur' => 'Direktur Utama',
                'nama_bank' => 'Bank Mandiri',
                'no_rekening' => '1234567890',
                'nama_pemilik_rekening' => 'PT Global Digital Niaga',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'nama_perusahaan' => 'PT Borneo Digital Solusi',
                'bentuk_usaha' => 'PT',
                'npwp' => '02.987.654.3-701.000',
                'alamat' => 'Jl. Ahmad Yani No. 10, Kel. Akcaya, Kec. Pontianak Selatan, Kota Pontianak',
                'email' => 'info@borneodigital.com',
                'no_telepon' => '0561-765432',
                'nama_direktur' => 'Hendra Wijaya',
                'jabatan_direktur' => 'Direktur',
                'nama_bank' => 'Bank Kalbar',
                'no_rekening' => '1029384756',
                'nama_pemilik_rekening' => 'PT Borneo Digital Solusi',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 3,
                'nama_perusahaan' => 'CV Pontianak IT Media',
                'bentuk_usaha' => 'CV',
                'npwp' => '03.111.222.3-701.000',
                'alamat' => 'Jl. Gajah Mada No. 45, Pontianak',
                'email' => 'sales@pontianakit.com',
                'no_telepon' => '0561-123321',
                'nama_direktur' => 'Siti Aminah',
                'jabatan_direktur' => 'Direktur',
                'nama_bank' => 'Bank BNI',
                'no_rekening' => '9876543210',
                'nama_pemilik_rekening' => 'Siti Aminah',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        foreach ($vendors as $vendor) {
            DB::connection($connection)->table('procurement_vendors')->updateOrInsert(
                ['id' => $vendor['id']],
                $vendor
            );
        }
    }
}