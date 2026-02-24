<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\MapelImport;
use App\Mapel;
use App\Tapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Excel;
use Illuminate\Support\Facades\Response;


class MapelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Data Mata Pelajaran';
        $tapel = Tapel::findorfail(session()->get('tapel_id'));
        $data_mapel = Mapel::where('tapel_id', $tapel->id)->orderBy('nama_mapel', 'ASC')->get();
        return view('admin.mapel.index', compact('title', 'data_mapel', 'tapel'));
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
            'tapel_id' => 'required',
            'nama_mapel' => 'required|min:3|max:255',
            'ringkasan_mapel' => 'required|min:2|max:50',
        ]);
        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        } else {
            $mapel = new Mapel([
                'tapel_id' => $request->tapel_id,
                'nama_mapel' => $request->nama_mapel,
                'ringkasan_mapel' => $request->ringkasan_mapel,
            ]);
            $mapel->save();
            return back()->with('toast_success', 'Mata Pelajaran berhasil ditambahkan');
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
            'nama_mapel' => 'required|min:3|max:255',
            'ringkasan_mapel' => 'required|min:2|max:50',
        ]);
        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        } else {
            $mapel = Mapel::findorfail($id);
            $data_mapel = [
                'ringkasan_mapel' => $request->ringkasan_mapel,
            ];
            $mapel->update($data_mapel);
            return back()->with('toast_success', 'Mata Pelajaran berhasil diedit');
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
        $mapel = Mapel::findorfail($id);

        // ✅ FIX: Validasi spesifik sebelum delete
        // Cek apakah mata pelajaran masih digunakan di tabel lain

        // 1. Cek di Pembelajaran (mata pelajaran yang diajarkan guru)
        $pembelajaran_count = \App\Pembelajaran::where('mapel_id', $id)->count();
        if ($pembelajaran_count > 0) {
            return back()->with(
                'toast_warning',
                'Mata Pelajaran tidak dapat dihapus karena masih ada ' .
                $pembelajaran_count . ' pembelajaran aktif. ' .
                'Hapus pembelajaran terlebih dahulu.'
            );
        }

        // 2. Cek di K13 Mapping Mapel
        $k13_mapping_count = \App\K13MappingMapel::where('mapel_id', $id)->count();
        if ($k13_mapping_count > 0) {
            return back()->with(
                'toast_warning',
                'Mata Pelajaran tidak dapat dihapus karena sudah di-mapping ke ' .
                $k13_mapping_count . ' kelompok mapel K13. ' .
                'Hapus mapping terlebih dahulu di menu Mapping Mapel.'
            );
        }

        // 3. Cek di KTSP Mapping Mapel
        $ktsp_mapping_count = \App\KtspMappingMapel::where('mapel_id', $id)->count();
        if ($ktsp_mapping_count > 0) {
            return back()->with(
                'toast_warning',
                'Mata Pelajaran tidak dapat dihapus karena sudah di-mapping ke ' .
                $ktsp_mapping_count . ' kelompok mapel KTSP. ' .
                'Hapus mapping terlebih dahulu di menu Mapping Mapel.'
            );
        }

        // 4. Cek di K13 KKM Mapel
        $k13_kkm_count = \App\K13KkmMapel::where('mapel_id', $id)->count();
        if ($k13_kkm_count > 0) {
            return back()->with(
                'toast_warning',
                'Mata Pelajaran tidak dapat dihapus karena sudah memiliki ' .
                $k13_kkm_count . ' data KKM K13. ' .
                'Hapus data KKM terlebih dahulu.'
            );
        }

        // 5. Cek di KTSP KKM Mapel
        $ktsp_kkm_count = \App\KtspKkmMapel::where('mapel_id', $id)->count();
        if ($ktsp_kkm_count > 0) {
            return back()->with(
                'toast_warning',
                'Mata Pelajaran tidak dapat dihapus karena sudah memiliki ' .
                $ktsp_kkm_count . ' data KKM KTSP. ' .
                'Hapus data KKM terlebih dahulu.'
            );
        }

        // 6. Cek di K13 KD Mapel
        $k13_kd_count = \App\K13KdMapel::where('mapel_id', $id)->count();
        if ($k13_kd_count > 0) {
            return back()->with(
                'toast_warning',
                'Mata Pelajaran tidak dapat dihapus karena sudah memiliki ' .
                $k13_kd_count . ' Kompetensi Dasar (KD). ' .
                'Hapus KD terlebih dahulu di menu KD Mapel.'
            );
        }

        // Jika semua validasi lolos, delete mata pelajaran
        try {
            $mapel->delete();
            return back()->with('toast_success', 'Mata Pelajaran berhasil dihapus');
        } catch (\Throwable $th) {
            // Fallback jika ada error lain yang tidak terduga
            return back()->with(
                'toast_error',
                'Terjadi kesalahan: ' . $th->getMessage()
            );
        }
    }

    public function format_import()
    {
        $file = public_path() . "/format_import/format_import_mapel.xls";
        $headers = array(
            'Content-Type: application/xls',
        );
        return Response::download($file, 'format_import_mapel ' . date('Y-m-d H_i_s') . '.xls', $headers);
    }

    public function import(Request $request)
    {
        try {
            Excel::import(new MapelImport, $request->file('file_import'));
            return back()->with('toast_success', 'Data mata pelajaran berhasil diimport');
        } catch (\Throwable $th) {
            return back()->with('toast_error', 'Maaf, format data tidak sesuai');
        }
    }
}
