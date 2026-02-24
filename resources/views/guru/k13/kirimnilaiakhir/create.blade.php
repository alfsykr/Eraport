@include('layouts.main.header')
@include('layouts.sidebar.guru')

<!-- Content Wrapper -->
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
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-paper-plane"></i> {{$title}}</h3>
            </div>
            <div class="card-body">
              <!-- Pilih Mapel -->
              <div class="callout callout-info">
                <form action="{{ route('kirimnilaiakhir.create') }}" method="GET">
                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Mata Pelajaran</label>
                    <div class="col-sm-10">
                      <select class="form-control select2" name="pembelajaran_id" style="width:100%;" required
                        onchange="this.form.submit();">
                        <option value="" disabled>-- Pilih Pembelajaran --</option>
                        @foreach($data_pembelajaran as $mapel)
                          <option value="{{$mapel->id}}" @if($mapel->id == $pembelajaran->id) selected @endif>
                            {{$mapel->mapel->nama_mapel}} {{$mapel->kelas->nama_kelas}}
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </form>
              </div>

              <!-- Interval KKM -->
              <div class="card">
                <div class="card-header bg-success">
                  <h3 class="card-title"><i class="fas fa-greater-than-equal"></i> Interval Predikat Berdasarkan KKM
                  </h3>
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                        class="fas fa-minus"></i></button>
                  </div>
                </div>
                <div class="card-body">
                  <table class="table table-bordered">
                    <thead class="bg-info">
                      <tr>
                        <th rowspan="2" class="text-center">KKM</th>
                        <th colspan="4" class="text-center">Predikat</th>
                      </tr>
                      <tr>
                        <th class="text-center">D = Kurang</th>
                        <th class="text-center">C = Cukup</th>
                        <th class="text-center">B = Baik</th>
                        <th class="text-center">A = Sangat Baik</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td class="text-center">{{$kkm->kkm}}</td>
                        <td class="text-center">&lt; {{$kkm->predikat_c}}</td>
                        <td class="text-center">{{$kkm->predikat_c}} &le; nilai &lt; {{$kkm->predikat_b}}</td>
                        <td class="text-center">{{$kkm->predikat_b}} &le; nilai &lt; {{$kkm->predikat_a}}</td>
                        <td class="text-center">&ge; {{$kkm->predikat_a}}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Tabel Nilai Akhir -->
              <div class="card">
                <div class="card-header bg-primary">
                  <h3 class="card-title"><i class="fas fa-file-invoice"></i> Preview Nilai Akhir Raport</h3>
                </div>
                <form action="{{ route('kirimnilaiakhir.store') }}" method="POST">
                  @csrf
                  <div class="card-body">
                    <div class="alert alert-info">
                      <i class="fas fa-info-circle"></i>
                      Nilai akhir dihitung otomatis dari rata-rata semua nilai kisi-kisi yang telah diinput.
                    </div>
                    <div class="table-responsive">
                      <table class="table table-bordered table-hover table-striped">
                        <thead class="bg-info">
                          <tr>
                            <th class="text-center">No</th>
                            <th>Nama Siswa</th>
                            <th class="text-center">KKM</th>
                            <th class="text-center">Nilai Akhir</th>
                            <th class="text-center">Predikat</th>
                          </tr>
                        </thead>
                        <tbody>
                          <input type="hidden" name="pembelajaran_id" value="{{$pembelajaran->id}}">
                          @php $no = 0; @endphp
                          @foreach($data_anggota_kelas->sortBy('siswa.nama_lengkap') as $anggota)
                            @php $no++; @endphp
                            <tr>
                              <td class="text-center">{{$no}}</td>
                              <td>{{$anggota->siswa->nama_lengkap}}</td>
                              <input type="hidden" name="anggota_kelas_id[]" value="{{$anggota->id}}">
                              <td class="text-center">{{$kkm->kkm}}</td>
                              <td class="text-center">
                                <strong>{{$anggota->nilai_akhir}}</strong>
                                <input type="hidden" name="nilai_akhir[]" value="{{$anggota->nilai_akhir}}">
                              </td>
                              <td class="text-center">
                                <span
                                  class="badge badge-{{ $anggota->predikat == 'A' ? 'success' : ($anggota->predikat == 'B' ? 'primary' : ($anggota->predikat == 'C' ? 'warning' : 'danger')) }}">
                                  {{$anggota->predikat}}
                                </span>
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="card-footer clearfix">
                    <button type="submit" class="btn btn-primary float-right">
                      <i class="fas fa-paper-plane mr-1"></i>Kirim Nilai Akhir
                    </button>
                    <a href="{{ route('kirimnilaiakhir.index') }}" class="btn btn-default float-right mr-2">Batal</a>
                  </div>
                </form>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

@include('layouts.main.footer')
</body>

</html>