<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->string('nim_mahasiswa')->unique()->nullable();
            $table->string('nama_mahasiswa');
            // $table->string('nik_mahasiswa');
            $table->string('tmpt_lahir');
            $table->string('tgl_lahir');
            $table->string('jk');
            // $table->string('agama');
            // $table->string('stts_pernikahan');
            $table->string('nomor_telepon');
            $table->string('email');
            $table->timestamps();

            // Foreign key untuk kelas
            $table->unsignedBigInteger('id_prodi');
            $table->foreign('id_prodi')->references('id')->on('prodi')->onDelete('cascade');

            // Foreign key untuk program studi
            $table->unsignedBigInteger('id_prodi');
            $table->foreign('id_prodi')->references('id')->on('prodi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
