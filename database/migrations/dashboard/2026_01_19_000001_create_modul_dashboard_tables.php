<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Koneksi ke-6 khusus untuk Dashboard Eksekutif
    protected $connection = 'modul_dashboard';

    public function up()
    {
        
Schema::connection('modul_dashboard')->create('executive_summaries', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('sub_activity_id')->unique();
        $table->text('visi_text')->nullable();
        $table->text('misi_text')->nullable();
        $table->text('nama_program')->nullable();
        $table->text('nama_kegiatan')->nullable();
        $table->text('nama_sub_kegiatan');
        $table->string('indikator_kinerja')->nullable();
        $table->string('status_kinerja')->default('draft');
        $table->string('klasifikasi', 10)->nullable();

        $table->decimal('pagu_rka', 15, 2)->default(0);
        $table->string('sumber_dana')->nullable();
        $table->boolean('has_kak')->default(false);
        $table->string('metode_kak')->nullable();

        $table->string('nama_paket')->nullable();
        $table->string('status_pengadaan')->default('belum');
        $table->integer('progres_dokumen')->default(0);
        $table->string('nama_vendor')->nullable();
        
        // TAMBAHKAN KOLOM INI UNTUK DETEKSI HAMBATAN
        $table->boolean('is_bottleneck')->default(false); 
        
        $table->decimal('nilai_hps', 15, 2)->default(0);
        $table->decimal('nilai_kontrak_final', 15, 2)->default(0);
        $table->timestamps();
    });

Schema::connection('modul_dashboard')->create('executive_directives', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('sub_activity_id');
    $table->text('instruction');
    $table->string('target_wa')->nullable(); // Opsional jika ingin kirim ke individu
    $table->timestamps();
});

Schema::connection('modul_dashboard')->table('executive_directives', function (Blueprint $table) {
    $table->enum('status', ['sent', 'resolved'])->default('sent'); // 'sent' = terkirim via WA
});

        Schema::connection($this->connection)->create('executive_notes', function (Blueprint $table) {
            $table->id();
            
            // Link ke modul_kinerja.sub_activities (Tanpa constrained karena lintas DB)
            $table->unsignedBigInteger('sub_activity_id')->index(); 
            
            // Detail Instruksi
            $table->text('catatan_instruksi');
            $table->enum('prioritas', ['Normal', 'Tinggi', 'Urgent'])->default('Normal');
            
            // Tracking respon dari pelaksana
            $table->enum('status_tindak_lanjut', ['Belum', 'Proses', 'Selesai'])->default('Belum');
            $table->text('respon_pelaksana')->nullable();
            
            $table->timestamps();
        });

        // 2. TABEL MONITORING TARGET KHUSUS (PINNED IKU)
        // Pimpinan bisa memilih IKU mana saja yang ingin muncul di halaman utama dashboard
        Schema::connection($this->connection)->create('featured_indicators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sub_activity_id')->unique(); // Referensi IKU/Sub-Kegiatan
            $table->integer('urutan_tampilan')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });

        // 3. TABEL SNAPSHOT PROGRESS (Untuk Histori)
        // Menyimpan data historis bulanan agar pimpinan bisa melihat tren kenaikan progress
        Schema::connection($this->connection)->create('monthly_progress_logs', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->integer('bulan');
            $table->decimal('total_pagu_anggaran', 20, 2);
            $table->decimal('total_realisasi_kontrak', 20, 2);
            $table->integer('jumlah_kak_selesai');
            $table->integer('jumlah_kontrak_doc10');
            $table->timestamps();
        });
    }

public function down()
{
    Schema::connection('modul_dashboard')->dropIfExists('executive_summaries');
    Schema::connection('modul_dashboard')->dropIfExists('executive_directives');
    Schema::connection('modul_dashboard')->dropIfExists('executive_notes');
    Schema::connection('modul_dashboard')->dropIfExists('featured_indicators');
    Schema::connection('modul_dashboard')->dropIfExists('monthly_progress_logs');
}
};