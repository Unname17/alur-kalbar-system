<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Pastikan koneksi ke modul_kinerja (sesuaikan jika database terpisah)
        Schema::connection('modul_kinerja')->create('akses_penambahan_kinerja', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('opd_id'); // Rule ini berlaku untuk OPD mana
            $table->string('role_target'); // Misal: 'opd' (Kepala Dinas/Staff)
            
            // Parent ID yang DIIZINKAN untuk ditambah anak
            $table->unsignedBigInteger('parent_id_allowed'); 
            
            // Jenis Kinerja yang DIIZINKAN dibuat (misal: hanya boleh buat 'kegiatan')
            $table->string('jenis_kinerja_allowed'); 
            
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            // Foreign Key (Opsional, sesuaikan dengan tabel pohon_kinerja Anda)
            // $table->foreign('parent_id_allowed')->references('id')->on('pohon_kinerja')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::connection('modul_kinerja')->dropIfExists('akses_penambahan_kinerja');
    }
};