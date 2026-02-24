<?php

namespace App\Http\Controllers\Guru\K13;

use App\Guru;
use App\Http\Controllers\Controller;
use App\K13KdMapel;
use App\K13NilaiKisi;
use App\K13RencanaKisi;
use App\K13RencanaNilaiKeterampilan;
use App\K13RencanaNilaiPengetahuan;
use App\Kelas;
use App\Pembelajaran;
use App\Tapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RencanaKisiController extends Controller
{
    /**
     * Daftar mapel yang diajar guru + jumlah rencana kisi-kisi
     */
    public function index()
    {
        $title = 'Rencana Penilaian';
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

            // Cek apakah ada data lama yang bisa diimport
            $ada_pengetahuan = K13RencanaNilaiPengetahuan::where('pembelajaran_id', $pembelajaran->id)->exists();
            $ada_keterampilan = K13RencanaNilaiKeterampilan::where('pembelajaran_id', $pembelajaran->id)->exists();
            $pembelajaran->bisa_import = ($ada_pengetahuan || $ada_keterampilan) && $pembelajaran->jumlah_rencana == 0;
        }

        return view('guru.k13.rencanakisi.index', compact('title', 'data_pembelajaran'));
    }

    /**
     * Form tambah rencana kisi-kisi untuk satu pembelajaran
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

        // KD yang tersedia untuk mapel ini
        $data_kd = K13KdMapel::where('mapel_id', $pembelajaran->mapel_id)->get();

        $title = 'Rencana Penilaian - ' . $pembelajaran->mapel->nama_mapel . ' ' . $pembelajaran->kelas->nama_kelas;
        return view('guru.k13.rencanakisi.create', compact('title', 'pembelajaran', 'data_rencana', 'data_kd'));
    }

    /**
     * Simpan rencana kisi-kisi baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pembelajaran_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return back()->with('toast_error', $validator->errors()->first())->withInput();
        }

        $pembelajaran = Pembelajaran::findorfail($request->pembelajaran_id);
        $mode = $request->input('mode', 'baru');

        // Tentukan deskripsi
        if ($mode === 'pilih' && $request->k13_kd_mapel_id) {
            // Mode pilih: ambil deskripsi dari KD jika textarea kosong
            $deskripsi = $request->deskripsi_penilaian;
            if (empty(trim($deskripsi))) {
                $kd = K13KdMapel::find($request->k13_kd_mapel_id);
                $deskripsi = $kd ? $kd->kompetensi_dasar : null;
            }
        } else {
            $deskripsi = $request->deskripsi_penilaian;
        }

        if (empty(trim($deskripsi ?? ''))) {
            return back()->with('toast_error', 'Deskripsi kisi-kisi tidak boleh kosong')->withInput();
        }

        // Cek duplikat deskripsi dalam pembelajaran yang sama
        $sudah_ada = K13RencanaKisi::where('pembelajaran_id', $pembelajaran->id)
            ->whereRaw('LOWER(TRIM(deskripsi_penilaian)) = ?', [strtolower(trim($deskripsi))])
            ->exists();
        if ($sudah_ada) {
            return back()->with('toast_error', 'Kisi-kisi dengan deskripsi yang sama sudah ada. Silakan pilih atau buat kisi-kisi yang berbeda.')->withInput();
        }

        // Urutan otomatis
        $urutan = K13RencanaKisi::where('pembelajaran_id', $pembelajaran->id)->max('urutan') + 1;

        K13RencanaKisi::create([
            'pembelajaran_id' => $pembelajaran->id,
            'k13_kd_mapel_id' => $request->k13_kd_mapel_id ?: null,
            'deskripsi_penilaian' => $deskripsi,
            'urutan' => $urutan,
        ]);

        return redirect()->route('rencanakisi.create', ['pembelajaran_id' => $pembelajaran->id])
            ->with('toast_success', 'Rencana penilaian berhasil ditambahkan');
    }

    /**
     * Import otomatis dari rencana pengetahuan & keterampilan lama
     */
    public function importLama(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pembelajaran_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return back()->with('toast_error', $validator->errors()->first());
        }

        $pembelajaran = Pembelajaran::findorfail($request->pembelajaran_id);

        // Cek sudah ada rencana baru?
        $sudah_ada = K13RencanaKisi::where('pembelajaran_id', $pembelajaran->id)->exists();
        if ($sudah_ada) {
            return back()->with('toast_error', 'Sudah ada rencana penilaian. Hapus semua terlebih dahulu sebelum import.');
        }

        $urutan = 1;
        $imported = 0;

        // Import dari rencana pengetahuan (groupBy KD, ambil 1 per KD)
        $rencana_pengetahuan = K13RencanaNilaiPengetahuan::with('k13_kd_mapel')
            ->where('pembelajaran_id', $pembelajaran->id)
            ->get()
            ->unique('k13_kd_mapel_id');

        foreach ($rencana_pengetahuan as $rencana) {
            if (is_null($rencana->k13_kd_mapel))
                continue;

            K13RencanaKisi::create([
                'pembelajaran_id' => $pembelajaran->id,
                'k13_kd_mapel_id' => $rencana->k13_kd_mapel_id,
                'deskripsi_penilaian' => $rencana->k13_kd_mapel->kompetensi_dasar,
                'urutan' => $urutan++,
            ]);
            $imported++;
        }

        // Import dari rencana keterampilan (groupBy KD, ambil 1 per KD, skip yang sudah ada)
        $kd_sudah = K13RencanaKisi::where('pembelajaran_id', $pembelajaran->id)
            ->pluck('k13_kd_mapel_id')
            ->filter()
            ->toArray();

        $rencana_keterampilan = K13RencanaNilaiKeterampilan::with('k13_kd_mapel')
            ->where('pembelajaran_id', $pembelajaran->id)
            ->get()
            ->unique('k13_kd_mapel_id');

        foreach ($rencana_keterampilan as $rencana) {
            if (is_null($rencana->k13_kd_mapel))
                continue;
            if (in_array($rencana->k13_kd_mapel_id, $kd_sudah))
                continue; // skip duplikat

            K13RencanaKisi::create([
                'pembelajaran_id' => $pembelajaran->id,
                'k13_kd_mapel_id' => $rencana->k13_kd_mapel_id,
                'deskripsi_penilaian' => $rencana->k13_kd_mapel->kompetensi_dasar,
                'urutan' => $urutan++,
            ]);
            $imported++;
        }

        if ($imported === 0) {
            return back()->with('toast_warning', 'Tidak ada data lama yang bisa diimport');
        }

        return redirect()->route('rencanakisi.create', ['pembelajaran_id' => $pembelajaran->id])
            ->with('toast_success', $imported . ' indikator berhasil diimport dari data KD lama');
    }

    /**
     * Hapus rencana kisi-kisi
     */
    public function destroy($id)
    {
        $rencana = K13RencanaKisi::findorfail($id);
        $pembelajaran_id = $rencana->pembelajaran_id;

        // Cek apakah sudah ada nilai yang diinput
        $ada_nilai = K13NilaiKisi::where('k13_rencana_kisi_id', $rencana->id)->exists();
        if ($ada_nilai) {
            return back()->with('toast_error', 'Tidak bisa dihapus, sudah ada nilai yang diinput');
        }

        $rencana->delete();

        // Reorder urutan
        $rencana_list = K13RencanaKisi::where('pembelajaran_id', $pembelajaran_id)
            ->orderBy('urutan', 'ASC')->get();
        foreach ($rencana_list as $i => $r) {
            $r->update(['urutan' => $i + 1]);
        }

        return back()->with('toast_success', 'Rencana penilaian berhasil dihapus');
    }
}
