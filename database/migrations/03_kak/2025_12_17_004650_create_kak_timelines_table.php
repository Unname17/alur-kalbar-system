<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'modul_kak';

    public function up()
    {
        Schema::connection('modul_kak')->create('kak_timelines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kak_id');
            $table->string('nama_tahapan'); // Contoh: Persiapan, Pelaksanaan, Pelaporan
            
            // Loop untuk membuat kolom b1 sampai b12
            for ($i = 1; $i <= 12; $i++) {
                $table->boolean('b' . $i)->default(false);
            }

            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Relasi ke tabel KAK
            $table->foreign('kak_id')->references('id')->on('kak')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::connection('modul_kak')->dropIfExists('kak_timelines');
    }
};