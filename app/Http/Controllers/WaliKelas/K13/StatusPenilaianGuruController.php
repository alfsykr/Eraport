<?php

namespace App\Http\Controllers\WaliKelas\K13;

use App\Guru;
use App\Http\Controllers\Controller;
use App\K13DeskripsiNilaiSiswa;
use App\K13NilaiAkhirRaport;
use App\K13NilaiKisi;
use App\K13RencanaKisi;
use App\Kelas;
use App\Pembelajaran;
use App\Tapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatusPenilaianGuruController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Status Penilaian Oleh Guru';
        $tapel = Tapel::findorfail(session()->get('tapel_id'));
        $guru = Guru::where('user_id', Auth::user()->id)->first();
        $id_kelas_diampu = Kelas::where('tapel_id', $tapel->id)->where('guru_id', $guru->id)->get('id');
        $data_pembelajaran_kelas = Pembelajaran::whereIn('kelas_id', $id_kelas_diampu)->where('status', 1)->get();

        foreach ($data_pembelajaran_kelas as $pembelajaran) {

            // Rencana Kisi-kisi (sistem baru)
            $jumlah_rencana_kisi = K13RencanaKisi::where('pembelajaran_id', $pembelajaran->id)->count();
            $pembelajaran->rencana_kisi = $jumlah_rencana_kisi;

            // Input Nilai Kisi-kisi
            $jumlah_nilai_kisi = K13NilaiKisi::whereHas('rencana_kisi', function ($q) use ($pembelajaran) {
                $q->where('pembelajaran_id', $pembelajaran->id);
            })->count();
            $pembelajaran->nilai_kisi = $jumlah_nilai_kisi;

            // Kirim Nilai Akhir
            $nilai_akhir = K13NilaiAkhirRaport::where('pembelajaran_id', $pembelajaran->id)
                ->whereNotNull('nilai_akhir')
                ->count();
            $pembelajaran->nilai_akhir = $nilai_akhir;

            // Proses Deskripsi
            $deskripsi = K13DeskripsiNilaiSiswa::where('pembelajaran_id', $pembelajaran->id)->count();
            $pembelajaran->deskripsi = $deskripsi;
        }

        return view('walikelas.k13.statusnilaiguru.index', compact('title', 'data_pembelajaran_kelas'));
    }
}
