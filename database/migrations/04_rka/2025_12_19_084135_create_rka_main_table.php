<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tentukan koneksi database yang digunakan
     */
    protected $connection = 'modul_anggaran';

    /**
     * Jalankan migrasi
     */
    public function up(): void
    {
        Schema::connection($this->connection)->create('rka_main', function (Blueprint $table) {
            $table->id();
            // ID KAK sebagai jembatan dari modul perencanaan
            $table->unsignedBigInteger('kak_id')->unique(); 
            $table->string('nomor_rka')->nullable();
            $table->decimal('total_anggaran', 15, 2)->default(0);
            
            // Status untuk alur verifikasi anggaran
            $table->enum('status_anggaran', ['draft', 'pengajuan', 'disetujui', 'ditolak'])->default('draft');
            $table->timestamps();

            // Index untuk mempercepat relasi antar modul
            $table->index('kak_id');
        });
    }

    /**
     * Batalkan migrasi
     */
    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('rka_main');
    }
};