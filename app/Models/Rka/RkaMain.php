<?php

namespace App\Models\Rka;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kinerja\SubActivity; // Model dari modul sebelah

class RkaMain extends Model
{
    protected $connection = 'modul_anggaran'; // Koneksi Database Anggaran
    protected $table = 'rka_mains';
    protected $guarded = ['id'];

    protected $fillable = [
        'sub_activity_id',
        'sub_unit_organisasi', // Baru: Untuk identitas di PDF
        'sumber_dana',
        'lokasi_kegiatan',
        'waktu_pelaksanaan',
        'kelompok_sasaran',
        'jenis_layanan',       // Baru: Step 3
        'spm',                 // Baru: Step 3
        'tim_anggaran',        // Baru: Step 3 (Data JSON TAPD)
        'nip_pptk',
        'nama_pptk',
        'pagu_indikatif',
        'total_anggaran',
        'status',
    ];

    /**
     * Agar data tim_anggaran yang berbentuk JSON otomatis menjadi array saat dipanggil
     * dan otomatis menjadi JSON saat disimpan ke database.
     */
    protected $casts = [
        'tim_anggaran' => 'array',
    ];

    // Relasi Lintas Database ke Sub Kegiatan (Modul Kinerja)
    public function subActivity()
    {
        return $this->setConnection('modul_kinerja')->belongsTo(SubActivity::class, 'sub_activity_id');
    }

    // Relasi ke Rincian Belanja (Satu Database)
    public function details()
    {
        return $this->hasMany(RkaDetail::class, 'rka_main_id');
    }
    public function kak()
{
    // Relasi ke tabel kak_mains di database sebelah (modul_kak)
    return $this->hasOne(\App\Models\Kak\KakMain::class, 'rka_main_id', 'id');
}
}