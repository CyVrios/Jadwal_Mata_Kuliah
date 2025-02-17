<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Menghapus kolom yang tidak diperlukan
        Schema::table('dosen', function (Blueprint $table) {
            $table->dropColumn('id_dosen');
        });

        Schema::table('prodi', function (Blueprint $table) {
            $table->dropColumn('id_prodi');
        });

        Schema::table('ruangan', function (Blueprint $table) {
            $table->dropColumn('id_ruangan');
        });
    }

    public function down()
    {
        // Jika ingin rollback, tambahkan kembali kolom yang dihapus
        Schema::table('dosen', function (Blueprint $table) {
            $table->string('id_dosen')->unique();
        });

        Schema::table('prodi', function (Blueprint $table) {
            $table->string('id_prodi')->unique();
        });

        Schema::table('ruangan', function (Blueprint $table) {
            $table->string('id_ruangan')->unique();
        });
    }
};
