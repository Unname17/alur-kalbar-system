<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menentukan koneksi database yang digunakan
     */
    protected $connection = 'modul_kinerja'; 

    public function up()
    {
        Schema::connection('modul_kinerja')->create('log_aktivitas', function (Blueprint $table) {
            $table->id();
            
            // 1. IDENTITAS (SIAPA)
            // Relasi ke pengguna di database sistem_admin
            $table->unsignedBigInteger('user_id'); 
            // Mencatat OPD asal pelaku saat itu agar mudah difilter
            $table->unsignedBigInteger('opd_id')->nullable(); 
            
            // 2. AKTIVITAS (APA)
            // Contoh: LOGIN, SIMPAN_DATA, VALIDASI_SUKSES
            $table->string('aktivitas'); 
            // Kategori modul: KINERJA, AKSES, ADMIN, dll
            $table->string('modul')->nullable(); 
            // Penjelasan lengkap (Contoh: "Menyetujui Program A")
            $table->text('deskripsi')->nullable(); 
            
            // 3. METADATA TEKNIS (DARI MANA & BAGAIMANA)
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable(); // Mencatat Browser & Device
            
            // Menyimpan snapshot data yang dikirim (Payload) untuk audit
            $table->json('payload')->nullable(); 
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::connection('modul_kinerja')->dropIfExists('log_aktivitas');
    }
};