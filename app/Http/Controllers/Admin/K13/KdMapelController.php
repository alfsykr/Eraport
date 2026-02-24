<?php

namespace App\Http\Controllers\Admin\K13;

use App\Http\Controllers\Controller;
use App\K13KdMapel;
use App\Kelas;
use App\Mapel;
use App\Tapel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Excel;

class KdMapelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Data Kompetensi Dasar';
        $tapel = Tapel::findorfail(session()->get('tapel_id'));
        $data_mapel = Mapel::where('tapel_id', $tapel->id)->orderBy('nama_mapel', 'ASC')->get();
        $id_mapel = Mapel::where('tapel_id', $tapel->id)->get('id');

        $data_kelas = Kelas::where('tapel_id', $tapel->id)->groupBy('tingkatan_kelas')->orderBy('tingkatan_kelas', 'ASC')->get();

        if (count($data_mapel) == 0) {
            return redirect('admin/mapel')->with('toast_warning', 'Mohon isikan data mata pelajaran');
        } elseif (count($data_kelas) == 0) {
            return redirect('admin/kelas')->with('toast_warning', 'Mohon isikan data kelas');
        } else {
            $data_kd = K13KdMapel::whereIn('mapel_id', $id_mapel)->get();
            return view('admin.k13.kd.index', compact('title', 'data_mapel', 'data_kelas', 'data_kd'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mapel_id' => 'required',
        ]);
        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        } else {
            $title = 'Tambah Data Kisi-Kisi';
            $mapel_id = $request->mapel_id;
            $tingkatan_kelas = 0; // default, tidak dipakai

            $tapel = Tapel::findorfail(session()->get('tapel_id'));
            $data_mapel = Mapel::where('tapel_id', $tapel->id)->orderBy('nama_mapel', 'ASC')->get();
            $data_kelas = Kelas::where('tapel_id', $tapel->id)->groupBy('tingkatan_kelas')->orderBy('tingkatan_kelas', 'ASC')->get();
            return view('admin.k13.kd.create', compact('title', 'mapel_id', 'tingkatan_kelas', 'tapel', 'data_mapel', 'data_kelas'));
        }
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
            'kompetensi_dasar.*' => 'required|min:5|max:255',
        ]);
        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        } else {
            for ($count = 0; $count < count($request->kompetensi_dasar); $count++) {
                $data_kd = array(
                    'mapel_id' => $request->mapel_id,
                    'tingkatan_kelas' => 0,   // default
                    'semester' => $request->semester,
                    'jenis_kompetensi' => 3,   // default: Pengetahuan (tidak relevan di sistem baru)
                    'kode_kd' => '-', // default
                    'kompetensi_dasar' => $request->kompetensi_dasar[$count],
                    'ringkasan_kompetensi' => '-', // default
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                );
                $store_data_kd[] = $data_kd;
            }
            K13KdMapel::insert($store_data_kd);
            return redirect('admin/k13kd')->with('toast_success', 'Data kisi-kisi berhasil ditambahkan');
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
            'kompetensi_dasar' => 'required|min:5|max:255',
        ]);
        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        } else {
            $kd = K13KdMapel::findorfail($id);
            $data_kd = [
                'kompetensi_dasar' => $request->kompetensi_dasar,
                'ringkasan_kompetensi' => '-',
            ];
            $kd->update($data_kd);
            return back()->with('toast_success', 'Data kisi-kisi berhasil diedit');
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
        $kd = K13KdMapel::findorfail($id);
        try {
            $kd->delete();
            return back()->with('toast_success', 'Kompetensi dasar berhasil dihapus');
        } catch (\Throwable $th) {
            return back()->with('toast_error', 'Data Kompetensi dasar tidak dapat dihapus');
        }
    }
}
