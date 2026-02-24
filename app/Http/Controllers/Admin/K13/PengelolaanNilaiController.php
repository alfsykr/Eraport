<?php

namespace App\Http\Controllers\Admin\K13;

use App\AnggotaKelas;
use App\Http\Controllers\Controller;
use App\K13MappingMapel;
use App\K13NilaiAkhirRaport;
use App\K13RencanaKisi;
use App\K13NilaiKisi;
use App\Kelas;
use App\Mapel;
use App\Pembelajaran;
use App\Sekolah;
use App\Tapel;
use Illuminate\Http\Request;

class PengelolaanNilaiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Hasil Pengelolaan Nilai';
        $data_kelas = Kelas::where('tapel_id', session()->get('tapel_id'))->get();
        return view('admin.k13.pengelolaannilai.pilihkelas', compact('title', 'data_kelas'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $title = 'Hasil Pengelolaan Nilai';
        $tapel = Tapel::findorfail(session()->get('tapel_id'));
        $data_kelas = Kelas::where('tapel_id', $tapel->id)->get();

        $kelas = Kelas::findorfail($request->kelas_id);

        // Ambil semua pembelajaran aktif di kelas ini
        $data_pembelajaran = Pembelajaran::where('kelas_id', $kelas->id)
            ->whereNotNull('guru_id')
            ->where('status', 1)
            ->with(['mapel', 'guru'])
            ->get();

        // Hitung jumlah anggota kelas (untuk cek nilai)
        $jumlah_anggota = AnggotaKelas::where('kelas_id', $kelas->id)->count();

        foreach ($data_pembelajaran as $pembelajaran) {
            // Status Rencana Kisi-Kisi
            $jumlah_rencana = K13RencanaKisi::where('pembelajaran_id', $pembelajaran->id)->count();
            $pembelajaran->jumlah_rencana_kisi = $jumlah_rencana;

            // Status Input Nilai Kisi-Kisi (cek apakah ada nilai yang sudah diinput)
            $jumlah_nilai = K13NilaiKisi::whereHas('rencana_kisi', function ($q) use ($pembelajaran) {
                $q->where('pembelajaran_id', $pembelajaran->id);
            })->count();
            $pembelajaran->jumlah_nilai_kisi = $jumlah_nilai;

            // Status Kirim Nilai Akhir (cek apakah nilai_akhir sudah terisi)
            $jumlah_kirim = K13NilaiAkhirRaport::where('pembelajaran_id', $pembelajaran->id)
                ->whereNotNull('nilai_akhir')
                ->count();
            $pembelajaran->jumlah_kirim_nilai = $jumlah_kirim;
            $pembelajaran->jumlah_anggota = $jumlah_anggota;
        }

        return view('admin.k13.pengelolaannilai.index', compact(
            'title',
            'kelas',
            'data_kelas',
            'data_pembelajaran'
        ));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
