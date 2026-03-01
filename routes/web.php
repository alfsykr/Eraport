<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/unauthorized', function () {
  $title = 'Unauthorized';
  return view('errorpage.401', compact('title'));
});


Route::get('/', 'AuthController@index')->name('login');
Route::post('/', 'AuthController@store')->name('login');
Route::post('/settingtapel', 'AuthController@setting_tapel')->name('setting.tapel');

Route::group(['middleware' => ['auth']], function () {

  Route::get('/logout', 'AuthController@logout')->name('logout');
  Route::get('/password', 'AuthController@view_ganti_password')->name('gantipassword');
  Route::post('/password', 'AuthController@ganti_password')->name('gantipassword');

  Route::get('/profile', 'ProfileUserController@index')->name('profile');

  Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

  // Route User Admin 
  Route::group(['middleware' => 'checkRole:1'], function () {
    Route::group(['prefix' => 'admin'], function () {
      Route::resource('profileadmin', 'Admin\ProfileController', [
        'uses' => ['update']
      ]);
      Route::resource('pengumuman', 'Admin\PengumumanController', [
        'uses' => ['index', 'store', 'update']
      ]);

      Route::get('user/export', 'Admin\UserController@export')->name('user.export');
      Route::resource('user', 'Admin\UserController', [
        'uses' => ['index', 'store', 'update']
      ]);
      Route::resource('sekolah', 'Admin\SekolahController', [
        'uses' => ['index', 'update']
      ]);
      Route::get('guru/export', 'Admin\GuruController@export')->name('guru.export');
      Route::get('guru/import', 'Admin\GuruController@format_import')->name('guru.format_import');
      Route::post('guru/import', 'Admin\GuruController@import')->name('guru.import');
      Route::resource('guru', 'Admin\GuruController', [
        'uses' => ['index', 'store', 'update', 'destroy']
      ]);
      Route::resource('tapel', 'Admin\TapelController', [
        'uses' => ['index', 'store', 'update', 'destroy']
      ]);
      Route::post('kelas/anggota', 'Admin\KelasController@store_anggota')->name('kelas.anggota');
      Route::delete('kelas/anggota/{anggota}', 'Admin\KelasController@delete_anggota')->name('kelas.anggota.delete');
      Route::resource('kelas', 'Admin\KelasController', [
        'uses' => ['index', 'store', 'show', 'destroy']
      ]);
      Route::get('siswa/export', 'Admin\SiswaController@export')->name('siswa.export');
      Route::get('siswa/import', 'Admin\SiswaController@format_import')->name('siswa.format_import');
      Route::post('siswa/import', 'Admin\SiswaController@import')->name('siswa.import');
      Route::post('siswa/registrasi', 'Admin\SiswaController@registrasi')->name('siswa.registrasi');
      Route::resource('siswa', 'Admin\SiswaController', [
        'uses' => ['index', 'store', 'update', 'destroy']
      ]);
      Route::get('mapel/import', 'Admin\MapelController@format_import')->name('mapel.format_import');
      Route::post('mapel/import', 'Admin\MapelController@import')->name('mapel.import');
      Route::resource('mapel', 'Admin\MapelController', [
        'uses' => ['index', 'store', 'update', 'destroy']
      ]);
      Route::resource('mapel', 'Admin\MapelController', [
        'uses' => ['index', 'store', 'update', 'destroy']
      ]);
      Route::get('pembelajaran/export', 'Admin\PembelajaranController@export')->name('pembelajaran.export');
      Route::post('pembelajaran/settings', 'Admin\PembelajaranController@settings')->name('pembelajaran.settings');
      Route::resource('pembelajaran', 'Admin\PembelajaranController', [
        'uses' => ['index', 'store']
      ]);
      Route::resource('rekapkehadiran', 'Admin\RekapKehadiranSiswaController', [
        'uses' => ['index', 'store']
      ]);

      Route::get('getKelas/ajax/{id}', 'AjaxController@ajax_kelas');

      // Raport K13 Admin
      Route::group(['middleware' => 'checkKurikulum:2013'], function () {

        // Setting Raport K13
        Route::resource('k13mapping', 'Admin\K13\MapingMapelController', [
          'uses' => ['index', 'store']
        ]);
        Route::get('k13kkm/import', 'Admin\K13\KkmMapelController@format_import')->name('k13kkm.format_import');
        Route::post('k13kkm/import', 'Admin\K13\KkmMapelController@import')->name('k13kkm.import');
        Route::resource('k13kkm', 'Admin\K13\KkmMapelController', [
          'uses' => ['index', 'store', 'update', 'destroy']
        ]);
        Route::resource('k13interval', 'Admin\K13\IntervalPredikatController', [
          'uses' => ['index']
        ]);
        Route::resource('k13tglraport', 'Admin\K13\TglRaportController', [
          'uses' => ['index', 'store', 'update', 'destroy']
        ]);
        Route::resource('k13validasi', 'Admin\K13\ValidasiController', [
          'uses' => ['index']
        ]);

        // Hasil Raport K13 
        Route::resource('k13statuspenilaian', 'Admin\K13\StatusPenilaianController', [
          'uses' => ['index', 'store']
        ]);
        Route::resource('k13pengelolaannilai', 'Admin\K13\PengelolaanNilaiController', [
          'uses' => ['index', 'store']
        ]);
        Route::resource('k13nilairaport', 'Admin\K13\NilaiRaportSemesterController', [
          'uses' => ['index', 'store']
        ]);
        Route::resource('k13leger', 'Admin\K13\LegerNilaiSiswaController', [
          'uses' => ['index', 'store', 'show']
        ]);
        // Route::resource('k13raportpts', 'Admin\K13\CetakRaportPTSController', [
        //   'uses' => ['index', 'store', 'show']
        // ]); // DISABLED - Hanya gunakan Raport Semester, Kisi, dan Personal Program
        Route::resource('k13raportsemester', 'Admin\K13\CetakRaportSemesterController', [
          'uses' => ['index', 'store', 'show']
        ]);
        Route::resource('k13raportkisi', 'Admin\K13\CetakRaportKisiController', [
          'uses' => ['index', 'store', 'show']
        ]);
        // Cetak Raport Personal Program (Admin)
        Route::resource('pp-raport', 'Admin\PersonalProgramController', [
          'uses' => ['index', 'show']
        ])->names([
              'index' => 'pp_raport.index',
              'show' => 'pp_raport.show',
            ]);
      });
      // End  Raport K13 Admin



    });
  });
  // End Route User Admin 

  // Route User Guru 
  Route::group(['middleware' => 'checkRole:2'], function () {
    Route::group(['prefix' => 'guru'], function () {

      Route::resource('profileguru', 'Guru\ProfileController', [
        'uses' => ['update']
      ]);

      Route::get('akses', 'AuthController@ganti_akses')->name('akses');

      // Route Guru Mapel

      // Raport K13 Guru
      Route::group(['middleware' => 'checkKurikulum:2013'], function () {

        // Sistem Kisi-kisi Terpadu (Baru)
        Route::get('rencanakisi/create', 'Guru\K13\RencanaKisiController@create')->name('rencanakisi.create');
        Route::post('rencanakisi/import', 'Guru\K13\RencanaKisiController@importLama')->name('rencanakisi.import');
        Route::resource('rencanakisi', 'Guru\K13\RencanaKisiController', [
          'uses' => ['index', 'store', 'destroy']
        ]);
        Route::get('nilaikisi/create', 'Guru\K13\NilaiKisiController@create')->name('nilaikisi.create');
        Route::resource('nilaikisi', 'Guru\K13\NilaiKisiController', [
          'uses' => ['index', 'store']
        ]);
        Route::resource('nilaiterkirim', 'Guru\K13\LihatNilaiTerkirimController', [
          'uses' => ['index', 'create']
        ]);
        Route::resource('kirimnilaiakhir', 'Guru\K13\KirimNilaiAkhirController', [
          'uses' => ['index', 'create', 'store']
        ]);
        Route::resource('statuspengiriman', 'Guru\K13\StatusPengirimanNilaiController', [
          'uses' => ['index']
        ]);
        // Personal Program input by Guru
        Route::resource('personalprogram', 'Guru\PersonalProgramController', [
          'uses' => ['index', 'create', 'store', 'edit', 'update', 'destroy']
        ]);
      });
      // End  Raport K13 Guru



      //Route Wali Kelas (accessible by any guru who is wali kelas, no session switch needed)
      Route::resource('pesertadidik', 'Walikelas\PesertaDidikController', [
        'uses' => ['index']
      ]);
      Route::resource('kehadiran', 'Walikelas\KehadiranSiswaController', [
        'uses' => ['index', 'store']
      ]);
      Route::resource('prestasi', 'Walikelas\PrestasiSiswaController', [
        'uses' => ['index', 'store', 'destroy']
      ]);
      Route::resource('catatan', 'Walikelas\CatatanWaliKelasController', [
        'uses' => ['index', 'store']
      ]);
      Route::get('terapi', 'Walikelas\TerapiPerkembanganController@index')->name('terapi.index');
      Route::post('terapi', 'Walikelas\TerapiPerkembanganController@store')->name('terapi.store');
      Route::get('terapi/raport/{anggota_kelas}', 'Walikelas\TerapiPerkembanganController@show')->name('terapi.raport');
      Route::resource('kenaikan', 'Walikelas\KenaikanKelasController', [
        'uses' => ['index', 'store']
      ]);

      // Raport K13 Wali Kelas
      Route::group(['middleware' => 'checkKurikulum:2013'], function () {
        Route::get('leger/export', 'Walikelas\K13\LihatLegerNilaiController@export')->name('leger.export');
        Route::resource('leger', 'Walikelas\K13\LihatLegerNilaiController', [
          'uses' => ['index']
        ]);

        Route::resource('raportsemester', 'Walikelas\K13\CetakRaportSemesterController', [
          'uses' => ['index', 'store', 'show']
        ]);

      });
      // End  Raport K13 Wali Kelas


      // End Route Wali Kelas
    });
  });
  // End Route User Guru 

  // Route User Siswa 
  Route::group(['middleware' => 'checkRole:3'], function () {

    Route::resource('profilesiswa', 'Siswa\ProfileController', [
      'uses' => ['update']
    ]);
    Route::resource('presensi', 'Siswa\RekapKehadiranController', [
      'uses' => ['index']
    ]);

    // Raport K13 Siswa
    Route::group(['middleware' => 'checkKurikulum:2013'], function () {
      Route::resource('nilaiakhir', 'Siswa\K13\NilaiAkhirSemesterController', [
        'uses' => ['index']
      ]);
    });
    // End  Raport K13 Siswa


  });
  // End Route User Siswa 

});

// LANJUT KE GURU KTSP
