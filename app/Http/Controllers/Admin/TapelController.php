<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Kelas;
use App\Siswa;
use App\Tapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class TapelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Data Tahun Pelajaran';
        $data_tapel = Tapel::orderBy('id', 'DESC')->get();
        return view('admin.tapel.index', compact('title', 'data_tapel'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun_pelajaran' => 'required|min:9|max:9',
            'semester' => 'required',
        ]);
        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        } else {
            $tapel = new Tapel([
                'tahun_pelajaran' => $request->tahun_pelajaran,
                'semester' => $request->semester,
            ]);
            $tapel->save();
            // ✅ FIX: Tidak lagi reset kelas_id siswa saat tambah tahun baru
            // Data histori kelas siswa tetap terjaga di tabel anggota_kelas
            return back()->with('toast_success', 'Tahun Pelajaran berhasil ditambahkan');
        }
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
        $validator = Validator::make($request->all(), [
            'tahun_pelajaran' => 'required|min:9|max:9',
            'semester' => 'required',
        ]);
        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        } else {
            $tapel = Tapel::findorfail($id);
            $data_tapel = [
                'tahun_pelajaran' => $request->tahun_pelajaran,
                'semester' => $request->semester,
            ];
            $tapel->update($data_tapel);
            return back()->with('toast_success', 'Tahun Pelajaran berhasil diedit');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tapel = Tapel::findorfail($id);

        // Check if this tapel is still being used by any kelas
        $kelas_count = Kelas::where('tapel_id', $id)->count();

        if ($kelas_count > 0) {
            return back()->with(
                'toast_warning',
                'Tahun Pelajaran tidak dapat dihapus karena masih digunakan oleh ' .
                $kelas_count . ' kelas. Hapus kelas terlebih dahulu.'
            );
        }

        // Check if this is the active tapel in session
        if (session()->get('tapel_id') == $id) {
            return back()->with(
                'toast_warning',
                'Tahun Pelajaran aktif tidak dapat dihapus. ' .
                'Silakan ganti tahun pelajaran aktif terlebih dahulu'
            );
        }

        try {
            $tapel->delete();
            return back()->with('toast_success', 'Tahun Pelajaran berhasil dihapus');
        } catch (\Throwable $th) {
            return back()->with('toast_error', 'Tahun Pelajaran tidak dapat dihapus: ' . $th->getMessage());
        }
    }
}
