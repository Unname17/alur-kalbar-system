<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'sistem_admin';

    public function up()
    {
        // Tabel Perangkat Daerah
        Schema::connection($this->connection)->create('perangkat_daerah', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pd')->unique();
            $table->string('nama_pd');
            $table->string('singkatan', 50);
            $table->timestamps();
        });

        // Tabel Bidang
        Schema::connection($this->connection)->create('bidang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pd_id')->constrained('perangkat_daerah')->onDelete('cascade');
            $table->string('nama_bidang');
            $table->string('kode_bidang');
            $table->timestamps();
        });

        // Tabel Roles
        Schema::connection($this->connection)->create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->timestamps();
        });

        // Tabel Users (Login NIP)
        Schema::connection($this->connection)->create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 20)->unique();
            $table->string('nama_lengkap');
            $table->string('password');
            $table->foreignId('role_id')->constrained('roles');
            $table->foreignId('pd_id')->constrained('perangkat_daerah');
            $table->foreignId('bidang_id')->nullable()->constrained('bidang');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::connection($this->connection)->dropIfExists('users');
        Schema::connection($this->connection)->dropIfExists('roles');
        Schema::connection($this->connection)->dropIfExists('bidang');
        Schema::connection($this->connection)->dropIfExists('perangkat_daerah');
    }
};