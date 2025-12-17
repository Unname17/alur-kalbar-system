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
            $table->unsignedBigInteger('ssh_id')->nullable(); // Tetap nullable untuk item manual
            
            $table->string('nama_barang');
            $table->double('volume');
            $table->string('satuan');
            $table->double('harga_satuan');
            $table->double('total_harga');
            $table->text('keterangan')->nullable();

            // --- Kolom Tambahan untuk Fitur Usulan Manual ---
            $table->boolean('is_manual')->default(false); // True jika user input sendiri
            $table->integer('is_verified')->default(1);   // 0: Ditolak, 1: Pending (Usulan), 2: Terverifikasi (Admin)
            
            $table->timestamps();

            // Index untuk performa pencarian per KAK
            $table->index('kak_id');
        });
    }

    public function down()
    {
        Schema::connection($this->connection)->dropIfExists('kak_details');
    }
};