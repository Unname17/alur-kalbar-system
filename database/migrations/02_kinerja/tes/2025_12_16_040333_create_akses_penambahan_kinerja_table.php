<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::connection('modul_kinerja')->create('history_kinerja', function (Blueprint $table) {
$table->id();
        $table->unsignedBigInteger('pohon_kinerja_id');
        $table->json('data_lama'); // Menyimpan snapshot data sebelum diubah
        $table->json('data_baru'); // Menyimpan snapshot data setelah diubah
        $table->unsignedBigInteger('user_id'); // Siapa yang mengubah
        $table->timestamps();

        $table->foreign('pohon_kinerja_id')->references('id')->on('pohon_kinerja')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::connection('modul_kinerja')->dropIfExists('history_kinerja');
    }
};