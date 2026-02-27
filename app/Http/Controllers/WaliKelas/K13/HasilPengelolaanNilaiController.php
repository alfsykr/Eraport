<?php

namespace App\Http\Controllers\WaliKelas\K13;

use App\AnggotaKelas;
use App\Guru;
use App\Http\Controllers\Controller;
use App\K13NilaiKisi;
use App\K13RencanaKisi;
use App\Kelas;
use App\Pembelajaran;
use App\Sekolah;
use App\Tapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HasilPengelolaanNilaiController extends Controller
{
    public function index()
    {
        $title = 'Hasil Pengelolaan Nilai Siswa';
        $sekolah = Sekolah::first();
        $tapel = Tapel::findorfail(session()->get('tapel_id'));
        $guru = Guru::where('user_id', Auth::user()->id)->first();
        $id_kelas_diampu = Kelas::where('tapel_id', $tapel->id)->where('guru_id', $guru->id)->get('id');

        $data_anggota_kelas = AnggotaKelas::whereIn('kelas_id', $id_kelas_diampu)->get();

        foreach ($data_anggota_kelas as $anggota_kelas) {
            // Ambil semua pembelajaran di kelas siswa ini, urutkan berdasarkan nomor_urut mapping
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
                $rencana_ids = K13RencanaKisi::where('pembelajaran_id', $pembelajaran->id)->pluck('id');
                if ($rencana_ids->isEmpty())
                    continue;

                $avg_nilai = K13NilaiKisi::whereIn('k13_rencana_kisi_id', $rencana_ids)
                    ->where('anggota_kelas_id', $anggota_kelas->id)
                    ->avg('nilai');

                if (is_null($avg_nilai))
                    continue;

                $nilai_akhir = round($avg_nilai, 0);
                $total_nilai += $nilai_akhir;
                $count_mapel++;

                $data_nilai_mapel[] = [
                    'nama_mapel' => $pembelajaran->mapel->nama_mapel,
                    'nilai_akhir' => $nilai_akhir,
                ];
            }

            $anggota_kelas->data_nilai_mapel = $data_nilai_mapel;
            $anggota_kelas->rata_rata_semua = $count_mapel > 0 ? round($total_nilai / $count_mapel, 0) : '-';
        }

        return view('walikelas.k13.hasilnilai.index', compact('title', 'sekolah', 'data_anggota_kelas'));
    }
}
