<?php

namespace App\Http\Controllers\Admin\K13;

use App\AnggotaKelas;
use App\Http\Controllers\Controller;
use App\K13DeskripsiNilaiSiswa;
use App\K13NilaiAkhirRaport;
use App\Kelas;
use App\Mapel;
use App\Pembelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NilaiRaportSemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Nilai Raport Semester';
        $data_mapel = Mapel::where('tapel_id', session()->get('tapel_id'))->get();
        $data_kelas = Kelas::where('tapel_id', session()->get('tapel_id'))->get();
        return view('admin.k13.nilairaport.pilihkelas', compact('title', 'data_mapel', 'data_kelas'));
    }

    /**
     * Store / display nilai form OR save nilai.
     */
    public function store(Request $request)
    {
        // Jika request adalah simpan nilai
        if ($request->has('simpan_nilai')) {
            return $this->simpanNilai($request);
        }

        // Validasi pilih kelas & mapel
        $validator = Validator::make($request->all(), [
            'mapel_id' => 'required',
            'kelas_id' => 'required',
        ]);
        if ($validator->fails()) {
            return back()->with('toast_error', $validator->errors()->first())->withInput();
        }

        $title = 'Nilai Raport Semester';
        $data_mapel = Mapel::where('tapel_id', session()->get('tapel_id'))->get();
        $data_kelas = Kelas::where('tapel_id', session()->get('tapel_id'))->get();

        $pembelajaran = Pembelajaran::where('mapel_id', $request->mapel_id)
            ->where('kelas_id', $request->kelas_id)->first();

        if (is_null($pembelajaran)) {
            return back()->with('toast_error', 'Data pembelajaran tidak ditemukan');
        }

        $kelas = Kelas::findorfail($request->kelas_id);
        $mapel = Mapel::findorfail($request->mapel_id);

        $data_anggota_kelas = AnggotaKelas::where('kelas_id', $kelas->id)->get();
        foreach ($data_anggota_kelas as $anggota_kelas) {
            $anggota_kelas->nilai_raport = K13NilaiAkhirRaport::where('pembelajaran_id', $pembelajaran->id)
                ->where('anggota_kelas_id', $anggota_kelas->id)->first();
        }

        return view('admin.k13.nilairaport.index', compact('title', 'mapel', 'kelas', 'data_mapel', 'data_kelas', 'data_anggota_kelas'));
    }

    /**
     * Simpan nilai_akhir per siswa
     */
    private function simpanNilai(Request $request)
    {
        $kelas_id = $request->kelas_id;
        $mapel_id = $request->mapel_id;

        $pembelajaran = Pembelajaran::where('mapel_id', $mapel_id)
            ->where('kelas_id', $kelas_id)->first();

        if (is_null($pembelajaran)) {
            return back()->with('toast_error', 'Data pembelajaran tidak ditemukan');
        }

        $mapel = Mapel::findorfail($mapel_id);
        $kkm = $mapel->kkm ?? 75;

        $data_anggota_kelas = AnggotaKelas::where('kelas_id', $kelas_id)->get();

        foreach ($data_anggota_kelas as $anggota_kelas) {
            $nilai_akhir = $request->input("nilai.{$anggota_kelas->id}");
            $deskripsi = $request->input("deskripsi.{$anggota_kelas->id}", '');

            if (!is_null($nilai_akhir) && $nilai_akhir !== '') {
                $nilai_akhir = (int) $nilai_akhir;

                // Hitung predikat berdasarkan KKM
                $range = (100 - $kkm) / 3;
                if ($nilai_akhir >= $kkm + ($range * 2)) {
                    $predikat = 'A';
                } elseif ($nilai_akhir >= $kkm + $range) {
                    $predikat = 'B';
                } elseif ($nilai_akhir >= $kkm) {
                    $predikat = 'C';
                } else {
                    $predikat = 'D';
                }

                // Simpan atau update nilai
                $nilai_record = K13NilaiAkhirRaport::updateOrCreate(
                    [
                        'pembelajaran_id' => $pembelajaran->id,
                        'anggota_kelas_id' => $anggota_kelas->id,
                    ],
                    [
                        'kkm' => $kkm,
                        'nilai_akhir' => $nilai_akhir,
                        'predikat_akhir' => $predikat,
                        // Set nilai lama ke nilai_akhir juga (backward compat)
                        'nilai_pengetahuan' => $nilai_akhir,
                        'predikat_pengetahuan' => $predikat,
                        'nilai_keterampilan' => $nilai_akhir,
                        'predikat_keterampilan' => $predikat,
                        'nilai_spiritual' => '3',
                        'nilai_sosial' => '3',
                    ]
                );

                // Simpan deskripsi jika ada
                if (!empty($deskripsi)) {
                    K13DeskripsiNilaiSiswa::updateOrCreate(
                        ['k13_nilai_akhir_raport_id' => $nilai_record->id],
                        ['deskripsi_pengetahuan' => $deskripsi, 'deskripsi_keterampilan' => $deskripsi]
                    );
                }
            }
        }

        return back()->with('toast_success', 'Nilai berhasil disimpan!');
    }
}
