<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTerapiPerkembanganTable extends Migration
{
    public function up()
    {
        Schema::create('terapi_perkembangan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('anggota_kelas_id');
            $table->date('minggu_tanggal');
            $table->text('motorik_kasar')->nullable();
            $table->text('sosialisasi')->nullable();
            $table->text('rentang_akademis')->nullable();
            $table->text('evaluasi_sosialisasi')->nullable();
            $table->text('evaluasi_rentang_akademis')->nullable();
            $table->timestamps();

            $table->foreign('anggota_kelas_id')->references('id')->on('anggota_kelas')->onDelete('cascade');
            $table->unique(['anggota_kelas_id','minggu_tanggal']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('terapi_perkembangan');
    }
}







