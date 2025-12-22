<?php

namespace App\Models\Pengadaan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengadaan extends Model
{
    // Pastikan menggunakan koneksi database pengadaan
    protected $connection = 'modul_pengadaan';
    protected $table = 'pengadaans';

    protected $fillable = [
        'rka_id', 
        'kak_id', 
        'vendor_id', 
        'metode_pengadaan', 
        'target_volume', 
        'realisasi_volume', 
        'status_pengadaan'
    ];

    /**
     * Relasi ke Header RKA (Cross-Database ke modul_anggaran)
     */
    public function rka(): BelongsTo
    {
        // Laravel akan otomatis menggunakan koneksi dari model Rka
        return $this->belongsTo(\App\Models\Rka\Rka::class, 'rka_id');
    }

    /**
     * Relasi ke Detail Perencanaan/KAK (Cross-Database)
     * Inilah fungsi yang menyebabkan error 'undefined relationship' jika tidak ada
     */
    public function rkaPerencanaan(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Rka\RkaPerencanaan::class, 'kak_id', 'kak_id');
    }

    /**
     * Relasi ke Vendor (Satu database di modul_pengadaan)
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    /**
     * Relasi ke 9 Dokumen Pengadaan (Satu database di modul_pengadaan)
     */
    public function documents(): HasMany
    {
        return $this->hasMany(PengadaanDocument::class, 'pengadaan_id');
    }
}