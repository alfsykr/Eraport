@include('layouts.main.header')
@include('layouts.sidebar.guru')

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">{{$title}}</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">{{$title}}</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-notes-medical"></i> Input Perkembangan Terapi Mingguan</h3>
        </div>
        <div class="card-body">
          @if(!isset($kelas))
          <div class="alert alert-info mb-0">Anda belum terdaftar sebagai wali kelas pada tahun pelajaran ini.</div>
          @else
          <div class="callout callout-info">
            <form method="GET" action="{{ route('terapi.index') }}">
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Minggu Tanggal</label>
                <div class="col-sm-10">
                  <input type="date" class="form-control" name="minggu_tanggal" value="{{ $tanggal }}" onchange="this.form.submit();">
                </div>
              </div>
            </form>
          </div>

          <form action="{{ route('terapi.store') }}" method="POST">
            @csrf
            <input type="hidden" name="minggu_tanggal" value="{{ $tanggal }}">
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead class="bg-success">
                  <tr>
                    <th class="text-center" style="width: 4%">No</th>
                    <th style="width: 18%">Nama Siswa</th>
                    <th>Motorik Kasar</th>
                    <th>Sosialisasi</th>
                    <th>Rentang Akademis</th>
                    <th>Evaluasi Sosialisasi</th>
                    <th>Evaluasi Rentang Akademis</th>
                    <th class="text-center" style="width: 6%">Cetak</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $no = 0; ?>
                  @foreach($data_anggota_kelas->sortBy('siswa.nama_lengkap') as $anggota)
                  <?php $no++; ?>
                  <tr>
                    <td class="text-center">{{$no}}</td>
                    <td>{{$anggota->siswa->nama_lengkap}}</td>
                    <input type="hidden" name="anggota_kelas_id[]" value="{{ $anggota->id }}">
                    <td><textarea name="motorik_kasar[]" class="form-control" rows="2">{{ $anggota->tp_motorik_kasar }}</textarea></td>
                    <td><textarea name="sosialisasi[]" class="form-control" rows="2">{{ $anggota->tp_sosialisasi }}</textarea></td>
                    <td><textarea name="rentang_akademis[]" class="form-control" rows="2">{{ $anggota->tp_rentang_akademis }}</textarea></td>
                    <td><textarea name="evaluasi_sosialisasi[]" class="form-control" rows="2">{{ $anggota->tp_eval_sosialisasi }}</textarea></td>
                    <td><textarea name="evaluasi_rentang_akademis[]" class="form-control" rows="2">{{ $anggota->tp_eval_rentang_akademis }}</textarea></td>
                    <td class="text-center">
                      <a class="btn btn-sm btn-primary" target="_blank" href="{{ route('terapi.raport', $anggota->id) }}?minggu_tanggal={{ $tanggal }}"><i class="fas fa-print"></i></a>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="mt-3 text-right">
              <button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Simpan Minggu Ini</button>
            </div>
          </form>
          @endif
        </div>
      </div>
    </div>
  </section>
</div>

@include('layouts.main.footer')







