<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJenisProgramToSiswaTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->string('jenis_program', 20)->nullable()->after('status')
                ->comment('reguler atau inklusi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropColumn('jenis_program');
        });
    }
}
