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
       Schema::create('jadwal_2', function (Blueprint $table) {
            $table->id();
            $table->string('hari', 255); // Hari (Senin, Selasa, dll)
            $table->date('tanggal'); // Tanggal untuk jadwal berkala
            $table->time('jam_mulai'); // Waktu mulai
            $table->time('jam_selesai'); // Waktu selesai
            $table->string('kelas', 50)->nullable(); // Kelas, sesuai dengan database
        
            // Foreign key ke tabel dosen
            $table->unsignedBigInteger('dosen_id')->nullable();
            $table->foreign('dosen_id')->references('id')->on('dosen')->onDelete('no action');
        
            // Foreign key ke tabel ruangan
            $table->unsignedBigInteger('ruangan_id')->nullable();
            $table->foreign('ruangan_id')->references('id')->on('ruangan')->onDelete('no action');
        
            // Foreign key ke tabel matkul
            $table->unsignedBigInteger('kode_matkul')->nullable();
            $table->foreign('kode_matkul')->references('id')->on('matkul')->onDelete('no action');
        
            // Foreign key untuk program studi
            $table->unsignedBigInteger('prodi_id');
            $table->foreign('prodi_id')->references('id')->on('prodi')->onDelete('no action');
        
            $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
