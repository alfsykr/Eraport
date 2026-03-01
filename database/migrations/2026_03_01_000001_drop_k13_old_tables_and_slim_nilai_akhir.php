<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Drop tabel K13 lama & slim-down k13_nilai_akhir_raport
 *
 * Tabel yang di-drop (14 tabel sistem penilaian lama):
 *   - k13_butir_sikap, k13_kd_mapel
 *   - k13_nilai_pengetahuan, k13_nilai_keterampilan, k13_nilai_sosial
 *   - k13_nilai_spiritual, k13_nilai_pts_pas
 *   - k13_rencana_nilai_pengetahuan, k13_rencana_nilai_keterampilan
 *   - k13_rencana_nilai_sosial, k13_rencana_nilai_spiritual
 *   - k13_rencana_bobot_penilaian
 *   - k13_deskripsi_nilai_siswa, k13_deskripsi_sikap_siswa
 *
 * Kolom yang di-drop dari k13_nilai_akhir_raport:
 *   - nilai_pengetahuan, predikat_pengetahuan
 *   - nilai_keterampilan, predikat_keterampilan
 *   - nilai_spiritual, nilai_sosial
 *
 * Sistem penilaian beralih ke kisi-kisi (k13_rencana_kisi + k13_nilai_kisi)
 */
class DropK13OldTablesAndSlimNilaiAkhir extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        // ===================================================
        // PART 1: Hapus data dan drop 14 tabel K13 lama
        // Urutkan child tables dulu, baru parent tables
        // ===================================================

        $tables_to_drop = [
            // Child tables (dependan) dulu
            'k13_nilai_pengetahuan',
            'k13_nilai_keterampilan',
            'k13_nilai_sosial',
            'k13_nilai_spiritual',
            'k13_nilai_pts_pas',
            'k13_deskripsi_nilai_siswa',
            'k13_deskripsi_sikap_siswa',
            'k13_rencana_nilai_pengetahuan',
            'k13_rencana_nilai_keterampilan',
            'k13_rencana_nilai_sosial',
            'k13_rencana_nilai_spiritual',
            'k13_rencana_bobot_penilaian',
            // Parent tables
            'k13_kd_mapel',
            'k13_butir_sikap',
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

        // ===================================================
        // PART 2: Slim-down tabel k13_nilai_akhir_raport
        // Hapus kolom-kolom lama yang tidak digunakan
        // ===================================================

        if (Schema::hasTable('k13_nilai_akhir_raport')) {
            Schema::table('k13_nilai_akhir_raport', function (Blueprint $table) {
                $columns_to_drop = [
                    'nilai_pengetahuan',
                    'predikat_pengetahuan',
                    'nilai_keterampilan',
                    'predikat_keterampilan',
                    'nilai_spiritual',
                    'nilai_sosial',
                ];

                foreach ($columns_to_drop as $col) {
                    if (Schema::hasColumn('k13_nilai_akhir_raport', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Rollback tidak tersedia — data yang dihapus tidak bisa dipulihkan.
     * Untuk restore tabel, gunakan manual SQL dump atau backup database.
     */
    public function down()
    {
        // Tidak bisa otomatis rollback karena data sudah dihapus permanen.
        // Tambahkan kolom kembali ke k13_nilai_akhir_raport jika perlu rollback struktur:
        Schema::table('k13_nilai_akhir_raport', function (Blueprint $table) {
            $table->integer('nilai_pengetahuan')->nullable();
            $table->enum('predikat_pengetahuan', ['A', 'B', 'C', 'D'])->nullable();
            $table->integer('nilai_keterampilan')->nullable();
            $table->enum('predikat_keterampilan', ['A', 'B', 'C', 'D'])->nullable();
            $table->enum('nilai_spiritual', ['1', '2', '3', '4'])->nullable();
            $table->enum('nilai_sosial', ['1', '2', '3', '4'])->nullable();
        });
    }
}
