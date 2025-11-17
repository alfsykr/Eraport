<?php

namespace App\Http\Controllers\WaliKelas;

use App\AnggotaKelas;
use App\Guru;
use App\Http\Controllers\Controller;
use App\Kelas;
use App\Tapel;
use App\TerapiPerkembangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;

class TerapiPerkembanganController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Perkembangan Terapi (Mingguan)';
        $tapel = Tapel::findorfail(session()->get('tapel_id'));
        $guru = Guru::where('user_id', Auth::user()->id)->first();

        // wali kelas hanya untuk kelas yang dibinanya
        $kelas = Kelas::where('tapel_id', $tapel->id)->where('guru_id', $guru->id)->first();
        if (is_null($kelas)) {
            return view('walikelas.terapi.index', compact('title'));
        }

        $data_anggota_kelas = AnggotaKelas::where('kelas_id', $kelas->id)->get();
        $tanggal = $request->get('minggu_tanggal');
        if (empty($tanggal)) {
            $tanggal = now()->startOfWeek()->toDateString();
        }

        // join nilai yang sudah ada untuk tanggal tersebut
        foreach ($data_anggota_kelas as $anggota) {
            $progress = TerapiPerkembangan::where('anggota_kelas_id', $anggota->id)
                ->where('minggu_tanggal', $tanggal)
                ->first();
            $anggota->tp_motorik_kasar = optional($progress)->motorik_kasar;
            $anggota->tp_sosialisasi = optional($progress)->sosialisasi;
            $anggota->tp_rentang_akademis = optional($progress)->rentang_akademis;
            $anggota->tp_eval_sosialisasi = optional($progress)->evaluasi_sosialisasi;
            $anggota->tp_eval_rentang_akademis = optional($progress)->evaluasi_rentang_akademis;
        }

        return view('walikelas.terapi.index', compact('title', 'kelas', 'data_anggota_kelas', 'tanggal'));
    }

    public function store(Request $request)
    {
        $tanggal = $request->minggu_tanggal;
        for ($i = 0; $i < count($request->anggota_kelas_id); $i++) {
            $payload = [
                'motorik_kasar' => $request->motorik_kasar[$i] ?? null,
                'sosialisasi' => $request->sosialisasi[$i] ?? null,
                'rentang_akademis' => $request->rentang_akademis[$i] ?? null,
                'evaluasi_sosialisasi' => $request->evaluasi_sosialisasi[$i] ?? null,
                'evaluasi_rentang_akademis' => $request->evaluasi_rentang_akademis[$i] ?? null,
            ];

            $row = TerapiPerkembangan::firstOrNew([
                'anggota_kelas_id' => $request->anggota_kelas_id[$i],
                'minggu_tanggal' => $tanggal,
            ]);
            $row->fill($payload);
            $row->save();
        }

        return back()->with('toast_success', 'Perkembangan terapi minggu ini tersimpan');
    }

    public function show(Request $request, $anggota_kelas_id)
    {
        $title = 'Raport Perkembangan Terapi';
        $tanggal = $request->get('minggu_tanggal');
        if (empty($tanggal)) {
            $tanggal = now()->startOfWeek()->toDateString();
        }

        $anggota_kelas = AnggotaKelas::findorfail($anggota_kelas_id);
        $progress = TerapiPerkembangan::where('anggota_kelas_id', $anggota_kelas_id)
            ->where('minggu_tanggal', $tanggal)
            ->first();

        $pdf = PDF::loadView('walikelas.terapi.raport', compact('title', 'anggota_kelas', 'progress', 'tanggal'));
        return $pdf->stream('RAPORT TERAPI ' . $anggota_kelas->siswa->nama_lengkap . '.pdf');
    }
}







