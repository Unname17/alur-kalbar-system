<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'modul_kak';

    public function up()
    {
        Schema::connection('modul_kak')->create('kak_tim_pelaksana', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kak_id');
            $table->string('nama_personil');
            $table->string('peran_dalam_tim');
            $table->string('nip')->nullable();
            $table->timestamps();

            $table->foreign('kak_id')->references('id')->on('kak')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::connection('modul_kak')->dropIfExists('kak_tim_pelaksana');
    }
};