<?php
// File ini akan membuat semua tabel dengan struktur kolom yang sudah final (termasuk status, created_by, dan catatan_penolakan)

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Opsional, tapi baik untuk kejelasan
    protected $connection = 'modul_kinerja'; 

    public function up()
    {
        // 1. TABEL UTAMA: POHON KINERJA (dengan semua kolom approval)
        Schema::connection('modul_kinerja')->create('pohon_kinerja', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable(); 
            $table->unsignedBigInteger('opd_id')->nullable();    
            $table->string('nama_kinerja');
            $table->enum('jenis_kinerja', ['visi', 'misi', 'sasaran_daerah', 'sasaran_opd', 'program', 'kegiatan', 'sub_kegiatan']);
            
            // KOLOM FINAL UNTUK APPROVAL
            $table->enum('status', ['draft', 'pengajuan', 'disetujui', 'ditolak'])->default('draft');
            $table->text('catatan_penolakan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable(); // ID user yang mengajukan
            
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('pohon_kinerja')->onDelete('cascade');
        });

        // 2. DETAIL PROGRAM
        Schema::connection('modul_kinerja')->create('detail_program', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pohon_id');
            $table->text('sasaran_program')->nullable();
            $table->text('indikator_program');
            $table->string('target_program');
            $table->string('satuan_target');
            
            $table->timestamps();
            
            $table->foreign('pohon_id')->references('id')->on('pohon_kinerja')->onDelete('cascade');
        });

        // 3. DETAIL KEGIATAN
        Schema::connection('modul_kinerja')->create('detail_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pohon_id');
            $table->text('indikator_kegiatan');
            $table->string('target_kegiatan');
            $table->string('satuan_target');
            
            $table->timestamps();
            
            $table->foreign('pohon_id')->references('id')->on('pohon_kinerja')->onDelete('cascade');
        });

        // 4. DETAIL SUB KEGIATAN
        Schema::connection('modul_kinerja')->create('detail_sub_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pohon_id');
            $table->text('indikator_sub_kegiatan');
            $table->string('target_sub_kegiatan');
            $table->string('satuan_target');
            $table->decimal('anggaran', 15, 2)->default(0); 
            $table->string('penanggung_jawab'); 
            
            $table->timestamps();
            
            $table->foreign('pohon_id')->references('id')->on('pohon_kinerja')->onDelete('cascade');
        });
    }

    public function down()
    {
        // Urutan drop harus dari tabel anak ke tabel induk
        Schema::connection('modul_kinerja')->dropIfExists('detail_sub_kegiatan');
        Schema::connection('modul_kinerja')->dropIfExists('detail_kegiatan');
        Schema::connection('modul_kinerja')->dropIfExists('detail_program');
        Schema::connection('modul_kinerja')->dropIfExists('pohon_kinerja');
    }
};