<?php

namespace App\Http\Controllers\WaliKelas;

use App\AnggotaKelas;
use App\Guru;
use App\Http\Controllers\Controller;
use App\Kelas;
use App\TalentsMapping;
use App\Tapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TalentsMappingController extends Controller
{
    public function index()
    {
        $title = 'Talents Mapping';
        $tapel = Tapel::findorfail(session()->get('tapel_id'));
        $guru = Guru::where('user_id', Auth::user()->id)->first();
        $id_kelas_diampu = Kelas::where('tapel_id', $tapel->id)->where('guru_id', $guru->id)->get('id');
        $id_anggota_kelas = AnggotaKelas::whereIn('kelas_id', $id_kelas_diampu)->get('id');

        $data_talents = TalentsMapping::whereIn('anggota_kelas_id', $id_anggota_kelas)->get();
        $data_anggota_kelas = AnggotaKelas::whereIn('kelas_id', $id_kelas_diampu)->get();

        // Daftar nama talent: default + yang sudah pernah dipakai
        $default_talents = [
            'Competition',
            'Responsibility',
            'Achiever',
            'Activator',
            'Adaptability',
            'Communication',
            'Connectedness',
            'Consistency',
            'Developer',
            'Discipline',
            'Empathy',
            'Focus',
            'Futuristic',
            'Harmony',
            'Ideation',
            'Includer',
            'Individualization',
            'Input',
            'Intellection',
            'Learner',
            'Maximizer',
            'Positivity',
            'Relator',
            'Significance',
            'Strategic',
            'Woo',
        ];
        $used_talents = TalentsMapping::whereIn('anggota_kelas_id', $id_anggota_kelas)
            ->whereNotNull('nama_talents')
            ->pluck('nama_talents')
            ->filter()
            ->unique()
            ->values()
            ->toArray();
        $talent_names = array_unique(array_merge($default_talents, $used_talents));
        sort($talent_names);

        return view('walikelas.prestasi.index', compact('title', 'data_talents', 'data_anggota_kelas', 'talent_names'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'anggota_kelas_id' => 'required',
            'nama_talents' => 'required',
            'deskripsi_talents' => 'required|min:20|max:200',
        ]);
        if ($validator->fails()) {
            return back()->with('toast_error', $validator->errors()->first())->withInput();
        } else {
            $talents = new TalentsMapping([
                'anggota_kelas_id' => $request->anggota_kelas_id,
                'nama_talents' => $request->nama_talents,
                'deskripsi_talents' => $request->deskripsi_talents,
            ]);
            $talents->save();
            return back()->with('toast_success', 'Talents Mapping berhasil ditambahkan');
        }
    }

    public function destroy($id)
    {
        $talents = TalentsMapping::findorfail($id);
        try {
            $talents->delete();
            return back()->with('toast_success', 'Talents Mapping berhasil dihapus');
        } catch (\Throwable $th) {
            return back()->with('toast_error', 'Talents Mapping tidak dapat dihapus');
        }
    }
}
