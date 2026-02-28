<?php

namespace App\Http\Controllers\Guru\K13;

use App\Guru;
use App\Http\Controllers\Controller;
use App\K13NilaiAkhirRaport;
use App\K13NilaiKisi;
use App\K13RencanaKisi;
use App\Kelas;
use App\Pembelajaran;
use App\Tapel;
use Illuminate\Support\Facades\Auth;

class StatusPengirimanNilaiController extends Controller
{
    /**
     * Menampilkan status pengiriman nilai akhir per mata pelajaran yang diajarkan guru.
     */
    public function index()
    {
        $title = 'Status Pengiriman Nilai Akhir';
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
            // Jumlah kisi-kisi yang sudah dibuat
            $rencana_ids = K13RencanaKisi::where('pembelajaran_id', $pembelajaran->id)->pluck('id');
            $pembelajaran->jumlah_kisi = $rencana_ids->count();

            // Jumlah siswa di kelas ini
            $jumlah_siswa = $pembelajaran->kelas->anggota_kelas()->count();
            $pembelajaran->jumlah_siswa = $jumlah_siswa;

            // Jumlah nilai kisi yang sudah diinput
            $pembelajaran->jumlah_nilai_kisi = K13NilaiKisi::whereIn('k13_rencana_kisi_id', $rencana_ids)->count();

            // Jumlah nilai akhir yang sudah dikirim
            $sudah_kirim = K13NilaiAkhirRaport::where('pembelajaran_id', $pembelajaran->id)->count();
            $pembelajaran->sudah_kirim = $sudah_kirim;

            // Status
            if ($pembelajaran->jumlah_kisi == 0) {
                $pembelajaran->status_label = 'Belum Ada Kisi';
                $pembelajaran->status_class = 'badge-secondary';
            } elseif ($sudah_kirim >= $jumlah_siswa && $jumlah_siswa > 0) {
                $pembelajaran->status_label = 'Sudah Terkirim';
                $pembelajaran->status_class = 'badge-success';
            } elseif ($sudah_kirim > 0) {
                $pembelajaran->status_label = 'Sebagian Terkirim';
                $pembelajaran->status_class = 'badge-warning';
            } else {
                $pembelajaran->status_label = 'Belum Terkirim';
                $pembelajaran->status_class = 'badge-danger';
            }
        }

        return view('guru.k13.statuspengiriman.index', compact('title', 'data_pembelajaran'));
    }
}
