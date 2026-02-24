<?php

namespace App\Http\Controllers\Admin\K13;

use App\AnggotaKelas;
use App\Http\Controllers\Controller;
use App\K13NilaiKisi;
use App\K13RencanaKisi;
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

        return view('admin.k13.raportkisi.index', compact(
            'title',
            'kelas',
            'data_kelas',
            'data_anggota_kelas',
            'paper_size',
            'orientation'
        ));
    }

    public function show(Request $request, $id)
    {
        $sekolah = Sekolah::first();
        $anggota_kelas = AnggotaKelas::with(['kelas.tapel', 'kelas.guru'])->findOrFail($id);

        $cek_tanggal_raport = K13TglRaport::where('tapel_id', session()->get('tapel_id'))->first();
        if (is_null($cek_tanggal_raport)) {
            return back()->with('toast_warning', 'Tanggal raport belum disetting oleh admin');
        }

        // Ambil semua pembelajaran di kelas ini, urutkan berdasarkan mapping mapel
        $pembelajaran_list = Pembelajaran::with(['mapel.k13_mapping_mapel'])
            ->where('kelas_id', $anggota_kelas->kelas_id)
            ->get()
            ->sortBy(function ($item) {
                $kelompok = optional($item->mapel->k13_mapping_mapel)->kelompok ?? 'Z';
                $urutan = optional($item->mapel->k13_mapping_mapel)->nomor_urut ?? 99;
                return $kelompok . str_pad($urutan, 2, '0', STR_PAD_LEFT);
            });

        $data_mapel = [];

        foreach ($pembelajaran_list as $pembelajaran) {
            $detail_kisi = $this->getDetailKisi($pembelajaran, $anggota_kelas->id);

            // Skip mapel yang tidak punya rencana kisi-kisi
            if (empty($detail_kisi)) {
                continue;
            }

            $data_mapel[] = [
                'mapel' => $pembelajaran->mapel->nama_mapel,
                'detail' => $detail_kisi,
            ];
        }

        $pdf = PDF::loadview('walikelas.k13.raportkisi.raport', [
            'title' => 'Raport Kisi-Kisi',
            'sekolah' => $sekolah,
            'anggota_kelas' => $anggota_kelas,
            'data_mapel' => $data_mapel,
            'tanggal_raport' => $cek_tanggal_raport,
        ])->setPaper($request->paper_size, $request->orientation);

        $filename = 'RAPORT KISI-KISI ' . ($anggota_kelas->siswa->nama_lengkap ?? 'SISWA')
            . ' (' . ($anggota_kelas->kelas->nama_kelas ?? '') . ').pdf';

        return $pdf->stream($filename);
    }

    /**
     * Ambil detail kisi-kisi per siswa per pembelajaran (dari tabel baru)
     */
    private function getDetailKisi($pembelajaran, $anggota_kelas_id)
    {
        $rencana_list = K13RencanaKisi::where('pembelajaran_id', $pembelajaran->id)
            ->orderBy('urutan', 'ASC')
            ->get();

        if ($rencana_list->isEmpty()) {
            return [];
        }

        $hasil = [];
        foreach ($rencana_list as $rencana) {
            $nilai_obj = K13NilaiKisi::where('k13_rencana_kisi_id', $rencana->id)
                ->where('anggota_kelas_id', $anggota_kelas_id)
                ->first();

            $hasil[] = [
                'deskripsi' => $rencana->deskripsi_penilaian,
                'nilai' => $nilai_obj ? $nilai_obj->nilai : '-',
            ];
        }

        return $hasil;
    }
}
