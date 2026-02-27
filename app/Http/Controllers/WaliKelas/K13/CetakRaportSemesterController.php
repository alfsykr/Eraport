<?php

namespace App\Http\Controllers\WaliKelas\K13;

use App\AnggotaKelas;
use App\CatatanWaliKelas;
use App\Guru;
use App\Http\Controllers\Controller;
use App\K13KkmMapel;
use App\K13NilaiKisi;
use App\K13RencanaKisi;
use App\K13TglRaport;
use App\KehadiranSiswa;
use App\Kelas;
use App\Pembelajaran;
use App\PrestasiSiswa;
use App\Sekolah;
use App\Tapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;

class CetakRaportSemesterController extends Controller
{
    public function index()
    {
        $title = 'Cetak Raport Semester';
        return view('walikelas.k13.raportsemester.setpaper', compact('title'));
    }

    public function store(Request $request)
    {
        $title = 'Cetak Raport Semester';
        $tapel = Tapel::findorfail(session()->get('tapel_id'));
        $guru = Guru::where('user_id', Auth::user()->id)->first();
        $id_kelas_diampu = Kelas::where('tapel_id', $tapel->id)->where('guru_id', $guru->id)->get('id');
        $data_anggota_kelas = AnggotaKelas::whereIn('kelas_id', $id_kelas_diampu)->get();

        $paper_size = $request->paper_size;
        $orientation = $request->orientation;

        return view('walikelas.k13.raportsemester.index', compact('title', 'data_anggota_kelas', 'paper_size', 'orientation'));
    }

    public function show(Request $request, $id)
    {
        $sekolah = Sekolah::first();
        $anggota_kelas = AnggotaKelas::findorfail($id);

        if ($request->data_type == 1) {
            $title = 'Kelengkapan Raport';
            $kelengkapan_raport = PDF::loadview('walikelas.k13.raportsemester.kelengkapanraport', compact('title', 'sekolah', 'anggota_kelas'))->setPaper($request->paper_size, $request->orientation);
            return $kelengkapan_raport->stream('KELENGKAPAN RAPORT ' . $anggota_kelas->siswa->nama_lengkap . ' (' . $anggota_kelas->kelas->nama_kelas . ').pdf');

        } elseif ($request->data_type == 2) {
            $title = 'Cetak Raport';

            $cek_tanggal_raport = K13TglRaport::where('tapel_id', session()->get('tapel_id'))->first();
            if (is_null($cek_tanggal_raport)) {
                return back()->with('toast_warning', 'Tanggal raport belum disetting oleh admin');
            }

            // Ambil semua pembelajaran di kelas ini, urutkan berdasarkan nomor_urut mapping
            $pembelajaran_list = Pembelajaran::with(['mapel.k13_mapping_mapel'])
                ->where('kelas_id', $anggota_kelas->kelas_id)
                ->get()
                ->sortBy(function ($item) {
                    return optional($item->mapel->k13_mapping_mapel)->nomor_urut ?? 99;
                });

            $data_nilai_mapel = [];
            $total_nilai = 0;
            $count_mapel = 0;

            foreach ($pembelajaran_list as $pembelajaran) {
                // Ambil semua rencana kisi-kisi untuk pembelajaran ini
                $rencana_ids = K13RencanaKisi::where('pembelajaran_id', $pembelajaran->id)->pluck('id');

                if ($rencana_ids->isEmpty())
                    continue;

                // Hitung rata-rata nilai kisi-kisi siswa untuk mapel ini
                $avg_nilai = K13NilaiKisi::whereIn('k13_rencana_kisi_id', $rencana_ids)
                    ->where('anggota_kelas_id', $anggota_kelas->id)
                    ->avg('nilai');

                if (is_null($avg_nilai))
                    continue;

                $nilai_akhir = round($avg_nilai, 0);

                // Ambil KKM mapel
                $kkm_obj = K13KkmMapel::where('mapel_id', $pembelajaran->mapel_id)
                    ->where('kelas_id', $anggota_kelas->kelas_id)
                    ->first();
                $kkm = $kkm_obj ? round($kkm_obj->kkm, 0) : 0;

                // Hitung predikat
                $predikat = '-';
                if ($kkm > 0) {
                    $range = (100 - $kkm) / 3;
                    if ($nilai_akhir >= $kkm + ($range * 2))
                        $predikat = 'A';
                    elseif ($nilai_akhir >= $kkm + $range)
                        $predikat = 'B';
                    elseif ($nilai_akhir >= $kkm)
                        $predikat = 'C';
                    else
                        $predikat = 'D';
                }

                $data_nilai_mapel[] = [
                    'nama_mapel' => $pembelajaran->mapel->nama_mapel,
                    'kkm' => $kkm ?: '-',
                    'nilai_akhir' => $nilai_akhir,
                    'predikat_akhir' => $predikat,
                ];

                $total_nilai += $nilai_akhir;
                $count_mapel++;
            }

            $total_nilai_akhir = $total_nilai;
            $rata_rata_nilai_akhir = $count_mapel > 0 ? round($total_nilai / $count_mapel, 0) : 0;

            $data_prestasi_siswa = PrestasiSiswa::where('anggota_kelas_id', $anggota_kelas->id)->get();
            $kehadiran_siswa = KehadiranSiswa::where('anggota_kelas_id', $anggota_kelas->id)->first();
            $catatan_wali_kelas = CatatanWaliKelas::where('anggota_kelas_id', $anggota_kelas->id)->first();

            $raport = PDF::loadview('walikelas.k13.raportsemester.raport', compact(
                'title',
                'sekolah',
                'anggota_kelas',
                'data_nilai_mapel',
                'total_nilai_akhir',
                'rata_rata_nilai_akhir',
                'data_prestasi_siswa',
                'kehadiran_siswa',
                'catatan_wali_kelas'
            ))->setPaper($request->paper_size, $request->orientation);

            return $raport->stream('RAPORT ' . $anggota_kelas->siswa->nama_lengkap . ' (' . $anggota_kelas->kelas->nama_kelas . ').pdf');
        }
    }
}
