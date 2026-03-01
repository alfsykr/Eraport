<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Drop semua tabel ekstrakulikuler
 *
 * Tabel yang di-drop (3 tabel):
 *   - nilai_ekstrakulikulers
 *   - anggota_ekstrakulikulers
 *   - ekstrakulikulers
 *
 * Fitur ekstrakulikuler tidak digunakan dalam sistem penilaian K13 berbasis kisi-kisi.
 */
class DropEkstrakulikulerTables extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        $tables_to_drop = [
            // Child tables dulu
            'nilai_ekstrakulikuler',
            'anggota_ekstrakulikuler',
            // Parent table
            'ekstrakulikuler',
        ];

        // Hapus semua data terlebih dahulu
        foreach ($tables_to_drop as $table) {
            if (Schema::hasTable($table)) {
                \DB::table($table)->delete();
            }
        }

        // Drop tabel
        foreach ($tables_to_drop as $table) {
            Schema::dropIfExists($table);
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Rollback tidak tersedia — data yang dihapus tidak bisa dipulihkan.
     */
    public function down()
    {
        // Data permanen tidak bisa di-rollback
    }
}
