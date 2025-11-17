<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\PersonalProgram;
use App\Siswa;
use App\Guru;
use Illuminate\Support\Facades\Auth;

class PersonalProgramController extends Controller
{
    public function index()
    {
        $title = 'Personal Program';
        $guru = Guru::where('user_id', Auth::id())->firstOrFail();
        $data_pp = PersonalProgram::with(['siswa', 'guru'])
            ->where('guru_id', $guru->id)
            ->orderBy('updated_at', 'DESC')
            ->get();
        $data_siswa = Siswa::orderBy('nama_lengkap', 'ASC')->get();

        return view('guru.personal_program.index', compact('title', 'data_pp', 'data_siswa'));
    }

    public function create(Request $request)
    {
        return redirect()->route('personalprogram.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|integer',
            'semester' => 'required|string|max:15',
        ]);

        $guru = Guru::where('user_id', Auth::id())->firstOrFail();

        PersonalProgram::create([
            'siswa_id' => $request->siswa_id,
            'semester' => $request->semester,
            'motorik_kasar' => $request->motorik_kasar,
            'sosialisasi' => $request->sosialisasi,
            'rentang_akademis' => $request->rentang_akademis,
            'evaluasi_motorik_kasar' => $request->evaluasi_motorik_kasar,
            'evaluasi_sosialisasi' => $request->evaluasi_sosialisasi,
            'evaluasi_rentang_akademis' => $request->evaluasi_rentang_akademis,
            'guru_id' => $guru->id,
        ]);

        return redirect()->route('personalprogram.index')->with('toast_success', 'Personal Program disimpan.');
    }

    public function edit(PersonalProgram $personalprogram)
    {
        $title = 'Edit Personal Program';
        $data_siswa = Siswa::orderBy('nama_lengkap', 'ASC')->get();
        return view('guru.personal_program.edit', compact('title', 'personalprogram', 'data_siswa'));
    }

    public function update(Request $request, PersonalProgram $personalprogram)
    {
        $request->validate([
            'siswa_id' => 'required|integer',
            'semester' => 'required|string|max:15',
        ]);

        $personalprogram->update([
            'siswa_id' => $request->siswa_id,
            'semester' => $request->semester,
            'motorik_kasar' => $request->motorik_kasar,
            'sosialisasi' => $request->sosialisasi,
            'rentang_akademis' => $request->rentang_akademis,
            'evaluasi_motorik_kasar' => $request->evaluasi_motorik_kasar,
            'evaluasi_sosialisasi' => $request->evaluasi_sosialisasi,
            'evaluasi_rentang_akademis' => $request->evaluasi_rentang_akademis,
        ]);

        return redirect()->route('personalprogram.index')->with('toast_success', 'Personal Program diupdate.');
    }

    public function destroy(PersonalProgram $personalprogram)
    {
        $personalprogram->delete();
        return redirect()->route('personalprogram.index')->with('toast_success', 'Personal Program dihapus.');
    }
}


