<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// database/migrations/..._create_rka_perencanaan_table.php

public function up()
{
    Schema::connection('modul_anggaran')->create('rka_perencanaan', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('kak_id')->unique(); // ID asli dari DB KAK
        $table->string('judul_kak');
        $table->string('kode_proyek')->nullable();
        $table->string('nama_pembuat');
        $table->decimal('pagu_indikatif', 15, 2)->default(0);
        $table->enum('status_internal', ['baru', 'draft', 'pengajuan', 'revisi', 'final'])->default('baru');
        $table->text('catatan_revisi')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('rka_perencanaan');
    }
};
