<?php

namespace App\Models\Pengadaan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Rka\RkaMain;

class ProcurementPackage extends Model
{
    use SoftDeletes;

    // Koneksi ke database khusus pengadaan
    protected $connection = 'modul_pengadaan';
    protected $table = 'procurement_packages';
    
    protected $guarded = ['id'];

    protected $fillable = [
        'nama_paket',
        'status_tahapan',
        'perubahan_ke',
        'tanggal_perubahan',
        'pagu_paket',
        'pertimbangan_akun',
        'opsi_pdn',
        'alasan_pdn',
        'jenis_pengadaan',
        'alasan_pemilihan_jenis',
        'metode_pemilihan',
        'alasan_metode_pemilihan',
        'kode_kbki',
        'deskripsi_kbki',
        'is_pdn',
        'is_umkm',
        'lokasi_pekerjaan',
        'jadwal_pelaksanaan',
        'uraian_pekerjaan',
        'tanggal_penyusunan',
        'nama_pa_kpa',
        'nip_pa_kpa',
        'nama_tenaga_ahli',
        'hps_total',
        'nilai_kontrak'
    ];

    // --- RELASI KE MODUL LAIN ---
    public function rka()
    {
        return $this->belongsTo(RkaMain::class, 'rka_main_id', 'id');
    }

    // --- RELASI KE TABEL PENDUKUNG (HAS ONE / HAS MANY) ---
    
    // 1. Strategi & Analisis (Doc 2 & 3)
    public function preparation()
    {
        return $this->hasOne(ProcurementPreparation::class, 'package_id');
    }

    // 2. Item Barang/Jasa (Doc 4 & 5)
    public function items()
    {
        return $this->hasMany(ProcurementItem::class, 'package_id');
    }

    // 3. Analisis Pasar (Doc 6 & 7)
    public function marketAnalyses()
    {
        return $this->hasMany(ProcurementMarketAnalysis::class, 'package_id');
    }

    // 4. Negosiasi (Doc 9)
    public function negotiations()
    {
        return $this->hasMany(ProcurementNegotiation::class, 'package_id');
    }

    // 5. Kontrak (Doc 10)
    public function contract()
    {
        return $this->hasOne(ProcurementContract::class, 'package_id');
    }
    public function price_references()
{
    return $this->hasMany(ProcurementPriceReference::class, 'package_id');
}
    
}