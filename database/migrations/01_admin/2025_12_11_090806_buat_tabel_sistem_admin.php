<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'sistem_admin'; 

    public function up()
    {
        // 1. Tabel Perangkat Daerah
        Schema::connection('sistem_admin')->create('perangkat_daerah', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perangkat_daerah');
            $table->string('kode_unit')->nullable();
            $table->string('singkatan')->nullable();
            // Kolom ini sudah ada di seeder Anda
            $table->enum('status_input', ['buka', 'tutup'])->default('buka');
            $table->enum('status_verifikasi', ['menunggu', 'disetujui', 'ditolak', 'revisi'])->default('menunggu');
            $table->unsignedBigInteger('diverifikasi_oleh')->nullable();
            $table->text('catatan_verifikasi')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Pengguna (PERBAIKAN: Tambahkan status_input di sini)
        Schema::connection('sistem_admin')->create('pengguna', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_perangkat_daerah');
            $table->string('nama_lengkap');
            $table->string('nip')->unique();
            $table->string('kata_sandi');
            $table->string('peran'); 
            
            // TAMBAHKAN KOLOM INI AGAR SEEDER BERHASIL
            $table->enum('status_input', ['buka', 'tutup'])->default('buka'); 
            
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('id_perangkat_daerah')->references('id')->on('perangkat_daerah')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::connection('sistem_admin')->dropIfExists('pengguna');
        Schema::connection('sistem_admin')->dropIfExists('perangkat_daerah');
    }
};