<?php

namespace App\Http\Controllers\Siswa\K13;

use App\AnggotaKelas;
use App\Http\Controllers\Controller;
use App\K13NilaiKisi;
use App\K13RencanaKisi;
use App\Kelas;
use App\Pembelajaran;
use App\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NilaiAkhirSemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Nilai Akhir Semester';
        $siswa = Siswa::where('user_id', Auth::user()->id)->first();

        $data_id_kelas = Kelas::where('tapel_id', session()->get('tapel_id'))->get('id');
        $anggota_kelas = AnggotaKelas::whereIn('kelas_id', $data_id_kelas)->where('siswa_id', $siswa->id)->first();
        if (is_null($anggota_kelas)) {
            return back()->with('toast_warning', 'Anda belum masuk ke anggota kelas');
        } else {
            $data_pembelajaran = Pembelajaran::where('kelas_id', $anggota_kelas->kelas_id)->where('status', 1)->get();
            foreach ($data_pembelajaran as $pembelajaran) {
                // Ambil semua rencana kisi untuk pembelajaran ini
                $rencana_kisi_ids = K13RencanaKisi::where('pembelajaran_id', $pembelajaran->id)->pluck('id');
                // Hitung rata-rata nilai kisi-kisi untuk siswa ini
                $rata_nilai = K13NilaiKisi::whereIn('k13_rencana_kisi_id', $rencana_kisi_ids)
                    ->where('anggota_kelas_id', $anggota_kelas->id)
                    ->avg('nilai');
                $pembelajaran->rata_nilai_kisi = $rata_nilai ? round($rata_nilai, 1) : null;
            }
            return view('siswa.k13.nilaiakhir.index', compact('title', 'siswa', 'data_pembelajaran'));
        }
    }
}
