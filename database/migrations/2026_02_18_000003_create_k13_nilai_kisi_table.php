<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateK13NilaiKisiTable extends Migration
{
    public function up()
    {
        Schema::create('k13_nilai_kisi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('k13_rencana_kisi_id');
            $table->unsignedBigInteger('anggota_kelas_id');
            $table->integer('nilai'); // 0-100
            $table->timestamps();

            $table->foreign('k13_rencana_kisi_id')->references('id')->on('k13_rencana_kisi')->onDelete('cascade');
            $table->foreign('anggota_kelas_id')->references('id')->on('anggota_kelas')->onDelete('cascade');

            $table->unique(['k13_rencana_kisi_id', 'anggota_kelas_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('k13_nilai_kisi');
    }
}
