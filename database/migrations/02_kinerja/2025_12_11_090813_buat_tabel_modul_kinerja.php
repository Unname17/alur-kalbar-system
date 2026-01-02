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

        // --- LEVEL 2 s/d 6 (Ditambahkan Status & Tracking) ---
        
        // LEVEL 2: TUJUAN PD
        Schema::connection($this->connection)->create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_id')->constrained('missions')->onDelete('cascade');
            $table->unsignedBigInteger('pd_id');
            $table->text('nama_tujuan');
            $table->text('indikator')->nullable();
            $table->string('satuan')->nullable();
            $table->string('baseline_2024')->nullable();
            $this->addTargetColumns($table);
            $this->addValidationColumns($table); // Kolom Status & Revisi
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
            $table->string('baseline_2024')->nullable();
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
            $table->string('baseline_2024')->nullable();
            $this->addTargetColumns($table);
            $this->addValidationColumns($table);
            $table->timestamps();
            $table->softDeletes();
        });

        // LEVEL 5: KEGIATAN
        Schema::connection($this->connection)->create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->onDelete('cascade');
            $table->text('nama_kegiatan');
            $table->text('indikator_kegiatan')->nullable();
            $table->string('satuan')->nullable();
            $table->string('baseline_2024')->nullable();
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
            $table->string('baseline_2024')->nullable();
            $this->addTargetColumns($table);
            $table->enum('tipe_perhitungan', ['Akumulasi', 'Non-Akumulasi'])->default('Non-Akumulasi'); 
            $table->enum('klasifikasi', ['IKD', 'IKU', 'IKK'])->default('IKK');
            
            $this->addValidationColumns($table); // Validasi Berjenjang
            
            $table->string('created_by_nip', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // --- TABEL PENDUKUNG ---
        Schema::connection($this->connection)->create('pengaturan_akses_modul', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pd_id')->nullable(); // Global atau spesifik OPD
            $table->string('user_nip', 20)->nullable();      // Spesifik Pegawai
            $table->unsignedBigInteger('parent_id')->nullable(); // Akar Pohon (Parent)
            $table->string('level_izin')->nullable();        // Program, Kegiatan, dll
            $table->timestamp('waktu_buka')->nullable();
            $table->timestamp('waktu_tutup')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->text('pesan_blokir')->nullable();
            $table->string('updated_by_nip', 20);
            $table->timestamps();
        });

        // [UPDATED] TABEL LOG AKTIVITAS (Audit Trail)
        // Disesuaikan agar bisa mencatat 5W+1H dan sinkron dengan Helper LogKinerja
        Schema::connection($this->connection)->create('log_aktivitas', function (Blueprint $table) {
            $table->id();
            
            // WHO (Siapa)
            $table->string('user_nip', 20)->nullable(); // Nullable jika sistem otomatis
            $table->string('user_nama')->nullable();    // Snapshot nama saat kejadian
            $table->unsignedBigInteger('pd_id')->nullable(); // Agar bisa difilter per OPD
            
            // WHAT & WHERE (Apa & Dimana)
            $table->string('aksi');  // CREATE, UPDATE, DELETE, ACCESS, LOGIN
            $table->string('modul'); // Wizard, Pohon, Akses, Dashboard
            $table->text('deskripsi'); // Penjelasan (Contoh: "Menambahkan Kegiatan: Sosialisasi...")
            
            // DETAIL TEKNIS (Objek yang diubah)
            $table->string('subject_type')->nullable(); // Model Class (Ex: App\Models\Kinerja\Goal)
            $table->unsignedBigInteger('subject_id')->nullable(); // ID Data
            
            // DATA CHANGES (Opsional untuk History Data)
            $table->json('old_values')->nullable(); // Data sebelum edit
            $table->json('new_values')->nullable(); // Data sesudah edit
            
            // HOW & WHEN (Jejak Digital)
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Helper untuk kolom target tahunan.
     */
    private function addTargetColumns(Blueprint $table) {
        $table->string('target_2025')->nullable();
        $table->string('target_2026')->nullable();
        $table->string('target_2027')->nullable();
        $table->string('target_2028')->nullable();
        $table->string('target_2029')->nullable();
        $table->string('target_2030')->nullable();
    }

    /**
     * Helper Baru: Kolom Validasi & Status Workflow
     */
    private function addValidationColumns(Blueprint $table) {
        // Status Workflow
        $table->enum('status', ['draft', 'pending', 'verified', 'validated', 'approved', 'rejected'])
              ->default('draft');
        
        // Catatan jika ditolak (revisi)
        $table->text('catatan_revisi')->nullable();
        
        // Tracking Siapa yang memproses
        $table->string('nip_verifier', 20)->nullable(); // Kabid
        $table->string('nip_validator', 20)->nullable(); // Kadis
        $table->string('nip_approver', 20)->nullable();  // Bappeda
    }

    public function down() {
        Schema::connection($this->connection)->dropIfExists('log_aktivitas'); // Update nama drop
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