<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJadwalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->string('hari'); // Hari (Senin, Selasa, dll)
            $table->time('jam_mulai'); // Waktu mulai
            $table->time('jam_selesai'); // Waktu selesai
            $table->string('smt'); // semester
            $table->string('sks'); // jumlah sks

            // Foreign key ke tabel dosens
            $table->unsignedBigInteger('id_dosen')->nullable();
            $table->foreign('id_dosen')->references('id')->on('dosen')->onDelete('cascade');

            // Foreign key ke tabel kelas
            $table->unsignedBigInteger('id_kelas')->nullable();
            $table->foreign('id_kelas')->references('id')->on('kelas')->onDelete('cascade');

            // Foreign key ke tabel ruangan
            $table->unsignedBigInteger('id_ruangan')->nullable();
            $table->foreign('id_ruangan')->references('id')->on('ruangan')->onDelete('cascade');

            // Foreign key ke tabel matkul
            $table->unsignedBigInteger('kode_matkul')->nullable(); // Nullable
            $table->foreign('kode_matkul')->references('id')->on('matkul')->onDelete('cascade');

            // Foreign key untuk program studi
            $table->unsignedBigInteger('id_prodi');
            $table->foreign('id_prodi')->references('id')->on('prodi')->onDelete('cascade');

            $table->enum('mode_pembelajaran', ['luring', 'daring', 'luring/daring']); // Mode pembelajaran
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jadwal');
    }
}
