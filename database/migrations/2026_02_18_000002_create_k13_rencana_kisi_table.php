<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateK13RencanaKisiTable extends Migration
{
    public function up()
    {
        Schema::create('k13_rencana_kisi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembelajaran_id');
            $table->unsignedBigInteger('k13_kd_mapel_id')->nullable(); // KD opsional
            $table->text('deskripsi_penilaian');  // deskripsi indikator/KD
            $table->integer('urutan')->default(0); // urutan tampil di raport
            $table->timestamps();

            $table->foreign('pembelajaran_id')->references('id')->on('pembelajaran')->onDelete('cascade');
            $table->foreign('k13_kd_mapel_id')->references('id')->on('k13_kd_mapel')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('k13_rencana_kisi');
    }
}
