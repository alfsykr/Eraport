<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\PersonalProgram;
use App\Kelas;
use App\AnggotaKelas;

class PersonalProgramController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Cetak Raport Personal Program';
        $kelas_id = $request->input('kelas_id');
        $data_kelas = Kelas::orderBy('tingkatan_kelas', 'ASC')->get();
        $data_anggota_kelas = collect();

        if ($kelas_id) {
            $data_anggota_kelas = AnggotaKelas::with(['siswa'])
                ->where('kelas_id', $kelas_id)
                ->get();
        }

        return view('admin.personal_program.index', compact('title', 'data_kelas', 'data_anggota_kelas', 'kelas_id'));
    }

    public function show($anggota_kelas_id, Request $request)
    {
        $paper_size = $request->get('paper_size', 'A4');
        $orientation = $request->get('orientation', 'potrait');
        $semester = $request->get('semester', 'Ganjil');

        $anggota = \App\AnggotaKelas::with(['siswa', 'kelas.tapel'])->findOrFail($anggota_kelas_id);
        $siswa = $anggota->siswa;

        // Cari PP berdasarkan siswa_id dan semester
        $pp = PersonalProgram::with('guru')
            ->where('siswa_id', $siswa->id)
            ->where('semester', $semester)
            ->latest('updated_at')
            ->first();

        // Fallback: jika tidak ditemukan dengan semester ini, ambil PP paling baru
        if (!$pp) {
            $pp = PersonalProgram::with('guru')
                ->where('siswa_id', $siswa->id)
                ->latest('updated_at')
                ->first();
        }

        $title = 'Raport Personal Program';

        $pdf = \PDF::loadview('admin.personal_program.raport', [
            'title' => $title,
            'paper_size' => $paper_size,
            'orientation' => $orientation,
            'anggota' => $anggota,
            'siswa' => $siswa,
            'pp' => $pp,
            'semester' => $semester,
        ])->setPaper($paper_size, $orientation);

        $filename = 'RAPORT PERSONAL PROGRAM ' . ($siswa->nama_lengkap ?? 'SISWA') . ' (' . ($anggota->kelas->nama_kelas ?? '') . ').pdf';
        return $pdf->stream($filename);
    }
}


