<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'modul_kak'; 

    public function up()
    {
        Schema::connection($this->connection)->create('kak_mains', function (Blueprint $table) {
            $table->id();

            // Link ke Modul Anggaran & Kinerja
            $table->unsignedBigInteger('rka_main_id')->index(); 
            $table->unsignedBigInteger('sub_activity_id')->index(); 

            // A. LATAR BELAKANG
            $table->longText('latar_belakang')->nullable();

            // DATA LIST (JSON Array)
            $table->json('dasar_hukum')->nullable(); 

            // B. PENERIMA MANFAAT
            $table->text('penerima_manfaat')->nullable();

            // C. MAKSUD DAN TUJUAN
            // FIX: Hapus ->change() karena ini create table
            $table->longText('maksud')->nullable(); 
            $table->json('tujuan')->nullable(); 

            // E. METODE
            $table->string('metode_pelaksanaan')->nullable();

            // F. TAHAPAN & JADWAL (Digabung dalam JSON yang lebih kompleks)
            $table->json('tahapan_pelaksanaan')->nullable(); 

            // G. TEMPAT
            $table->string('tempat_pelaksanaan')->nullable();

            // H. JADWAL (Opsional, jika ingin terpisah, tapi kita akan gabung di tahapan)
            $table->json('jadwal_matriks')->nullable(); 
            
            // J. PENANDATANGAN (Snapshot)
            $table->string('nama_pa_kpa')->nullable();
            $table->string('nip_pa_kpa')->nullable();
            $table->string('jabatan_pa_kpa')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::connection($this->connection)->dropIfExists('kak_mains');
    }
};