<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Drop semua tabel KTSP 2006
 *
 * Tabel yang di-drop (9 tabel):
 *   - ktsp_deskripsi_nilai_siswa
 *   - ktsp_nilai_akhir_raport
 *   - ktsp_nilai_uts_uas
 *   - ktsp_nilai_uh
 *   - ktsp_nilai_tugas
 *   - ktsp_bobot_penilaian
 *   - ktsp_kkm_mapel
 *   - ktsp_tgl_raport
 *   - ktsp_mapping_mapel
 *
 * Sistem beralih ke K13 sebagai satu-satunya kurikulum yang digunakan.
 */
class DropKtspTables extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        $tables_to_drop = [
            // Child tables (dependan) dulu
            'ktsp_deskripsi_nilai_siswa',
            'ktsp_nilai_akhir_raport',
            'ktsp_nilai_uts_uas',
            'ktsp_nilai_uh',
            'ktsp_nilai_tugas',
            'ktsp_bobot_penilaian',
            // Parent tables
            'ktsp_kkm_mapel',
            'ktsp_tgl_raport',
            'ktsp_mapping_mapel',
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
