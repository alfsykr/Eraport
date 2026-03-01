<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class DropUnusedTables extends Migration
{
    /**
     * Drop semua tabel yang tidak terpakai.
     * Urutan: child tables dulu sebelum parent tables.
     *
     * @return void
     */
    public function up()
    {
        // Nonaktifkan FK constraints sementara agar drop tidak terganggu relasi
        Schema::disableForeignKeyConstraints();

        // ==============================
        // GRUP KTSP (tidak ada di routes/controllers)
        // ==============================
        Schema::dropIfExists('ktsp_deskripsi_nilai_siswas');
        Schema::dropIfExists('ktsp_nilai_akhir_raports');
        Schema::dropIfExists('ktsp_nilai_tugas');
        Schema::dropIfExists('ktsp_nilai_uhs');
        Schema::dropIfExists('ktsp_nilai_uts_uas');
        Schema::dropIfExists('ktsp_bobot_penilaians');
        Schema::dropIfExists('ktsp_kkm_mapels');
        Schema::dropIfExists('ktsp_mapping_mapels');
        Schema::dropIfExists('ktsp_tgl_raports');

        // ==============================
        // GRUP EKSTRAKULIKULER
        // Hapus data siswa terdaftar dulu, baru drop tabelnya
        // ==============================
        if (Schema::hasTable('nilai_ekstrakulikulers')) {
            \DB::table('nilai_ekstrakulikulers')->delete();
        }
        if (Schema::hasTable('anggota_ekstrakulikulers')) {
            \DB::table('anggota_ekstrakulikulers')->delete();
        }
        if (Schema::hasTable('ekstrakulikulers')) {
            \DB::table('ekstrakulikulers')->delete();
        }

        Schema::dropIfExists('nilai_ekstrakulikulers');
        Schema::dropIfExists('anggota_ekstrakulikulers');
        Schema::dropIfExists('ekstrakulikulers');

        // ==============================
        // GRUP K13 LAMA (digantikan sistem kisi-kisi baru)
        // ==============================
        Schema::dropIfExists('k13_deskripsi_sikap_siswas');
        Schema::dropIfExists('k13_deskripsi_nilai_siswas');
        Schema::dropIfExists('k13_rencana_nilai_pengetahuans');
        Schema::dropIfExists('k13_rencana_nilai_keterampilans');
        Schema::dropIfExists('k13_rencana_nilai_spirituals');
        Schema::dropIfExists('k13_rencana_nilai_sosials');
        Schema::dropIfExists('k13_rencana_bobot_penilaians');
        Schema::dropIfExists('k13_butir_sikaps');

        // ==============================
        // TABEL LAIN TIDAK TERPAKAI
        // ==============================
        Schema::dropIfExists('siswa_keluars');

        // Aktifkan kembali FK constraints
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Rollback: lihat file backup migration
     * 2026_02_28_000002_backup_restore_unused_tables.php
     * untuk merekonstruksi tabel jika diperlukan.
     *
     * @return void
     */
    public function down()
    {
        // Rollback otomatis tidak tersedia karena struktur tabel kompleks.
        // Gunakan migration 2026_02_28_000002 untuk restore manual.
    }
}
