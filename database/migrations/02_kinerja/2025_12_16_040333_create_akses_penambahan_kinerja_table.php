<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::connection('modul_kinerja')->create('akses_penambahan_kinerja', function (Blueprint $table) {
            $table->id();
            
            // Siapa yang diberi izin? (Dinas mana)
            $table->unsignedBigInteger('opd_id'); 
            $table->string('role_target')->default('opd'); // Default ke akun dinas
            
            // Izin nempel ke Node mana? (Foreign Key ke Pohon Kinerja)
            $table->unsignedBigInteger('parent_id_allowed'); 
            
            // Dia boleh nambahin apa? (Program/Kegiatan/Sub Kegiatan)
            $table->string('jenis_kinerja_allowed'); 
            
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by'); // Biasanya ID Admin Bappeda/Sekretariat
            $table->timestamps();

            // --- PERBAIKAN: FOREIGN KEY DIAKTIFKAN ---
            // Ini penting agar relasinya kuat ke tabel pohon_kinerja yang baru
            $table->foreign('parent_id_allowed')
                  ->references('id')->on('pohon_kinerja')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::connection('modul_kinerja')->dropIfExists('akses_penambahan_kinerja');
    }
};