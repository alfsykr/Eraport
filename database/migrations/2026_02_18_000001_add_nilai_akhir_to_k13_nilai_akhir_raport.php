<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNilaiAkhirToK13NilaiAkhirRaport extends Migration
{
    public function up()
    {
        Schema::table('k13_nilai_akhir_raport', function (Blueprint $table) {
            // Tambah kolom nilai_akhir dan predikat_akhir (nullable agar data lama aman)
            $table->integer('nilai_akhir')->nullable()->after('kkm');
            $table->enum('predikat_akhir', ['A', 'B', 'C', 'D'])->nullable()->after('nilai_akhir');
        });
    }

    public function down()
    {
        Schema::table('k13_nilai_akhir_raport', function (Blueprint $table) {
            $table->dropColumn(['nilai_akhir', 'predikat_akhir']);
        });
    }
}
