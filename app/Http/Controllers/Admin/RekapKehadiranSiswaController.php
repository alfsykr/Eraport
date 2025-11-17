<?php

namespace App\Http\Controllers\Admin;

use App\AnggotaKelas;
use App\Http\Controllers\Controller;
use App\KehadiranSiswa;
use App\Kelas;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RekapKehadiranSiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Rekap Kehadiran Siswa';
        $data_kelas = Kelas::where('tapel_id', session()->get('tapel_id'))->get();
        return view('admin.rekapkehadiran.pilihkelas', compact('title', 'data_kelas'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $title = 'Rekap Kehadiran Siswa';
        $data_kelas = Kelas::where('tapel_id', session()->get('tapel_id'))->get();
        $kelas = Kelas::findorfail($request->kelas_id);
        $data_anggota_kelas = AnggotaKelas::where('kelas_id', $kelas->id)->get();
        return view('admin.rekapkehadiran.index', compact('title', 'kelas', 'data_kelas', 'data_anggota_kelas'));
    }

    public function update(Request $request)
    {
        if (is_null($request->anggota_kelas_id)) {
            return back()->with('toast_error', 'Data siswa tidak ditemukan');
        }

        for ($index = 0; $index < count($request->anggota_kelas_id); $index++) {
            $data = [
                'anggota_kelas_id' => $request->anggota_kelas_id[$index],
                'sakit' => $request->sakit[$index],
                'izin' => $request->izin[$index],
                'tanpa_keterangan' => $request->tanpa_keterangan[$index],
                'updated_at' => Carbon::now(),
            ];

            $kehadiran = KehadiranSiswa::where('anggota_kelas_id', $request->anggota_kelas_id[$index])->first();

            if (is_null($kehadiran)) {
                $data['created_at'] = Carbon::now();
                KehadiranSiswa::insert($data);
            } else {
                $kehadiran->update($data);
            }
        }

        $title = 'Rekap Kehadiran Siswa';
        $data_kelas = Kelas::where('tapel_id', session()->get('tapel_id'))->get();
        $kelas = Kelas::findorfail($request->kelas_id);
        $data_anggota_kelas = AnggotaKelas::where('kelas_id', $kelas->id)->get();

        session()->flash('toast_success', 'Data kehadiran berhasil diperbarui');

        return view('admin.rekapkehadiran.index', compact('title', 'kelas', 'data_kelas', 'data_anggota_kelas'));
    }

}
