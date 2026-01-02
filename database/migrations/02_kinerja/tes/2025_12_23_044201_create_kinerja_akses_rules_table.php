<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Menggunakan koneksi modul_kinerja agar konsisten
    protected $connection = 'modul_kinerja'; 

    public function up()
    {
        Schema::connection('modul_kinerja')->create('kinerja_akses_rules', function (Blueprint $table) {
            $table->id();
    $table->integer('opd_id')->nullable(); // Target Instansi
    $table->unsignedBigInteger('user_id')->nullable(); // Target Orang (Bisa Kosong)
    $table->unsignedBigInteger('parent_id_allowed'); // Batas Akar Pohon
    $table->string('jenis_kinerja_allowed'); // Level Izin (Program/Kegiatan)

    // SKEMA WAKTU (Batas Waktu Pengisian)
    $table->dateTime('start_date')->nullable(); // Mulai dibuka
    $table->dateTime('end_date')->nullable();   // Otomatis terkunci setelah ini

    $table->enum('status_akses', ['open', 'locked'])->default('open'); 
    $table->timestamps();

            // Foreign key ke tabel utama pohon_kinerja
            $table->foreign('parent_id_allowed')
                  ->references('id')->on('pohon_kinerja')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::connection('modul_kinerja')->dropIfExists('kinerja_akses_rules');
    }
};