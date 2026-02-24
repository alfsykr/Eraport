<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonalProgramTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personal_program', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('siswa_id');
            $table->string('semester');
            $table->text('motorik_kasar')->nullable();
            $table->text('sosialisasi')->nullable();
            $table->text('rentang_akademis')->nullable();
            $table->text('evaluasi_motorik_kasar')->nullable();
            $table->text('evaluasi_sosialisasi')->nullable();
            $table->text('evaluasi_rentang_akademis')->nullable();
            $table->unsignedBigInteger('guru_id')->nullable();
            $table->timestamps();

            $table->foreign('siswa_id')->references('id')->on('siswa');
            $table->foreign('guru_id')->references('id')->on('guru');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personal_program');
    }
}
