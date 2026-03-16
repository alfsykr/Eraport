<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Rename tabel prestasi_siswa → talents_mapping
 * dan rename kolom jenis_prestasi → nama_talents, deskripsi → deskripsi_talents
 */
class RenamePrestasiSiswaToTalentsMapping extends Migration
{
    public function up()
    {
        // 1. Rename tabel
        Schema::rename('prestasi_siswa', 'talents_mapping');

        // 2. Rename kolom di dalam tabel
        Schema::table('talents_mapping', function (Blueprint $table) {
            $table->renameColumn('jenis_prestasi', 'nama_talents');
            $table->renameColumn('deskripsi', 'deskripsi_talents');
        });
    }

    public function down()
    {
        Schema::table('talents_mapping', function (Blueprint $table) {
            $table->renameColumn('nama_talents', 'jenis_prestasi');
            $table->renameColumn('deskripsi_talents', 'deskripsi');
        });

        Schema::rename('talents_mapping', 'prestasi_siswa');
    }
}
