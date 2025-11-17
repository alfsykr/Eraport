<?php

namespace App\Http\Controllers\Admin\K13;

use App\AnggotaKelas;
use App\Http\Controllers\Controller;
use App\K13NilaiKeterampilan;
use App\K13NilaiPengetahuan;
use App\K13RencanaNilaiKeterampilan;
use App\K13RencanaNilaiPengetahuan;
use App\K13TglRaport;
use App\Kelas;
use App\Pembelajaran;
use App\Sekolah;
use Illuminate\Http\Request;
use PDF;

class CetakRaportKisiController extends Controller
{
    public function index()
    {
        $title = 'Raport Kisi-Kisi';
        $data_kelas = Kelas::where('tapel_id', session()->get('tapel_id'))->get();
        return view('admin.k13.raportkisi.setpaper', compact('title', 'data_kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|integer',
            'paper_size' => 'required|string',
            'orientation' => 'required|string',
        ]);

        $title = 'Raport Kisi-Kisi';
        $kelas = Kelas::findOrFail($request->kelas_id);
        $data_kelas = Kelas::where('tapel_id', session()->get('tapel_id'))->get();
        $data_anggota_kelas = AnggotaKelas::where('kelas_id', $kelas->id)->get();

        $paper_size = $request->paper_size;
        $orientation = $request->orientation;

        return view('admin.k13.raportkisi.index', compact('title', 'kelas', 'data_kelas', 'data_anggota_kelas', 'paper_size', 'orientation'));
    }

    public function show(Request $request, $id)
    {
        $sekolah = Sekolah::first();
        $anggota_kelas = AnggotaKelas::with(['kelas.tapel', 'kelas.guru'])->findOrFail($id);

        $cek_tanggal_raport = K13TglRaport::where('tapel_id', session()->get('tapel_id'))->first();
        if (is_null($cek_tanggal_raport)) {
            return back()->with('toast_warning', 'Tanggal raport belum disetting oleh admin');
        }

        $pembelajaran = Pembelajaran::with(['mapel.k13_mapping_mapel'])
            ->where('kelas_id', $anggota_kelas->kelas_id)
            ->get()
            ->sortBy(function ($item) {
                $kelompok = optional($item->mapel->k13_mapping_mapel)->kelompok ?? 'Z';
                $urutan = optional($item->mapel->k13_mapping_mapel)->nomor_urut ?? 99;
                return $kelompok . str_pad($urutan, 2, '0', STR_PAD_LEFT);
            });

        $data_mapel = [];

        foreach ($pembelajaran as $mapel) {
            $detail_pengetahuan = $this->kisiPengetahuan($mapel, $anggota_kelas->id);
            $detail_keterampilan = $this->kisiKeterampilan($mapel, $anggota_kelas->id);

            if (count($detail_pengetahuan) === 0 && count($detail_keterampilan) === 0) {
                continue;
            }

            $data_mapel[] = [
                'mapel' => $mapel->mapel->nama_mapel,
                'pengetahuan' => $detail_pengetahuan,
                'keterampilan' => $detail_keterampilan,
            ];
        }

        $pdf = PDF::loadview('walikelas.k13.raportkisi.raport', [
            'title' => 'Raport Kisi-Kisi',
            'sekolah' => $sekolah,
            'anggota_kelas' => $anggota_kelas,
            'data_mapel' => $data_mapel,
            'tanggal_raport' => $cek_tanggal_raport,
        ])->setPaper($request->paper_size, $request->orientation);

        $filename = 'RAPORT KISI-KISI ' . ($anggota_kelas->siswa->nama_lengkap ?? 'SISWA') . ' (' . ($anggota_kelas->kelas->nama_kelas ?? '') . ').pdf';
        return $pdf->stream($filename);
    }

    private function kisiPengetahuan($pembelajaran, $anggota_kelas_id)
    {
        $rencana = K13RencanaNilaiPengetahuan::with('k13_kd_mapel')
            ->where('pembelajaran_id', $pembelajaran->id)
            ->get()
            ->groupBy('k13_kd_mapel_id');

        $hasil = [];

        foreach ($rencana as $kd_id => $items) {
            $kd = $items->first()->k13_kd_mapel;

            if (is_null($kd)) {
                continue;
            }

            $nilai = K13NilaiPengetahuan::where('anggota_kelas_id', $anggota_kelas_id)
                ->whereIn('k13_rencana_nilai_pengetahuan_id', $items->pluck('id'))
                ->avg('nilai');

            $hasil[] = [
                'kode' => $kd->kode_kd,
                'kompetensi' => $kd->kompetensi_dasar,
                'nilai' => is_null($nilai) ? '-' : round($nilai, 0),
            ];
        }

        return $hasil;
    }

    private function kisiKeterampilan($pembelajaran, $anggota_kelas_id)
    {
        $rencana = K13RencanaNilaiKeterampilan::with('k13_kd_mapel')
            ->where('pembelajaran_id', $pembelajaran->id)
            ->get()
            ->groupBy('k13_kd_mapel_id');

        $hasil = [];

        foreach ($rencana as $kd_id => $items) {
            $kd = $items->first()->k13_kd_mapel;

            if (is_null($kd)) {
                continue;
            }

            $nilai = K13NilaiKeterampilan::where('anggota_kelas_id', $anggota_kelas_id)
                ->whereIn('k13_rencana_nilai_keterampilan_id', $items->pluck('id'))
                ->avg('nilai');

            $hasil[] = [
                'kode' => $kd->kode_kd,
                'kompetensi' => $kd->kompetensi_dasar,
                'nilai' => is_null($nilai) ? '-' : round($nilai, 0),
            ];
        }

        return $hasil;
    }
}


