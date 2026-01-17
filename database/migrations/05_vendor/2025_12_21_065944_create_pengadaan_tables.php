<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'modul_pengadaan';

    public function up()
    {
        // 1. TABEL MASTER KBKI
        Schema::connection($this->connection)->create('kbki_masters', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kbki')->unique();
            $table->text('deskripsi_kbki');
            $table->timestamps();
        });

        // 2. TABEL MASTER VENDOR
        Schema::connection($this->connection)->create('procurement_vendors', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan');
            $table->string('bentuk_usaha')->nullable(); 
            $table->string('npwp')->nullable();
            $table->text('alamat')->nullable();
            $table->string('email')->nullable();
            $table->string('no_telepon')->nullable();
            $table->string('nama_direktur')->nullable();
            $table->string('jabatan_direktur')->default('Direktur');
            $table->string('nama_bank')->nullable();
            $table->string('no_rekening')->nullable();
            $table->string('nama_pemilik_rekening')->nullable();
            $table->timestamps();
        });

        // 3. TABEL UTAMA PAKET (IDENTIFIKASI & RINGKASAN DOC 6)
        Schema::connection($this->connection)->create('procurement_packages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rka_main_id')->nullable()->index(); 
            $table->string('nama_paket'); 
            $table->string('status_tahapan')->default('identifikasi'); 
            $table->integer('perubahan_ke')->default(0);
            $table->date('tanggal_perubahan')->nullable();
            $table->decimal('pagu_paket', 15, 2)->default(0);
            $table->text('pertimbangan_akun')->nullable(); 
            $table->integer('opsi_pdn')->nullable();
            $table->text('alasan_pdn')->nullable();
            $table->string('jenis_pengadaan');
            $table->text('alasan_pemilihan_jenis')->nullable(); 
            $table->string('metode_pemilihan');
            $table->text('alasan_metode_pemilihan')->nullable(); 
            $table->string('kode_kbki')->nullable();
            $table->string('deskripsi_kbki')->nullable();
            $table->boolean('is_pdn')->default(true);
            $table->boolean('is_umkm')->default(true);
            $table->string('lokasi_pekerjaan')->nullable(); 
            $table->string('jadwal_pelaksanaan')->nullable(); 
            $table->text('uraian_pekerjaan')->nullable();
            $table->string('file_sbu')->nullable();
            $table->string('file_kontrak_lama')->nullable();
            $table->text('kesimpulan_ppk')->nullable();
            $table->date('tanggal_penyusunan')->nullable();
            $table->string('nama_pa_kpa')->nullable();
            $table->string('nip_pa_kpa')->nullable();
            $table->string('nama_tenaga_ahli')->nullable();
            $table->decimal('hps_total', 15, 2)->nullable();
            $table->decimal('nilai_kontrak', 15, 2)->nullable();

            // Field Justifikasi Harga (Doc 6)
            $table->text('justifikasi_harga_pasar')->nullable(); 
            $table->text('kesimpulan_analisis_harga')->nullable(); 
            $table->decimal('hps_hitung_rata_rata', 15, 2)->nullable(); 
            $table->decimal('hps_hitung_median', 15, 2)->nullable(); 
            $table->decimal('hps_terendah', 15, 2)->nullable();
            $table->decimal('hps_tertinggi', 15, 2)->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. TABEL ITEM PENGADAAN (SPEK DOC 4 & 5)
        Schema::connection($this->connection)->create('procurement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('procurement_packages')->onDelete('cascade');
            $table->unsignedBigInteger('rka_detail_id')->index(); 
            $table->string('nama_item'); 
            $table->string('merk_tipe')->nullable(); 
            $table->string('masa_garansi')->nullable(); 
            $table->text('standar_mutu')->nullable(); 
            $table->text('fungsi_kinerja')->nullable(); 
            $table->text('aspek_pemeliharaan')->nullable(); 
            $table->text('suku_cadang')->nullable();
            $table->text('deskripsi_spesifikasi')->nullable();
            $table->text('link_produk_katalog')->nullable();
            $table->decimal('volume', 15, 2);
            $table->string('satuan');
            $table->decimal('harga_satuan_rka', 15, 2);
            $table->decimal('harga_satuan_hps', 15, 2)->nullable();
            $table->decimal('total_hps', 15, 2)->nullable();
            $table->timestamps();
        });

        // 5. TABEL STRATEGI PERSIAPAN (DOC 2)
        Schema::connection($this->connection)->create('procurement_preparations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('procurement_packages')->onDelete('cascade');
            $table->text('alasan_metode')->nullable(); 
            $table->enum('kriteria_barang_jasa', ['Standar', 'Kompleks'])->default('Standar');
            $table->string('jalur_strategis')->nullable();
            $table->integer('jalur_prioritas')->nullable(); 
            
            // Checkbox Uji Pasar (Jangan dibuang)
            $table->boolean('uji_pasar_ideal')->default(false);
            $table->boolean('uji_non_kritikal')->default(false);
            $table->boolean('uji_nol_value_added')->default(false);
            $table->boolean('uji_spek_stabil')->default(false);
            $table->boolean('uji_pengalaman_identik')->default(false);

            $table->text('justifikasi_pilihan')->nullable();
            $table->json('target_strategis')->nullable(); 
            $table->date('tanggal_analisis')->nullable();
            $table->timestamps();
        });

        // 6. TABEL ANALISIS PERSIAPAN (DOC 3)
        Schema::connection($this->connection)->create('procurement_preparation_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('procurement_packages')->onDelete('cascade');
            $table->string('nama_calon_penyedia')->nullable();
            $table->string('produk_katalog')->nullable();
            $table->decimal('harga_tayang_katalog', 15, 2)->nullable();
            $table->text('link_produk_katalog')->nullable();
            
            // Evaluasi JSON (Sangat penting)
            $table->json('evaluasi_teknis')->nullable();   
            $table->json('evaluasi_harga')->nullable();    
            $table->json('evaluasi_kontrak')->nullable();  
            $table->json('evaluasi_katalog')->nullable();  

            $table->text('ulasan_kualitatif')->nullable(); 
            $table->string('reputasi_merek')->nullable(); 
            $table->text('spesifikasi_sumber')->nullable(); 
            $table->timestamps();
        });

        // 7. TABEL ANALISIS PASAR LAMA
        Schema::connection($this->connection)->create('procurement_market_analysis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('procurement_packages')->onDelete('cascade');
            $table->string('sumber_referensi'); 
            $table->string('link_url')->nullable();
            $table->decimal('harga_tayang', 15, 2);
            $table->date('tanggal_akses');
            $table->text('keterangan_komparasi')->nullable();
            $table->timestamps();
        });

        // 8. TABEL NEGOSIASI (DOC 9)
        Schema::connection($this->connection)->create('procurement_negotiations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('procurement_packages')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('procurement_vendors');
            $table->date('tanggal_negosiasi');
            $table->boolean('lulus_administrasi')->default(true);
            $table->boolean('lulus_teknis')->default(true);
            $table->boolean('lulus_kualifikasi')->default(true);
            $table->decimal('nominal_penawaran', 15, 2);
            $table->decimal('nominal_negosiasi', 15, 2);
            $table->enum('hasil_akhir', ['Sepakat', 'Gagal'])->default('Sepakat');
            $table->text('catatan_negosiasi')->nullable();
            $table->timestamps();
        });

        // 9. TABEL REFERENSI HARGA (DOC 6) [cite: 401-419]
        Schema::connection($this->connection)->create('procurement_price_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('procurement_packages')->onDelete('cascade');
            $table->enum('type', ['qualitative', 'market', 'sbu', 'contract']);
            $table->string('merek_model')->nullable();       
            $table->string('sumber_nama')->nullable();       
            $table->text('link_url')->nullable();            
            $table->decimal('harga_satuan', 15, 2)->default(0); 
            $table->string('file_bukti')->nullable();
            $table->text('kelebihan')->nullable();           
            $table->text('kekurangan')->nullable();          
            $table->string('garansi_layanan')->nullable();   
            $table->string('nomor_tanggal_dok')->nullable(); 
            $table->string('tahun_anggaran')->nullable();    
            $table->text('catatan_penyesuaian')->nullable(); 
            $table->text('catatan_relevansi')->nullable();   
            $table->date('tanggal_akses')->nullable();
            $table->timestamps();
        });

        // 10. TABEL KONTRAK / SURAT PESANAN (FIX SIAP DOC 10) 
        Schema::connection($this->connection)->create('procurement_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('procurement_packages')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('procurement_vendors');
            
            $table->string('nomor_sp');
            $table->date('tanggal_sp');
            $table->string('sumber_dana')->nullable(); // Tambahan Doc 10
            
            $table->integer('waktu_penyelesaian')->default(30); 
            $table->date('tanggal_mulai_kerja')->nullable();
            $table->date('tanggal_selesai_kerja')->nullable();
            $table->text('alamat_penyerahan')->nullable(); // Tambahan Doc 10 (Lokasi Pekerjaan)
            
            $table->enum('jenis_pembayaran', ['Sekaligus', 'Termin', 'Bulanan'])->default('Sekaligus');
            $table->decimal('nilai_kontrak_final', 15, 2);
            $table->decimal('nilai_jaminan_pelaksanaan', 15, 2)->nullable(); // Tambahan Doc 10
            $table->string('penerbit_jaminan')->nullable(); // Tambahan Doc 10

            $table->string('nama_pejabat_penandatangan')->nullable();
            $table->string('nip_pejabat_penandatangan')->nullable();
            $table->string('jabatan_pejabat')->nullable();
            
            $table->text('syarat_khusus_tambahan')->nullable(); // Tambahan Doc 10
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::connection($this->connection)->dropIfExists('procurement_contracts');
        Schema::connection($this->connection)->dropIfExists('procurement_price_references');
        Schema::connection($this->connection)->dropIfExists('procurement_negotiations');
        Schema::connection($this->connection)->dropIfExists('procurement_market_analysis');
        Schema::connection($this->connection)->dropIfExists('procurement_preparation_analyses');
        Schema::connection($this->connection)->dropIfExists('procurement_preparations');
        Schema::connection($this->connection)->dropIfExists('procurement_items');
        Schema::connection($this->connection)->dropIfExists('procurement_packages');
        Schema::connection($this->connection)->dropIfExists('procurement_vendors');
        Schema::connection($this->connection)->dropIfExists('kbki_masters');
    }
};