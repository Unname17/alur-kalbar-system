<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'modul_anggaran';

    public function up()
    {
        Schema::connection($this->connection)->create('kak_details', function (Blueprint $table) {
$table->id();
        $table->unsignedBigInteger('kak_id'); 
        
        // TAMBAHKAN KOLOM INI: Sebagai penghubung ke rka_main
        $table->unsignedBigInteger('rka_id')->nullable(); 
        
        $table->unsignedBigInteger('ssh_id')->nullable();
        $table->string('nama_barang');
        $table->double('volume');
        $table->string('satuan');
        $table->double('harga_satuan');
        $table->double('total_harga');
        $table->text('keterangan')->nullable();
        $table->boolean('is_manual')->default(false);
        $table->integer('is_verified')->default(1);
        $table->string('kategori');
        $table->timestamps();

        $table->index('kak_id');
        // Tambahkan index untuk rka_id agar pencarian cepat
        $table->index('rka_id');
        });
    }

    public function down()
    {
        Schema::connection($this->connection)->dropIfExists('kak_details');
    }
};