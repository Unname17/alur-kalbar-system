<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // KONEKSI DATABASE BARU
    protected $connection = 'modul_anggaran';

    public function up()
    {
        // 1. TABEL HASIL SPK
        Schema::connection($this->connection)->create('spk_rankings', function (Blueprint $table) {
            $table->id();
            
            // LINTAS DATABASE: Jangan pakai constrained()!
            // Cukup index saja biar cepat saat di-join manual
            $table->unsignedBigInteger('sub_activity_id')->index(); 
            
            $table->float('nilai_gap')->default(0);
            $table->float('nilai_klasifikasi')->default(0);
            $table->float('total_skor')->default(0);
            $table->integer('ranking')->default(0);
            $table->boolean('is_priority')->default(false);
            $table->timestamps();
        });

        // 2. MASTER REKENING BELANJA (SSH)
        Schema::connection($this->connection)->create('master_rekenings', function (Blueprint $table) {
            $table->id();
            $table->string('kode_rekening', 50)->unique();
            $table->string('nama_rekening');
            $table->timestamps();
        });

        // 3. HEADER RKA
        Schema::connection($this->connection)->create('rka_mains', function (Blueprint $table) {
            $table->id();
            
            // LINTAS DATABASE: Referensi ke modul_kinerja.sub_activities
            $table->unsignedBigInteger('sub_activity_id')->index();
            
            $table->string('sumber_dana')->default('PENDAPATAN ASLI DAERAH (PAD)'); 
            $table->text('lokasi_kegiatan')->nullable();
            $table->string('waktu_pelaksanaan')->nullable();
            $table->string('kelompok_sasaran')->nullable();
            // --- TAMBAHAN UNTUK STEP 3 ---
    $table->string('sub_unit_organisasi')->nullable(); // Menyesuaikan tampilan PDF
    $table->string('jenis_layanan')->nullable();       // Fitur Step 3
    $table->text('spm')->nullable();                 // Fitur Step 3
    $table->json('tim_anggaran')->nullable();         // Simpan data dinamis TAPD
    // -----------------------------
            
            $table->string('nip_pptk', 20)->nullable();
            $table->string('nama_pptk')->nullable();

            $table->decimal('pagu_indikatif', 15, 2)->default(0);
            $table->decimal('total_anggaran', 15, 2)->default(0);
            
            $table->enum('status', ['draft', 'diajukan', 'diterima', 'ditolak'])->default('draft');
            $table->timestamps();
        });

        // 4. RINCIAN BELANJA RKA
        // Kalau ini relasinya MASIH DALAM SATU DB (modul_anggaran), jadi boleh pakai constrained()
        Schema::connection($this->connection)->create('rka_details', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel rka_mains di database yang sama (AMAN)
            $table->foreignId('rka_main_id')
                  ->constrained('rka_mains')
                  ->onDelete('cascade');
            
            // Relasi ke master_rekenings di database yang sama (AMAN)
            $table->foreignId('rekening_id')->constrained('master_rekenings'); 
            
            $table->text('uraian_belanja');
            $table->text('spesifikasi')->nullable();
            $table->decimal('koefisien', 10, 2)->default(1);
            $table->string('satuan', 50);
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('ppn_persen', 5, 2)->default(0);
            $table->decimal('sub_total', 15, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::connection($this->connection)->dropIfExists('rka_details');
        Schema::connection($this->connection)->dropIfExists('rka_mains');
        Schema::connection($this->connection)->dropIfExists('master_rekenings');
        Schema::connection($this->connection)->dropIfExists('spk_rankings');
    }
};