<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * BACKUP RESTORE MIGRATION
 * File ini berisi struktur tabel yang dihapus oleh:
 * 2026_02_28_000001_drop_unused_tables.php
 *
 * Jalankan `php artisan migrate:rollback` atau gunakan
 * down() ini jika perlu restore tabel yang terhapus.
 */
class BackupRestoreUnusedTables extends Migration
{
    /**
     * up() kosong — file ini hanya untuk rollback/restore.
     */
    public function up()
    {
        // Kosong — tidak ada yang dijalankan saat migrate normal
    }

    /**
     * Restore semua tabel yang dihapus oleh drop_unused_tables.
     * Urutan: parent tables dulu sebelum child tables.
     */
    public function down()
    {
        // ==============================
        // RESTORE GRUP KTSP
        // ==============================

        if (!Schema::hasTable('ktsp_mapping_mapels')) {
            Schema::create('ktsp_mapping_mapels', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tapel_id');
                $table->unsignedBigInteger('mapel_id');
                $table->unsignedBigInteger('guru_id');
                $table->unsignedBigInteger('kelas_id');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ktsp_kkm_mapels')) {
            Schema::create('ktsp_kkm_mapels', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pembelajaran_id');
                $table->integer('kkm')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ktsp_tgl_raports')) {
            Schema::create('ktsp_tgl_raports', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tapel_id');
                $table->date('tgl_raport')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ktsp_bobot_penilaians')) {
            Schema::create('ktsp_bobot_penilaians', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pembelajaran_id');
                $table->integer('bobot_tugas')->nullable();
                $table->integer('bobot_uh')->nullable();
                $table->integer('bobot_uts')->nullable();
                $table->integer('bobot_uas')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ktsp_nilai_tugas')) {
            Schema::create('ktsp_nilai_tugas', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pembelajaran_id');
                $table->unsignedBigInteger('anggota_kelas_id');
                $table->double('nilai')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ktsp_nilai_uhs')) {
            Schema::create('ktsp_nilai_uhs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pembelajaran_id');
                $table->unsignedBigInteger('anggota_kelas_id');
                $table->double('nilai')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ktsp_nilai_uts_uas')) {
            Schema::create('ktsp_nilai_uts_uas', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pembelajaran_id');
                $table->unsignedBigInteger('anggota_kelas_id');
                $table->double('nilai_uts')->nullable();
                $table->double('nilai_uas')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ktsp_nilai_akhir_raports')) {
            Schema::create('ktsp_nilai_akhir_raports', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pembelajaran_id');
                $table->unsignedBigInteger('anggota_kelas_id');
                $table->integer('kkm')->nullable();
                $table->double('nilai_akhir')->nullable();
                $table->string('predikat')->nullable();
                $table->boolean('kirim_nilai')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ktsp_deskripsi_nilai_siswas')) {
            Schema::create('ktsp_deskripsi_nilai_siswas', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pembelajaran_id');
                $table->unsignedBigInteger('anggota_kelas_id');
                $table->text('deskripsi')->nullable();
                $table->timestamps();
            });
        }

        // ==============================
        // RESTORE GRUP EKSTRAKULIKULER
        // ==============================

        if (!Schema::hasTable('ekstrakulikulers')) {
            Schema::create('ekstrakulikulers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tapel_id');
                $table->string('nama_ekstrakulikuler');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('anggota_ekstrakulikulers')) {
            Schema::create('anggota_ekstrakulikulers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ekstrakulikuler_id');
                $table->unsignedBigInteger('anggota_kelas_id');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('nilai_ekstrakulikulers')) {
            Schema::create('nilai_ekstrakulikulers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('anggota_ekstrakulikuler_id');
                $table->string('keterangan')->nullable();
                $table->string('nilai')->nullable();
                $table->timestamps();
            });
        }

        // ==============================
        // RESTORE GRUP K13 LAMA
        // ==============================

        if (!Schema::hasTable('k13_butir_sikaps')) {
            Schema::create('k13_butir_sikaps', function (Blueprint $table) {
                $table->id();
                $table->string('butir_sikap');
                $table->string('jenis_sikap'); // spiritual / sosial
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('k13_rencana_nilai_pengetahuans')) {
            Schema::create('k13_rencana_nilai_pengetahuans', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pembelajaran_id');
                $table->string('jenis_penilaian')->nullable();
                $table->string('teknik_penilaian')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('k13_rencana_nilai_keterampilans')) {
            Schema::create('k13_rencana_nilai_keterampilans', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pembelajaran_id');
                $table->string('jenis_penilaian')->nullable();
                $table->string('teknik_penilaian')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('k13_rencana_nilai_spirituals')) {
            Schema::create('k13_rencana_nilai_spirituals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pembelajaran_id');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('k13_rencana_nilai_sosials')) {
            Schema::create('k13_rencana_nilai_sosials', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pembelajaran_id');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('k13_rencana_bobot_penilaians')) {
            Schema::create('k13_rencana_bobot_penilaians', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pembelajaran_id');
                $table->integer('bobot_pengetahuan')->nullable();
                $table->integer('bobot_keterampilan')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('k13_deskripsi_nilai_siswas')) {
            Schema::create('k13_deskripsi_nilai_siswas', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pembelajaran_id');
                $table->unsignedBigInteger('anggota_kelas_id');
                $table->text('deskripsi_pengetahuan')->nullable();
                $table->text('deskripsi_keterampilan')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('k13_deskripsi_sikap_siswas')) {
            Schema::create('k13_deskripsi_sikap_siswas', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('anggota_kelas_id');
                $table->text('deskripsi_spiritual')->nullable();
                $table->text('deskripsi_sosial')->nullable();
                $table->timestamps();
            });
        }

        // ==============================
        // RESTORE SISWA KELUAR
        // ==============================

        if (!Schema::hasTable('siswa_keluars')) {
            Schema::create('siswa_keluars', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('siswa_id');
                $table->string('keterangan')->nullable();
                $table->date('tgl_keluar')->nullable();
                $table->timestamps();
            });
        }
    }
}
