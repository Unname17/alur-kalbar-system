<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // WAJIB: Arahkan ke koneksi modul_anggaran sesuai config database Anda
    protected $connection = 'modul_anggaran';

    public function up()
    {
        Schema::connection($this->connection)->create('master_ssh', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique();
            $table->string('nama_barang');
            $table->string('satuan');
            $table->double('harga_satuan');
            $table->string('spesifikasi')->nullable();
            $table->string('kategori'); // SSH, SBU, HSPK
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::connection($this->connection)->dropIfExists('master_ssh');
    }
};