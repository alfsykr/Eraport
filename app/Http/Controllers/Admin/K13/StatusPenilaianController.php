<?php

namespace App\Http\Controllers\Admin\K13;

use App\Http\Controllers\Controller;
use App\K13NilaiAkhirRaport;
use App\K13NilaiKisi;
use App\K13RencanaKisi;
use App\Kelas;
use App\Pembelajaran;
use App\Tapel;
use Illuminate\Http\Request;

class StatusPenilaianController extends Controller
{
    public function index()
    {
        $title = 'Status Penilaian';
        $data_kelas = Kelas::where('tapel_id', session()->get('tapel_id'))->get();
        return view('admin.k13.statuspenilaian.pilihkelas', compact('title', 'data_kelas'));
    }

    public function store(Request $request)
    {
        $title = 'Hasil Pengelolaan Nilai';
        $tapel = Tapel::findorfail(session()->get('tapel_id'));
        $data_kelas = Kelas::where('tapel_id', $tapel->id)->get();

        $kelas = Kelas::findorfail($request->kelas_id);

        $data_pembelajaran_kelas = Pembelajaran::where('kelas_id', $kelas->id)
            ->where('status', 1)
            ->whereNotNull('guru_id')
            ->with(['mapel', 'guru'])
            ->get();

        foreach ($data_pembelajaran_kelas as $pembelajaran) {
            // Status Rencana Kisi-Kisi
            $jumlah_rencana = K13RencanaKisi::where('pembelajaran_id', $pembelajaran->id)->count();
            $pembelajaran->rencana_kisi = $jumlah_rencana;

            // Status Input Nilai Kisi-Kisi
            $jumlah_nilai = K13NilaiKisi::whereHas('rencana_kisi', function ($q) use ($pembelajaran) {
                $q->where('pembelajaran_id', $pembelajaran->id);
            })->count();
            $pembelajaran->nilai_kisi = $jumlah_nilai;

            // Status Kirim Nilai Akhir
            $jumlah_akhir = K13NilaiAkhirRaport::where('pembelajaran_id', $pembelajaran->id)
                ->whereNotNull('nilai_akhir')
                ->count();
            $pembelajaran->nilai_akhir_kisi = $jumlah_akhir;
        }

        return view('admin.k13.statuspenilaian.index', compact('title', 'kelas', 'data_kelas', 'data_pembelajaran_kelas'));
    }
}
