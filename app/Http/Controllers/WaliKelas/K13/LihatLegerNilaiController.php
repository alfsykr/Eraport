<?php

namespace App\Http\Controllers\WaliKelas\K13;

use App\AnggotaKelas;
use App\Exports\WaliKelasLegerNilaiExport;
use App\Guru;
use App\Http\Controllers\Controller;
use App\K13NilaiKisi;
use App\K13RencanaKisi;
use App\KehadiranSiswa;
use App\Kelas;
use App\Pembelajaran;
use App\Sekolah;
use App\Tapel;
use Illuminate\Http\Request;
use Excel;
use Illuminate\Support\Facades\Auth;

class LihatLegerNilaiController extends Controller
{
    public function index()
    {
        $title = 'Leger Nilai Siswa';
        $tapel = Tapel::findorfail(session()->get('tapel_id'));
        $guru = Guru::where('user_id', Auth::user()->id)->first();
        $id_kelas_diampu = Kelas::where('tapel_id', $tapel->id)->where('guru_id', $guru->id)->get('id');
        $sekolah = Sekolah::first();

        $data_anggota_kelas = AnggotaKelas::whereIn('kelas_id', $id_kelas_diampu)->get();

        foreach ($data_anggota_kelas as $anggota_kelas) {
            $pembelajaran_list = Pembelajaran::with(['mapel.k13_mapping_mapel'])
                ->where('kelas_id', $anggota_kelas->kelas_id)
                ->get()
                ->sortBy(function ($item) {
                    return optional($item->mapel->k13_mapping_mapel)->nomor_urut ?? 99;
                });

            $total_nilai = 0;
            $count_mapel = 0;

            foreach ($pembelajaran_list as $pembelajaran) {
                $rencana_ids = K13RencanaKisi::where('pembelajaran_id', $pembelajaran->id)->pluck('id');
                if ($rencana_ids->isEmpty())
                    continue;

                $avg = K13NilaiKisi::whereIn('k13_rencana_kisi_id', $rencana_ids)
                    ->where('anggota_kelas_id', $anggota_kelas->id)
                    ->avg('nilai');

                if (is_null($avg))
                    continue;

                $total_nilai += round($avg, 0);
                $count_mapel++;
            }

            $anggota_kelas->rata_rata_semua = $count_mapel > 0 ? round($total_nilai / $count_mapel, 0) : '-';
            $anggota_kelas->kehadiran_siswa = KehadiranSiswa::where('anggota_kelas_id', $anggota_kelas->id)->first();
        }

        return view('walikelas.k13.leger.index', compact('title', 'sekolah', 'data_anggota_kelas'));
    }

    public function export()
    {
        $filename = 'leger_nilai_siswa_k13 ' . date('Y-m-d H_i_s') . '.xls';
        return Excel::download(new WaliKelasLegerNilaiExport, $filename);
    }
}
