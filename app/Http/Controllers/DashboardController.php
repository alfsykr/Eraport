<?php

namespace App\Http\Controllers;

use App\AnggotaKelas;
use App\Guru;
use App\K13NilaiAkhirRaport;
use App\K13KkmMapel;
use App\Kelas;
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

            // Dashboard Guru
            $id_kelas = Kelas::where('tapel_id', $tapel->id)->get('id');

            $jumlah_kelas_diampu = count(Pembelajaran::where('guru_id', $guru->id)->whereIn('kelas_id', $id_kelas)->where('status', 1)->groupBy('kelas_id')->get());
            $jumlah_mapel_diampu = count(Pembelajaran::where('guru_id', $guru->id)->whereIn('kelas_id', $id_kelas)->where('status', 1)->groupBy('mapel_id')->get());

            $id_kelas_diampu = Pembelajaran::where('guru_id', $guru->id)->whereIn('kelas_id', $id_kelas)->where('status', 1)->groupBy('kelas_id')->get('kelas_id');
            $jumlah_siswa_diampu = AnggotaKelas::whereIn('kelas_id', $id_kelas_diampu)->count();

            $data_capaian_penilaian = Pembelajaran::where('guru_id', $guru->id)->whereIn('kelas_id', $id_kelas)->where('status', 1)->get();

            // Capaian Penilaian K13 - Kisi-Kisi
            foreach ($data_capaian_penilaian as $penilaian) {
                $penilaian->jumlah_anggota = \App\AnggotaKelas::where('kelas_id', $penilaian->kelas_id)->count();
                $penilaian->jumlah_rencana_kisi = \App\K13RencanaKisi::where('pembelajaran_id', $penilaian->id)->count();
                $penilaian->jumlah_nilai_kisi = \App\K13NilaiKisi::whereHas('rencana_kisi', function ($q) use ($penilaian) {
                    $q->where('pembelajaran_id', $penilaian->id);
                })->count();
                $penilaian->jumlah_kirim_nilai = \App\K13NilaiAkhirRaport::where('pembelajaran_id', $penilaian->id)
                    ->whereNotNull('nilai_akhir')
                    ->count();
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
