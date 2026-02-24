<?php

namespace App\Http\Controllers;

use App\AnggotaKelas;
use App\Guru;
use App\K13DeskripsiNilaiSiswa;
use App\K13KkmMapel;
use App\K13NilaiAkhirRaport;
use App\K13NilaiKeterampilan;
use App\K13NilaiPengetahuan;
use App\K13NilaiPtsPas;
use App\K13NilaiSosial;
use App\K13NilaiSpiritual;
use App\K13RencanaBobotPenilaian;
use App\K13RencanaNilaiKeterampilan;
use App\K13RencanaNilaiPengetahuan;
use App\K13RencanaNilaiSosial;
use App\K13RencanaNilaiSpiritual;
use App\Kelas;
use App\KtspBobotPenilaian;
use App\KtspDeskripsiNilaiSiswa;
use App\KtspKkmMapel;
use App\KtspNilaiAkhirRaport;
use App\KtspNilaiTugas;
use App\KtspNilaiUh;
use App\KtspNilaiUtsUas;
use App\Pembelajaran;
use App\Pengumuman;
use App\RiwayatLogin;
use App\Sekolah;
use App\Siswa;
use App\Tapel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Dashboard';
        $sekolah = Sekolah::first();
        $tapel = Tapel::findorfail(session()->get('tapel_id'));
        $data_pengumuman = Pengumuman::all();
        $data_riwayat_login = RiwayatLogin::where('user_id', '!=', Auth::user()->id)->where('updated_at', '>=', Carbon::today())->orderBy('status_login', 'DESC')->orderBy('updated_at', 'DESC')->get();

        if (Auth::user()->role == 1) {
            $jumlah_guru = Guru::all()->count();
            $jumlah_siswa = Siswa::where('status', 1)->count();
            $jumlah_kelas = Kelas::where('tapel_id', $tapel->id)->count();

            return view('dashboard.admin', compact(
                'title',
                'data_pengumuman',
                'data_riwayat_login',
                'sekolah',
                'tapel',
                'jumlah_guru',
                'jumlah_siswa',
                'jumlah_kelas',
            ));
        } elseif (Auth::user()->role == 2) {
            $guru = Guru::where('user_id', Auth::user()->id)->first();

            // Dashboard Guru (selalu tampil dashboard guru, menu wali kelas muncul kondisional di sidebar)
            if (true) {
                $id_kelas = Kelas::where('tapel_id', $tapel->id)->get('id');

                $jumlah_kelas_diampu = count(Pembelajaran::where('guru_id', $guru->id)->whereIn('kelas_id', $id_kelas)->where('status', 1)->groupBy('kelas_id')->get());
                $jumlah_mapel_diampu = count(Pembelajaran::where('guru_id', $guru->id)->whereIn('kelas_id', $id_kelas)->where('status', 1)->groupBy('mapel_id')->get());

                $id_kelas_diampu = Pembelajaran::where('guru_id', $guru->id)->whereIn('kelas_id', $id_kelas)->where('status', 1)->groupBy('kelas_id')->get('kelas_id');
                $jumlah_siswa_diampu = AnggotaKelas::whereIn('kelas_id', $id_kelas_diampu)->count();

                $data_capaian_penilaian = Pembelajaran::where('guru_id', $guru->id)->whereIn('kelas_id', $id_kelas)->where('status', 1)->get();

                // Capaian Penilaian K13 - Kisi-Kisi
                if (session()->get('kurikulum') == '2013') {
                    foreach ($data_capaian_penilaian as $penilaian) {
                        // Jumlah anggota kelas
                        $penilaian->jumlah_anggota = \App\AnggotaKelas::where('kelas_id', $penilaian->kelas_id)->count();

                        // Rencana Kisi-Kisi
                        $penilaian->jumlah_rencana_kisi = \App\K13RencanaKisi::where('pembelajaran_id', $penilaian->id)->count();

                        // Input Nilai Kisi-Kisi
                        $penilaian->jumlah_nilai_kisi = \App\K13NilaiKisi::whereHas('rencana_kisi', function ($q) use ($penilaian) {
                            $q->where('pembelajaran_id', $penilaian->id);
                        })->count();

                        // Kirim Nilai Akhir
                        $penilaian->jumlah_kirim_nilai = \App\K13NilaiAkhirRaport::where('pembelajaran_id', $penilaian->id)
                            ->whereNotNull('nilai_akhir')
                            ->count();
                    }
                } elseif (session()->get('kurikulum') == '2006') {
                    // Capaian Penilaian KTSP
                    foreach ($data_capaian_penilaian as $penilaian) {
                        $kkm = KtspKkmMapel::where('mapel_id', $penilaian->mapel->id)->where('kelas_id', $penilaian->kelas_id)->first();

                        $nilai_tugas = KtspNilaiTugas::where('pembelajaran_id', $penilaian->id)->get();
                        $penilaian->nilai_tugas = count($nilai_tugas);

                        $nilai_uh = KtspNilaiUh::where('pembelajaran_id', $penilaian->id)->get();
                        $penilaian->nilai_uh = count($nilai_uh);

                        $nilai_uts_uas = KtspNilaiUtsUas::where('pembelajaran_id', $penilaian->id)->get();
                        $penilaian->nilai_uts_uas = count($nilai_uts_uas);

                        $kirim_nilai = KtspNilaiAkhirRaport::where('pembelajaran_id', $penilaian->id)->get();
                        $penilaian->kirim_nilai = count($kirim_nilai);

                        $deskripsi = KtspDeskripsiNilaiSiswa::where('pembelajaran_id', $penilaian->id)->get();
                        $penilaian->deskripsi = count($deskripsi);

                        $bobot = KtspBobotPenilaian::where('pembelajaran_id', $penilaian->id)->first();
                        if (is_null($bobot)) {
                            $penilaian->bobot_tugas = null;
                            $penilaian->bobot_uh = null;
                            $penilaian->bobot_uts = null;
                            $penilaian->bobot_uas = null;
                        } else {
                            $penilaian->bobot_tugas = $bobot->bobot_tugas;
                            $penilaian->bobot_uh = $bobot->bobot_uh;
                            $penilaian->bobot_uts = $bobot->bobot_uts;
                            $penilaian->bobot_uas = $bobot->bobot_uas;
                        }

                        if (is_null($kkm)) {
                            $penilaian->kkm = null;
                        } else {
                            $penilaian->kkm = $kkm->kkm;
                        }
                    }
                }

                return view('dashboard.guru', compact(
                    'title',
                    'data_pengumuman',
                    'data_riwayat_login',
                    'sekolah',
                    'tapel',
                    'jumlah_kelas_diampu',
                    'jumlah_mapel_diampu',
                    'jumlah_siswa_diampu',
                    'data_capaian_penilaian',
                ));
            } elseif (session()->get('akses_sebagai') == 'Wali Kelas') {

                $id_kelas_diampu = Kelas::where('tapel_id', $tapel->id)->where('guru_id', $guru->id)->get('id');
                $jumlah_anggota_kelas = count(AnggotaKelas::whereIn('kelas_id', $id_kelas_diampu)->get());

                $id_pembelajaran_kelas = Pembelajaran::whereIn('kelas_id', $id_kelas_diampu)->where('status', 1)->get('id');
                if (session()->get('kurikulum') == '2013') {
                    $jumlah_kirim_nilai = count(K13NilaiAkhirRaport::whereIn('pembelajaran_id', $id_pembelajaran_kelas)->groupBy('pembelajaran_id')->get());
                    $jumlah_proses_deskripsi = count(K13DeskripsiNilaiSiswa::whereIn('pembelajaran_id', $id_pembelajaran_kelas)->groupBy('pembelajaran_id')->get());
                } elseif (session()->get('kurikulum') == '2006') {
                    $jumlah_kirim_nilai = count(KtspNilaiAkhirRaport::whereIn('pembelajaran_id', $id_pembelajaran_kelas)->groupBy('pembelajaran_id')->get());
                    $jumlah_proses_deskripsi = count(KtspDeskripsiNilaiSiswa::whereIn('pembelajaran_id', $id_pembelajaran_kelas)->groupBy('pembelajaran_id')->get());
                }

                // Dashboard Wali Kelas
                return view('dashboard.walikelas', compact(
                    'title',
                    'data_pengumuman',
                    'data_riwayat_login',
                    'sekolah',
                    'tapel',
                    'jumlah_anggota_kelas',
                    'jumlah_kirim_nilai',
                    'jumlah_proses_deskripsi',
                ));
            }
        } elseif (Auth::user()->role == 3) {

            $siswa = Siswa::where('user_id', Auth::user()->id)->first();

            $data_id_kelas = Kelas::where('tapel_id', $tapel->id)->get('id');

            $anggota_kelas = AnggotaKelas::whereIn('kelas_id', $data_id_kelas)->where('siswa_id', $siswa->id)->first();
            if (is_null($anggota_kelas)) {
                $jumlah_mapel = '-';
            } else {
                $jumlah_mapel = Pembelajaran::where('kelas_id', $anggota_kelas->kelas->id)->where('status', 1)->count();
            }

            return view('dashboard.siswa', compact(
                'title',
                'data_pengumuman',
                'data_riwayat_login',
                'sekolah',
                'tapel',
                'jumlah_mapel',
            ));
        }
    }
}
