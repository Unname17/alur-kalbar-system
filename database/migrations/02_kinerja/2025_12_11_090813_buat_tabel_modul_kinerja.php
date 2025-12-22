<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'modul_kinerja'; 

    public function up()
    {
        // 1. TABEL UTAMA: POHON KINERJA
        Schema::connection('modul_kinerja')->create('pohon_kinerja', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable(); 
            $table->unsignedBigInteger('opd_id')->nullable();    
            $table->string('nama_kinerja'); 
            
            // Level Kinerja Lengkap 
            $table->enum('jenis_kinerja', [
                'visi', 'misi', 'sasaran_daerah', 'sasaran_opd', 
                'program', 'kegiatan', 'sub_kegiatan', 'skp', 'rencana_aksi'
            ]);
            
            // Status Workflow
            $table->enum('status', ['draft', 'pengajuan', 'disetujui', 'ditolak'])->default('draft');
            $table->text('catatan_penolakan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            
            // Data Anggaran & Penanggung Jawab
            $table->decimal('anggaran', 15, 2)->default(0)->nullable(); 
            $table->string('penanggung_jawab')->nullable(); 

            // Fitur Tracking 5 Tahun sesuai arahan mentor [cite: 10]
            $table->decimal('target_t1', 15, 2)->default(0);
            $table->decimal('target_t2', 15, 2)->default(0);
            $table->decimal('target_t3', 15, 2)->default(0);
            $table->decimal('target_t4', 15, 2)->default(0);
            $table->decimal('target_t5', 15, 2)->default(0);
            
            $table->timestamps();
            $table->foreign('parent_id')->references('id')->on('pohon_kinerja')->onDelete('cascade');
        });

        // 2. TABEL INDIKATOR (PENTING: Jangan sampai tertinggal!)
        Schema::connection('modul_kinerja')->create('indikator_kinerja', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pohon_kinerja_id');
            $table->text('indikator');
            $table->string('target'); 
            $table->string('satuan');
            $table->timestamps();

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