<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Rename tabel prestasi_siswa → talents_mapping
 * dan rename kolom jenis_prestasi → nama_talents, deskripsi → deskripsi_talents
 *
 * Dump SQL lama sering membuat kedua tabel sekaligus; rename langsung akan gagal.
 */
class RenamePrestasiSiswaToTalentsMapping extends Migration
{
    public function up()
    {
        $hasPrestasi = Schema::hasTable('prestasi_siswa');
        $hasTalents = Schema::hasTable('talents_mapping');

        if (!$hasTalents && $hasPrestasi) {
            Schema::rename('prestasi_siswa', 'talents_mapping');
            $this->renameLegacyTalentColumns('talents_mapping');

            return;
        }

        if ($hasTalents) {
            $this->renameLegacyTalentColumns('talents_mapping');
        }

        if ($hasPrestasi && $hasTalents) {
            $this->mergePrestasiSiswaIntoTalentsMapping();
            Schema::dropIfExists('prestasi_siswa');
        }
    }

    /**
     * Rename jenis_prestasi/deskripsi → nama_talents/deskripsi_talents (MySQL CHANGE, tanpa doctrine/dbal).
     */
    private function renameLegacyTalentColumns(string $table): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        if (Schema::hasColumn($table, 'nama_talents')) {
            return;
        }

        if (!Schema::hasColumn($table, 'jenis_prestasi')) {
            return;
        }

        DB::statement("ALTER TABLE `{$table}` CHANGE `jenis_prestasi` `nama_talents` VARCHAR(200) NOT NULL");

        if (Schema::hasColumn($table, 'deskripsi') && !Schema::hasColumn($table, 'deskripsi_talents')) {
            DB::statement("ALTER TABLE `{$table}` CHANGE `deskripsi` `deskripsi_talents` VARCHAR(200) NULL");
        }
    }

    /**
     * Salin baris dari prestasi_siswa ke talents_mapping (id auto), lalu tabel lama dihapus.
     */
    private function mergePrestasiSiswaIntoTalentsMapping(): void
    {
        if (!Schema::hasColumn('talents_mapping', 'nama_talents')) {
            return;
        }

        $prestasiCols = Schema::getColumnListing('prestasi_siswa');
        if (!in_array('jenis_prestasi', $prestasiCols, true)) {
            return;
        }

        DB::statement('INSERT INTO `talents_mapping` (`anggota_kelas_id`, `nama_talents`, `deskripsi_talents`, `created_at`, `updated_at`)
            SELECT p.`anggota_kelas_id`, p.`jenis_prestasi`, p.`deskripsi`, p.`created_at`, p.`updated_at`
            FROM `prestasi_siswa` p
            WHERE NOT EXISTS (
                SELECT 1 FROM `talents_mapping` t
                WHERE t.`anggota_kelas_id` <=> p.`anggota_kelas_id`
                  AND t.`nama_talents` <=> p.`jenis_prestasi`
                  AND t.`deskripsi_talents` <=> p.`deskripsi`
            )');
    }

    public function down()
    {
        if (!Schema::hasTable('talents_mapping')) {
            return;
        }

        if (Schema::hasColumn('talents_mapping', 'nama_talents') && !Schema::hasColumn('talents_mapping', 'jenis_prestasi')) {
            DB::statement('ALTER TABLE `talents_mapping` CHANGE `nama_talents` `jenis_prestasi` VARCHAR(200) NOT NULL');
        }
        if (Schema::hasColumn('talents_mapping', 'deskripsi_talents') && !Schema::hasColumn('talents_mapping', 'deskripsi')) {
            DB::statement('ALTER TABLE `talents_mapping` CHANGE `deskripsi_talents` `deskripsi` VARCHAR(200) NULL');
        }

        Schema::rename('talents_mapping', 'prestasi_siswa');
    }
}
