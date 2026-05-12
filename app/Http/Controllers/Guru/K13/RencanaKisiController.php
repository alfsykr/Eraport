<?php

namespace App\Http\Controllers\Guru\K13;

use App\Guru;
use App\Http\Controllers\Controller;
use App\K13NilaiKisi;
use App\K13RencanaKisi;
// K13RencanaNilaiKeterampilan removed - model not available
// K13RencanaNilaiPengetahuan removed - model not available
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

            // Fitur import dari data lama dinonaktifkan - model tidak tersedia
            $pembelajaran->bisa_import = false;
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

        // Ambil indikator kisi-kisi dari pembelajaran lain dengan mapel yang sama
        // sebagai pilihan yang bisa langsung dipilih guru
        $pembelajaran_sama = Pembelajaran::where('mapel_id', $pembelajaran->mapel_id)
            ->where('id', '!=', $pembelajaran->id)
            ->pluck('id');

        $data_kd = K13RencanaKisi::whereIn('pembelajaran_id', $pembelajaran_sama)
            ->orderBy('deskripsi_penilaian', 'ASC')
            ->get()
            ->unique('deskripsi_penilaian')
            ->values();

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
            // Mode pilih: ambil deskripsi dari K13RencanaKisi yang dipilih
            $sumber = K13RencanaKisi::find($request->k13_kd_mapel_id);
            $deskripsi = $sumber ? $sumber->deskripsi_penilaian : $request->deskripsi_penilaian;
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

        return back()->with('toast_warning', 'Fitur import dari data lama tidak tersedia.');
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
