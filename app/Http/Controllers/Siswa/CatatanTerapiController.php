<?php

namespace App\Http\Controllers\Siswa;

use App\AnggotaKelas;
use App\Http\Controllers\Controller;
use App\Kelas;
use App\Siswa;
use App\TerapiPerkembangan;
use Illuminate\Support\Facades\Auth;

class CatatanTerapiController extends Controller
{
    public function index()
    {
        $title = 'Perkembangan Terapi';
        $siswa = Siswa::where('user_id', Auth::user()->id)->first();

        $data_id_kelas = Kelas::where('tapel_id', session()->get('tapel_id'))->get('id');
        $anggota_kelas = AnggotaKelas::whereIn('kelas_id', $data_id_kelas)->where('siswa_id', $siswa->id)->first();

        if (is_null($anggota_kelas)) {
            return view('siswa.terapi.index', compact('title', 'siswa'));
        }

        $data_terapi = TerapiPerkembangan::where('anggota_kelas_id', $anggota_kelas->id)
            ->orderBy('minggu_tanggal', 'DESC')
            ->get();

        $kelas = $anggota_kelas->kelas;

        return view('siswa.terapi.index', compact('title', 'siswa', 'kelas', 'data_terapi'));
    }
}
