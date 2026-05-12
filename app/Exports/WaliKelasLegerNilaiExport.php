<?php

namespace App\Exports;

use App\AnggotaKelas;
// Ekstrakulikuler, AnggotaEkstrakulikuler, NilaiEkstrakulikuler removed - models not available
// K13DeskripsiSikapSiswa removed - table not yet migrated
use App\Guru;
use App\K13MappingMapel;
use App\K13NilaiAkhirRaport;
use App\Kelas;
use App\Mapel;

use App\Pembelajaran;
use App\Sekolah;
use App\Tapel;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class WaliKelasLegerNilaiExport implements FromView, ShouldAutoSize
{

    public function view(): View
    {
        $time_download = date('Y-m-d H:i:s');

        $sekolah = Sekolah::first();
        $tapel = Tapel::findorfail(session()->get('tapel_id'));
        $guru = Guru::where('user_id', Auth::user()->id)->first();
        $id_kelas_diampu = Kelas::where('tapel_id', $tapel->id)->where('guru_id', $guru->id)->get('id');

        $data_id_mapel_semester_ini = Mapel::where('tapel_id', $tapel->id)->get('id');

        $data_id_mapel_kelompok_a = K13MappingMapel::whereIn('mapel_id', $data_id_mapel_semester_ini)->where('kelompok', 'A')->get('mapel_id');
        $data_id_mapel_kelompok_b = K13MappingMapel::whereIn('mapel_id', $data_id_mapel_semester_ini)->where('kelompok', 'B')->get('mapel_id');

        $data_id_pembelajaran_all = Pembelajaran::whereIn('kelas_id', $id_kelas_diampu)->get('id');
        $data_id_pembelajaran_a = Pembelajaran::whereIn('kelas_id', $id_kelas_diampu)->whereIn('mapel_id', $data_id_mapel_kelompok_a)->get('id');
        $data_id_pembelajaran_b = Pembelajaran::whereIn('kelas_id', $id_kelas_diampu)->whereIn('mapel_id', $data_id_mapel_kelompok_b)->get('id');

        $data_mapel_kelompok_a = K13NilaiAkhirRaport::whereIn('pembelajaran_id', $data_id_pembelajaran_a)->groupBy('pembelajaran_id')->get();
        $data_mapel_kelompok_b = K13NilaiAkhirRaport::whereIn('pembelajaran_id', $data_id_pembelajaran_b)->groupBy('pembelajaran_id')->get();

        // Ekstrakulikuler dinonaktifkan - model tidak tersedia
        $data_ekstrakulikuler = collect();
        $count_ekstrakulikuler = 0;

        $data_anggota_kelas = AnggotaKelas::whereIn('kelas_id', $id_kelas_diampu)->get();
        foreach ($data_anggota_kelas as $anggota_kelas) {

            $data_nilai_kelompok_a = K13NilaiAkhirRaport::whereIn('pembelajaran_id', $data_id_pembelajaran_a)->where('anggota_kelas_id', $anggota_kelas->id)->get();
            $data_nilai_kelompok_b = K13NilaiAkhirRaport::whereIn('pembelajaran_id', $data_id_pembelajaran_b)->where('anggota_kelas_id', $anggota_kelas->id)->get();

            $anggota_kelas->data_nilai_kelompok_a = $data_nilai_kelompok_a;
            $anggota_kelas->data_nilai_kelompok_b = $data_nilai_kelompok_b;

            $rt_nilai_akhir = K13NilaiAkhirRaport::whereIn('pembelajaran_id', $data_id_pembelajaran_all)->where('anggota_kelas_id', $anggota_kelas->id)->avg('nilai_akhir');

            $anggota_kelas->rata_rata_semua = round($rt_nilai_akhir, 0);

            // K13DeskripsiSikapSiswa dinonaktifkan - tabel belum ada
            $anggota_kelas->nilai_spiritual = '-';
            $anggota_kelas->nilai_sosial = '-';

            // Ekstrakulikuler dinonaktifkan - model tidak tersedia
            $anggota_kelas->data_nilai_ekstrakulikuler = collect();
        }

        return view('exports.walikelas.k13.legernilai', compact('time_download', 'sekolah', 'data_mapel_kelompok_a', 'data_mapel_kelompok_b', 'data_ekstrakulikuler', 'count_ekstrakulikuler', 'data_anggota_kelas'));
    }
}
