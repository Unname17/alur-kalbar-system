<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'modul_kinerja'; 

    public function up()
    {
        // 1. TABEL UTAMA: POHON KINERJA (Adjacency List)
        Schema::connection('modul_kinerja')->create('pohon_kinerja', function (Blueprint $table) {
            $table->id();
            
            // Hirarki (Parent ID)
            $table->unsignedBigInteger('parent_id')->nullable(); 
            
            $table->unsignedBigInteger('opd_id')->nullable();    
            $table->string('nama_kinerja'); 
            
            // Jenis level kinerja
            $table->enum('jenis_kinerja', ['visi', 'misi', 'sasaran_daerah', 'sasaran_opd', 'program', 'kegiatan', 'sub_kegiatan']);
            
            // Status Workflow
            $table->enum('status', ['draft', 'pengajuan', 'disetujui', 'ditolak'])->default('draft');
            $table->text('catatan_penolakan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            
            // Data Khusus Sub Kegiatan (Anggaran & PJ)
            $table->decimal('anggaran', 15, 2)->default(0)->nullable(); 
            $table->string('penanggung_jawab')->nullable(); 
            
            $table->timestamps();

            // Self-Join Constraint
            $table->foreign('parent_id')->references('id')->on('pohon_kinerja')->onDelete('cascade');
        });

        // 2. TABEL INDIKATOR (One-to-Many: 1 Kinerja Punya BANYAK Indikator)
        // Inilah tabel yang dicari oleh Seeder tadi!
        Schema::connection('modul_kinerja')->create('indikator_kinerja', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pohon_kinerja_id'); // Foreign Key ke Pohon
            
            $table->text('indikator');   // Nama Indikator
            $table->string('target');    // Target
            $table->string('satuan');    // Satuan
            
            $table->timestamps();

            // Relasi Foreign Key
            $table->foreign('pohon_kinerja_id')
                  ->references('id')->on('pohon_kinerja')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::connection('modul_kinerja')->dropIfExists('indikator_kinerja');
        Schema::connection('modul_kinerja')->dropIfExists('pohon_kinerja');
    }
};