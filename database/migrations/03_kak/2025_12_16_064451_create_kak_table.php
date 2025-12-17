<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'modul_kak'; // Database Sendiri

    public function up()
{
    Schema::connection('modul_kak')->create('kak', function (Blueprint $table) {
        $table->id();
        
        // Relasi ke Pohon Kinerja
        $table->unsignedBigInteger('pohon_kinerja_id')->index(); 

        // Field Standar KAK
        $table->string('judul_kak')->nullable();      
        $table->string('kode_proyek')->nullable();     
        $table->text('dasar_hukum')->nullable();      
        $table->text('latar_belakang')->nullable(); 
        $table->text('maksud_tujuan')->nullable();  
        $table->text('sasaran')->nullable();         
        $table->text('metode_pelaksanaan')->nullable(); 
        $table->string('lokasi')->nullable();        
        $table->text('penerima_manfaat')->nullable(); 
        $table->date('waktu_mulai')->nullable();
        $table->date('waktu_selesai')->nullable();

        // --- STATUS & APPROVAL ---
        // 0: Draft, 1: Diajukan, 2: Disetujui, 3: Ditolak
        $table->tinyInteger('status')->default(0); 
        
        // --- INI KOLOM BARU YANG KITA TAMBAHKAN ---
        $table->string('nomor_kak')->nullable(); 
        
        $table->text('catatan_sekretariat')->nullable(); 
        
        $table->timestamps();
    });
}

    public function down()
    {
        Schema::connection('modul_kak')->dropIfExists('kak');
    }
};