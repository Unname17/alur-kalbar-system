<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Tabel Perangkat Daerah (OPD) - NODE YANG AKAN DISETUJUI
        Schema::connection('sistem_admin')->create('perangkat_daerah', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perangkat_daerah');
            $table->string('kode_unit')->nullable();
            $table->string('singkatan')->nullable();
            
            // Status input dari sisi OPD
            $table->enum('status_input', ['buka', 'tutup'])->default('buka'); 

            // --- TAMBAHAN BARU: Fitur Verifikasi Sekretariat ---
            // Status persetujuan dari Sekretariat
            $table->enum('status_verifikasi', ['menunggu', 'disetujui', 'ditolak', 'revisi'])
                  ->default('menunggu');
            
            // Mencatat ID User Sekretariat yang melakukan klik 'Setuju'
            $table->unsignedBigInteger('diverifikasi_oleh')->nullable();
            
            // Catatan jika ditolak atau perlu revisi
            $table->text('catatan_verifikasi')->nullable();
            // ---------------------------------------------------

            $table->timestamps();
        });

        // 2. Tabel Jadwal Input
        Schema::connection('sistem_admin')->create('jadwal_penginputan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tahapan');
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai');
            $table->enum('status_aktif', ['buka', 'tutup']);
            $table->timestamps();
        });

        // 3. Tabel Pengguna
        Schema::connection('sistem_admin')->create('pengguna', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_perangkat_daerah');
            
            // Foreign key ke tabel perangkat_daerah
            $table->foreign('id_perangkat_daerah')
                  ->references('id')
                  ->on('perangkat_daerah')
                  ->onDelete('cascade');
            
            $table->string('nama_lengkap');
            $table->string('nip')->unique();
            $table->string('kata_sandi');
            
            // Role user, pastikan ejaan 'sekretariat' dan 'opd' konsisten dengan seeder
            $table->enum('peran', ['admin_utama', 'kepala_dinas', 'staf', 'ppk', 'sekretariat', 'opd']); 
            $table->enum('status_input', ['buka', 'tutup'])->default('buka');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Hapus tabel dengan urutan terbalik (anak dulu baru induk)
        Schema::connection('sistem_admin')->dropIfExists('pengguna');
        Schema::connection('sistem_admin')->dropIfExists('jadwal_penginputan');
        Schema::connection('sistem_admin')->dropIfExists('perangkat_daerah');
    }
};