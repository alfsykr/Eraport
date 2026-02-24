<?php

namespace App\Http\Controllers\Guru\K13;

use App\AnggotaKelas;
use App\Guru;
use App\Http\Controllers\Controller;
use App\K13KkmMapel;
use App\K13NilaiAkhirRaport;
use App\K13NilaiKisi;
use App\K13RencanaKisi;
use App\Kelas;
use App\Pembelajaran;
use App\Tapel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KirimNilaiAkhirController extends Controller
{
    /**
     * Daftar mapel yang diajar guru
     */
    public function index()
    {
        $title = 'Kirim Nilai Akhir';
        $tapel = Tapel::findorfail(session()->get('tapel_id'));
        $guru = Guru::where('user_id', Auth::user()->id)->first();
        $id_kelas = Kelas::where('tapel_id', $tapel->id)->get('id');

        $data_pembelajaran = Pembelajaran::where('guru_id', $guru->id)
            ->whereIn('kelas_id', $id_kelas)
            ->where('status', 1)
            ->orderBy('mapel_id', 'ASC')
            ->orderBy('kelas_id', 'ASC')
            ->get();

        foreach ($data_pembelajaran as $pembelajaran) {
            $pembelajaran->jumlah_rencana = K13RencanaKisi::where('pembelajaran_id', $pembelajaran->id)->count();
            $pembelajaran->sudah_kirim = K13NilaiAkhirRaport::where('pembelajaran_id', $pembelajaran->id)->count();
        }

        return view('guru.k13.kirimnilaiakhir.index', compact('title', 'data_pembelajaran'));
    }

    /**
     * Preview nilai akhir yang akan dikirim (auto-hitung dari rata-rata kisi-kisi)
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pembelajaran_id' => 'required',
        ]);
        if ($validator->fails()) {
            return back()->with('toast_error', $validator->errors()->first())->withInput();
        }

        $pembelajaran = Pembelajaran::findorfail($request->pembelajaran_id);

        // Cek KKM
        $kkm = K13KkmMapel::where('mapel_id', $pembelajaran->mapel_id)
            ->where('kelas_id', $pembelajaran->kelas_id)
            ->first();
        if (is_null($kkm)) {
            return back()->with('toast_warning', 'KKM mata pelajaran belum ditentukan');
        }

        // Cek rencana kisi-kisi
        $rencana_ids = K13RencanaKisi::where('pembelajaran_id', $pembelajaran->id)->pluck('id');
        if ($rencana_ids->isEmpty()) {
            return back()->with('toast_warning', 'Rencana penilaian kisi-kisi belum dibuat');
        }

        // Interval predikat
        $range = (100 - $kkm->kkm) / 3;
        $kkm->predikat_c = round($kkm->kkm, 0);
        $kkm->predikat_b = round($kkm->kkm + $range, 0);
        $kkm->predikat_a = round($kkm->kkm + ($range * 2), 0);

        // Data siswa + hitung nilai akhir dari rata-rata kisi-kisi
        $data_anggota_kelas = AnggotaKelas::where('kelas_id', $pembelajaran->kelas_id)->get();
        foreach ($data_anggota_kelas as $anggota) {
            $rata = K13NilaiKisi::whereIn('k13_rencana_kisi_id', $rencana_ids)
                ->where('anggota_kelas_id', $anggota->id)
                ->avg('nilai');

            $anggota->nilai_akhir = $rata ? round($rata, 0) : 0;
            $anggota->predikat = $this->hitungPredikat($anggota->nilai_akhir, $kkm);
        }

        // Data guru untuk dropdown
        $guru = Guru::where('user_id', Auth::user()->id)->first();
        $id_kelas = Kelas::where('tapel_id', session()->get('tapel_id'))->get('id');
        $data_pembelajaran = Pembelajaran::where('guru_id', $guru->id)
            ->whereIn('kelas_id', $id_kelas)
            ->where('status', 1)
            ->orderBy('mapel_id', 'ASC')
            ->orderBy('kelas_id', 'ASC')
            ->get();

        $title = 'Kirim Nilai Akhir';
        return view('guru.k13.kirimnilaiakhir.create', compact(
            'title',
            'data_pembelajaran',
            'pembelajaran',
            'kkm',
            'data_anggota_kelas'
        ));
    }

    /**
     * Simpan nilai akhir ke k13_nilai_akhir_raport
     */
    public function store(Request $request)
    {
        $pembelajaran = Pembelajaran::findorfail($request->pembelajaran_id);
        $kkm = K13KkmMapel::where('mapel_id', $pembelajaran->mapel_id)
            ->where('kelas_id', $pembelajaran->kelas_id)
            ->first();

        for ($i = 0; $i < count($request->anggota_kelas_id); $i++) {
            $nilai_akhir = (int) $request->nilai_akhir[$i];
            $predikat = $this->hitungPredikat($nilai_akhir, $kkm);

            $data = [
                'pembelajaran_id' => $request->pembelajaran_id,
                'anggota_kelas_id' => $request->anggota_kelas_id[$i],
                'kkm' => $kkm->kkm,
                'nilai_akhir' => $nilai_akhir,
                'predikat_akhir' => $predikat,
                // backward compat
                'nilai_pengetahuan' => $nilai_akhir,
                'predikat_pengetahuan' => $predikat,
                'nilai_keterampilan' => $nilai_akhir,
                'predikat_keterampilan' => $predikat,
                'nilai_spiritual' => 3,
                'nilai_sosial' => 3,
                'updated_at' => Carbon::now(),
            ];

            $cek = K13NilaiAkhirRaport::where('pembelajaran_id', $request->pembelajaran_id)
                ->where('anggota_kelas_id', $request->anggota_kelas_id[$i])
                ->first();

            if (is_null($cek)) {
                $data['created_at'] = Carbon::now();
                K13NilaiAkhirRaport::insert($data);
            } else {
                $cek->update($data);
            }
        }

        return redirect('guru/kirimnilaiakhir')->with('toast_success', 'Nilai akhir raport berhasil dikirim');
    }

    /**
     * Hitung predikat berdasarkan nilai dan KKM
     */
    private function hitungPredikat($nilai, $kkm)
    {
        $range = (100 - $kkm->kkm) / 3;
        if ($nilai >= round($kkm->kkm + ($range * 2), 0))
            return 'A';
        if ($nilai >= round($kkm->kkm + $range, 0))
            return 'B';
        if ($nilai >= $kkm->kkm)
            return 'C';
        return 'D';
    }
}
