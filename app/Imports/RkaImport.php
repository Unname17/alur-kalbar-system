<?php

namespace App\Imports;

use App\Models\Rka\KakDetail;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RkaImport implements ToModel, WithHeadingRow
{
    protected $kak_id;

    public function __construct($kak_id)
    {
        $this->kak_id = $kak_id;
    }

    /**
     * TENTUKAN POSISI HEADER
     * Karena ada 6 baris instruksi di atas, maka Header ada di Baris 7.
     */
    public function headingRow(): int
    {
        return 8; // <--- UBAH KE 7
    }

    public function model(array $row)
    {
        // Skip jika nama barang kosong (misal baris kosong tidak sengaja terupload)
        if (!isset($row['nama_barang_jasa_wajib']) && !isset($row['nama_barang'])) {
            return null;
        }

        // Mapping Data (Sesuaikan key array dengan nama header di Excel yang di-lowercase & snake_case)
        // Header "Nama Barang / Jasa (Wajib)" biasanya jadi 'nama_barang_jasa_wajib'
        
        return new KakDetail([
            'kak_id'       => $this->kak_id,
            'ssh_id'       => null,
            'kategori'     => $row['kategori_wajib'] ?? ($row['kategori'] ?? 'SBU'),
            'nama_barang'  => $row['nama_barang_jasa_wajib'] ?? $row['nama_barang'],
            'harga_satuan' => $row['harga_satuan_wajib'] ?? $row['harga'],
            'satuan'       => $row['satuan_wajib'] ?? $row['satuan'],
            'volume'       => $row['volume_wajib'] ?? $row['volume'],
            'total_harga'  => ($row['harga_satuan_wajib'] ?? 0) * ($row['volume_wajib'] ?? 0),
            'keterangan'   => $row['keterangan'] ?? 'Import Excel',
            'is_manual'    => true,
            'is_verified'  => 1,
        ]);
    }
}