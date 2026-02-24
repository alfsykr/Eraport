<?php

namespace App\Http\Controllers\Guru\K13;

use App\AnggotaKelas;
use App\Guru;
use App\Http\Controllers\Controller;
use App\K13NilaiKisi;
use App\K13RencanaKisi;
use App\Kelas;
use App\Pembelajaran;
use App\Tapel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NilaiKisiController extends Controller
{
    /**
     * Daftar mapel yang diajar + status nilai kisi-kisi
     */
    public function index()
    {
        $title = 'Input Nilai Kisi-kisi';
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
            $jumlah_rencana = K13RencanaKisi::where('pembelajaran_id', $pembelajaran->id)->count();
            $jumlah_siswa = AnggotaKelas::where('kelas_id', $pembelajaran->kelas_id)->count();
            $rencana_ids = K13RencanaKisi::where('pembelajaran_id', $pembelajaran->id)->pluck('id');

            // Hitung kisi-kisi yang sudah dinilai semua siswa (kolom Telah Dinilai lama)
            $telah_dinilai = 0;
            foreach ($rencana_ids as $rencana_id) {
                $count_nilai = K13NilaiKisi::where('k13_rencana_kisi_id', $rencana_id)->count();
                if ($count_nilai >= $jumlah_siswa && $jumlah_siswa > 0) {
                    $telah_dinilai++;
                }
            }

            // Hitung siswa yang SEMUA kisi-kisinya sudah terisi
            $siswa_lengkap = 0;
            if ($jumlah_rencana > 0 && $jumlah_siswa > 0) {
                $anggota_ids = AnggotaKelas::where('kelas_id', $pembelajaran->kelas_id)->pluck('id');
                foreach ($anggota_ids as $anggota_id) {
                    $jumlah_nilai_siswa = K13NilaiKisi::whereIn('k13_rencana_kisi_id', $rencana_ids)
                        ->where('anggota_kelas_id', $anggota_id)
                        ->count();
                    if ($jumlah_nilai_siswa >= $jumlah_rencana) {
                        $siswa_lengkap++;
                    }
                }
            }

            $pembelajaran->jumlah_rencana = $jumlah_rencana;
            $pembelajaran->jumlah_siswa = $jumlah_siswa;
            $pembelajaran->telah_dinilai = $telah_dinilai;
            $pembelajaran->siswa_lengkap = $siswa_lengkap;
        }

        return view('guru.k13.nilaikisi.index', compact('title', 'data_pembelajaran'));
    }

    /**
     * Form input nilai per KD per siswa
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pembelajaran_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return back()->with('toast_error', $validator->errors()->first());
        }

        $pembelajaran = Pembelajaran::findorfail($request->pembelajaran_id);
        $data_rencana = K13RencanaKisi::where('pembelajaran_id', $pembelajaran->id)
            ->orderBy('urutan', 'ASC')
            ->get();

        if ($data_rencana->isEmpty()) {
            return back()->with('toast_warning', 'Rencana penilaian belum dibuat. Silakan buat rencana penilaian terlebih dahulu.');
        }

        $data_anggota_kelas = AnggotaKelas::where('kelas_id', $pembelajaran->kelas_id)
            ->orderBy('id')
            ->get();

        // Ambil nilai yang sudah ada ke dalam array terpisah (bukan property model)
        // Format: $nilai_existing[anggota_id][rencana_id] = nilai
        $nilai_existing = [];
        foreach ($data_anggota_kelas as $anggota) {
            $nilai_existing[$anggota->id] = [];
            foreach ($data_rencana as $rencana) {
                $nilai = K13NilaiKisi::where('k13_rencana_kisi_id', $rencana->id)
                    ->where('anggota_kelas_id', $anggota->id)
                    ->value('nilai');
                $nilai_existing[$anggota->id][$rencana->id] = $nilai;
            }
        }

        $title = 'Input Nilai Kisi-kisi - ' . $pembelajaran->mapel->nama_mapel . ' ' . $pembelajaran->kelas->nama_kelas;
        return view('guru.k13.nilaikisi.create', compact(
            'title',
            'pembelajaran',
            'data_rencana',
            'data_anggota_kelas',
            'nilai_existing'
        ));
    }

    /**
     * Simpan / update nilai kisi-kisi
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pembelajaran_id' => 'required|integer',
            'nilai' => 'required|array',
        ]);
        if ($validator->fails()) {
            return back()->with('toast_error', $validator->errors()->first());
        }

        $pembelajaran = Pembelajaran::findorfail($request->pembelajaran_id);

        // nilai[rencana_id][anggota_kelas_id] = nilai
        foreach ($request->nilai as $rencana_id => $siswa_nilai) {
            foreach ($siswa_nilai as $anggota_kelas_id => $nilai) {
                // Jika nilai dikosongkan → hapus record yang ada
                if ($nilai === null || $nilai === '') {
                    K13NilaiKisi::where('k13_rencana_kisi_id', $rencana_id)
                        ->where('anggota_kelas_id', $anggota_kelas_id)
                        ->delete();
                    continue;
                }

                $nilai_int = (int) $nilai;
                if ($nilai_int < 0 || $nilai_int > 100) {
                    return back()->with('toast_error', 'Nilai harus antara 0 sampai 100');
                }

                K13NilaiKisi::updateOrCreate(
                    [
                        'k13_rencana_kisi_id' => $rencana_id,
                        'anggota_kelas_id' => $anggota_kelas_id,
                    ],
                    [
                        'nilai' => $nilai_int,
                        'updated_at' => Carbon::now(),
                    ]
                );
            }
        }

        return redirect()->route('nilaikisi.index')
            ->with('toast_success', 'Nilai kisi-kisi berhasil disimpan');
    }
}
