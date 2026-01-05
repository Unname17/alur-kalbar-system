<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $connection = 'modul_kinerja';

    public function up() {
        // --- LEVEL 1: KEBIJAKAN PROVINSI ---
        Schema::connection($this->connection)->create('visions', function (Blueprint $table) {
            $table->id();
            $table->text('visi_text'); 
            $table->year('tahun_awal'); $table->year('tahun_akhir');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::connection($this->connection)->create('missions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vision_id')->constrained('visions')->onDelete('cascade');
            $table->integer('nomor_misi');
            $table->text('misi_text'); 
            $table->timestamps();
            $table->softDeletes();
        });

        // LEVEL 2: TUJUAN PD
        Schema::connection($this->connection)->create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_id')->constrained('missions')->onDelete('cascade');
            $table->unsignedBigInteger('pd_id');
            $table->text('nama_tujuan');
            $table->text('indikator')->nullable();
            $table->string('satuan')->nullable();
            $table->string('baseline_2024')->nullable(); // TETAP baseline_2024
            $this->addTargetColumns($table);
            $this->addValidationColumns($table); 
            $table->timestamps();
            $table->softDeletes();
        });

        // LEVEL 3: SASARAN STRATEGIS
        Schema::connection($this->connection)->create('sasaran_strategis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goal_id')->constrained('goals')->onDelete('cascade');
            $table->text('nama_sasaran');
            $table->text('indikator_sasaran')->nullable();
            $table->string('satuan')->nullable();
            $table->string('baseline_2024')->nullable(); // TETAP baseline_2024
            $this->addTargetColumns($table);
            $this->addValidationColumns($table);
            $table->timestamps();
            $table->softDeletes();
        });

        // LEVEL 4: PROGRAM
        Schema::connection($this->connection)->create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sasaran_id')->constrained('sasaran_strategis')->onDelete('cascade');
            $table->text('nama_program'); 
            $table->text('indikator_program')->nullable();
            $table->string('satuan')->nullable();
            $table->string('baseline_2024')->nullable(); // TETAP baseline_2024
            $this->addTargetColumns($table);
            $this->addValidationColumns($table);
            $table->timestamps();
            $table->softDeletes();
        });

        // LEVEL 5: KEGIATAN (PARENT RKA)
        Schema::connection($this->connection)->create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->onDelete('cascade');
            $table->string('kode_kegiatan')->nullable();
            $table->text('nama_kegiatan');
            
            // PERBAIKAN DI SINI:
            // Nama kolom diubah ke 'pagu_anggaran' agar Sesuai Controller Pagu.
            // Gunakan bigInteger karena ini nominal uang.
            $table->bigInteger('pagu_anggaran')->default(0); 
            
            $table->text('indikator_kegiatan')->nullable();
            $table->string('satuan')->nullable();
            $table->string('baseline_2024')->nullable(); // TETAP baseline_2024
            $this->addTargetColumns($table);
            $this->addValidationColumns($table);
            $table->timestamps();
            $table->softDeletes();
        });

        // LEVEL 6: SUB-KEGIATAN
        Schema::connection($this->connection)->create('sub_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->string('kode_sub')->nullable();
            $table->text('nama_sub');
            $table->text('indikator_sub')->nullable();
            $table->string('satuan')->nullable(); 
            $table->string('baseline_2024')->nullable(); // TETAP baseline_2024
            $this->addTargetColumns($table);
            $table->enum('tipe_perhitungan', ['Akumulasi', 'Non-Akumulasi'])->default('Non-Akumulasi'); 
            $table->enum('klasifikasi', ['IKD', 'IKU', 'IKK'])->default('IKK');
            $this->addValidationColumns($table); 
            $table->string('created_by_nip', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // --- TABEL PENDUKUNG ---
        Schema::connection($this->connection)->create('pengaturan_akses_modul', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pd_id')->nullable(); 
            $table->string('user_nip', 20)->nullable();      
            $table->unsignedBigInteger('parent_id')->nullable(); 
            $table->string('level_izin')->nullable();        
            $table->timestamp('waktu_buka')->nullable();
            $table->timestamp('waktu_tutup')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->text('pesan_blokir')->nullable();
            $table->string('updated_by_nip', 20)->nullable();
            $table->timestamps();
        });

        Schema::connection($this->connection)->create('log_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->string('user_nip', 20)->nullable(); 
            $table->string('user_nama')->nullable();    
            $table->unsignedBigInteger('pd_id')->nullable(); 
            $table->string('aksi');  
            $table->string('modul'); 
            $table->text('deskripsi'); 
            $table->string('subject_type')->nullable(); 
            $table->unsignedBigInteger('subject_id')->nullable(); 
            $table->json('old_values')->nullable(); 
            $table->json('new_values')->nullable(); 
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    private function addTargetColumns(Blueprint $table) {
        $table->string('target_2025')->nullable();
        $table->string('target_2026')->nullable();
        $table->string('target_2027')->nullable();
        $table->string('target_2028')->nullable();
        $table->string('target_2029')->nullable();
        $table->string('target_2030')->nullable();
    }

    private function addValidationColumns(Blueprint $table) {
        $table->enum('status', ['draft', 'pending', 'verified', 'validated', 'approved', 'rejected'])
              ->default('draft');
        $table->text('catatan_revisi')->nullable();
        $table->string('nip_verifier', 20)->nullable(); 
        $table->string('nip_validator', 20)->nullable(); 
        $table->string('nip_approver', 20)->nullable();  
    }

    public function down() {
        Schema::connection($this->connection)->dropIfExists('log_aktivitas'); 
        Schema::connection($this->connection)->dropIfExists('pengaturan_akses_modul');
        Schema::connection($this->connection)->dropIfExists('sub_activities');
        Schema::connection($this->connection)->dropIfExists('activities');
        Schema::connection($this->connection)->dropIfExists('programs');
        Schema::connection($this->connection)->dropIfExists('sasaran_strategis');
        Schema::connection($this->connection)->dropIfExists('goals');
        Schema::connection($this->connection)->dropIfExists('missions');
        Schema::connection($this->connection)->dropIfExists('visions');
    }
};