<?php

namespace App\Models\Kak;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Kinerja\PohonKinerja;
// --- PERBAIKAN 1: Pastikan Model Pengguna di-import ---
// Cek folder App/Models Anda, apakah namanya Pengguna.php atau User.php?
// Jika User.php, ganti jadi: use App\Models\User;
use App\Models\Admin\Pengguna; 

class Kak extends Model
{
    use HasFactory;
    
    protected $connection = 'modul_kak'; 
    protected $table = 'kak';
    protected $guarded = []; // Jika sudah pakai guarded [], fillable tidak wajib, tapi dibiarkan juga tidak apa-apa
    protected $fillable = [
        'pohon_kinerja_id', 'judul_kak', 'kode_proyek', 'dasar_hukum', 
        'latar_belakang', 'maksud_tujuan', 'sasaran', 'metode_pelaksanaan', 
        'lokasi', 'penerima_manfaat', 'waktu_mulai', 'waktu_selesai',
        'status', 'catatan_sekretariat','nomor_kak',
        'id_pengguna' // Pastikan kolom ini ada di database jika mau diisi massal
    ];

    // Relasi Cross-Database ke Modul Kinerja
    public function pohonKinerja()
    {
        return $this->belongsTo(PohonKinerja::class, 'pohon_kinerja_id');
    }

    public function timPelaksana()
    {
        return $this->hasMany(KakTimPelaksana::class, 'kak_id');
    }
    
    public function timelines() {
        return $this->hasMany(KakTimeline::class, 'kak_id');
    }

    // --- PERBAIKAN 2: FUNGSI RINCIAN BELANJA CUKUP SATU SAJA ---
    public function rincianBelanja()
    {
        return $this->hasMany(\App\Models\Rka\KakDetail::class, 'kak_id');
    }

    /**
     * Helper untuk menghitung Total Anggaran KAK
     */
    public function getTotalAnggaranAttribute()
    {
        return $this->rincianBelanja()->sum('total_harga');
    }

    // --- PERBAIKAN 3: RELASI USER ---
    public function user()
    {
        // Pastikan kolom di tabel 'kak' namanya benar-benar 'id_pengguna'
        // Jika di database namanya 'user_id', ubah parameter kedua jadi 'user_id'
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }
}