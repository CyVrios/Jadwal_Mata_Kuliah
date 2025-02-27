<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('matkul', function (Blueprint $table) {
            $table->id();
            $table->string('kode_matkul')->unique();
            $table->string('nama_matkul');
            $table->string('smt');
            $table->string('sks');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('matkul');
    }
};
