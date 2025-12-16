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
        // Ini menyimpan struktur hirarki (Adjacency List)
        Schema::connection('modul_kinerja')->create('pohon_kinerja', function (Blueprint $table) {
            $table->id();
            
            // PARENT_ID: Kunci Hirarki (Visi -> Misi -> Program -> Kegiatan)
            $table->unsignedBigInteger('parent_id')->nullable(); 
            
            $table->unsignedBigInteger('opd_id')->nullable();    
            $table->string('nama_kinerja'); // Nama Nomenklatur
            
            // Jenis level kinerja
            $table->enum('jenis_kinerja', ['visi', 'misi', 'sasaran_daerah', 'sasaran_opd', 'program', 'kegiatan', 'sub_kegiatan']);
            
            // Kolom Status (Workflow)
            $table->enum('status', ['draft', 'pengajuan', 'disetujui', 'ditolak'])->default('draft');
            $table->text('catatan_penolakan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            
            // DATA KHUSUS (Hanya terisi jika jenisnya sub_kegiatan, null jika visi/misi)
            // Ini lebih efisien daripada buat tabel terpisah hanya untuk 2 kolom ini
            $table->decimal('anggaran', 15, 2)->default(0)->nullable(); 
            $table->string('penanggung_jawab')->nullable(); 
            
            $table->timestamps();

            // Self-Join Constraint (Penting untuk Adjacency List)
            $table->foreign('parent_id')->references('id')->on('pohon_kinerja')->onDelete('cascade');
        });

        // 2. TABEL INDIKATOR (One-to-Many Relationship)
        // Satu Pohon Kinerja BISA PUNYA BANYAK Indikator
        Schema::connection('modul_kinerja')->create('indikator_kinerja', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pohon_kinerja_id'); // Foreign Key ke tabel atas
            
            $table->text('indikator');   // Contoh: "Jumlah Dokumen Laporan"
            $table->string('target');    // Contoh: "5"
            $table->string('satuan');    // Contoh: "Dokumen"
            
            $table->timestamps();

            // Relasi: Jika Pohon dihapus, Indikatornya hilang otomatis
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