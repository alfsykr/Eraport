<?php

namespace App\Http\Controllers\Admin\K13;

use App\AnggotaKelas;
use App\Http\Controllers\Controller;
use App\K13NilaiKisi;
use App\K13RencanaKisi;
use App\KehadiranSiswa;
use App\Kelas;
use App\Pembelajaran;
use App\Exports\AdminK13LegerNilaiExport;
use App\Sekolah;
use App\Tapel;
use Illuminate\Http\Request;
use Excel;

class LegerNilaiSiswaController extends Controller
{
    public function index()
    {
        $title = 'Leger Nilai Siswa';
        $data_kelas = Kelas::where('tapel_id', session()->get('tapel_id'))->get();
        return view('admin.k13.leger.pilihkelas', compact('title', 'data_kelas'));
    }

    public function store(Request $request)
    {
        $title = 'Leger Nilai Siswa';
        $tapel = Tapel::findorfail(session()->get('tapel_id'));
        $kelas = Kelas::findorfail($request->kelas_id);
        $data_kelas = Kelas::where('tapel_id', $tapel->id)->get();
        $sekolah = Sekolah::first();

        $data_anggota_kelas = AnggotaKelas::where('kelas_id', $kelas->id)->get();

        foreach ($data_anggota_kelas as $anggota_kelas) {
            // Ambil semua pembelajaran di kelas ini
            $pembelajaran_list = Pembelajaran::with(['mapel.k13_mapping_mapel'])
                ->where('kelas_id', $kelas->id)
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

        return view('admin.k13.leger.index', compact(
            'title',
            'kelas',
            'data_kelas',
            'sekolah',
            'data_anggota_kelas'
        ));
    }

    public function show($id)
    {
        $kelas = Kelas::findorfail($id);
        $filename = 'leger_nilai_k13_siswa_kelas ' . $kelas->nama_kelas . ' ' . date('Y-m-d H_i_s') . '.xls';
        return Excel::download(new AdminK13LegerNilaiExport($id), $filename);
    }
}
