<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Koneksi khusus untuk Modul Pengadaan
        $conn = 'modul_pengadaan';

        // 1. TABEL VENDORS
        if (!Schema::connection($conn)->hasTable('vendors')) {
            Schema::connection($conn)->create('vendors', function (Blueprint $table) {
                $table->id();
                $table->string('nama_perusahaan');
                $table->string('npwp')->unique();
                $table->string('nama_direktur')->nullable();
                $table->text('alamat');
                $table->string('email')->nullable();
                $table->string('nomor_telepon');
                $table->string('nama_bank');
                $table->string('nomor_rekening');
                $table->string('nama_rekening');
                $table->timestamps();
            });
        }

        // 2. TABEL PENGADAANS
        if (!Schema::connection($conn)->hasTable('pengadaans')) {
            Schema::connection($conn)->create('pengadaans', function (Blueprint $table) {
                $table->id();
                // Link ke Modul Anggaran (Cross-Database)
                $table->unsignedBigInteger('rka_id')->comment('Refer ke database alur_kalbar_anggaran.rka_main'); 
                $table->unsignedBigInteger('kak_id')->comment('Refer ke database alur_kalbar_anggaran.rka_perencanaan');
                
                $table->unsignedBigInteger('vendor_id')->nullable();
                $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');

                $table->enum('metode_pengadaan', ['katalog', 'pl', 'tender'])->nullable();
                $table->integer('target_volume')->default(0);
                $table->integer('realisasi_volume')->default(0);
                $table->string('status_pengadaan')->default('berjalan');
                $table->timestamps();
            });
        }

        // 3. TABEL PENGADAAN_DOCUMENTS
        if (!Schema::connection($conn)->hasTable('pengadaan_documents')) {
            Schema::connection($conn)->create('pengadaan_documents', function (Blueprint $table) {
                $table->id();
                // Foreign key lokal tetap bisa digunakan
                $table->foreignId('pengadaan_id')->constrained('pengadaans')->onDelete('cascade');
                $table->integer('urutan_dokumen');
                $table->string('nama_dokumen');
                $table->string('file_path')->nullable();
                $table->boolean('is_verified')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        $conn = 'modul_pengadaan';
        Schema::connection($conn)->dropIfExists('pengadaan_documents');
        Schema::connection($conn)->dropIfExists('pengadaans');
        Schema::connection($conn)->dropIfExists('vendors');
    }
};