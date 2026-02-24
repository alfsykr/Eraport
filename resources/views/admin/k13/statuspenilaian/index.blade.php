@include('layouts.main.header')
@include('layouts.sidebar.admin')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">{{$title}}</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">{{$title}}</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-check-circle"></i> {{$title}}</h3>
            </div>
            <div class="card-body">
              <div class="callout callout-info">
                <form action="{{ route('k13statuspenilaian.store') }}" method="POST">
                  @csrf
                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Kelas</label>
                    <div class="col-sm-10">
                      <select class="form-control select2" name="kelas_id" style="width: 100%;" required
                        onchange="this.form.submit();">
                        <option value="" disabled>-- Pilih Kelas --</option>
                        @foreach($data_kelas->sortBy('tingkatan_kelas') as $kls)
                          <option value="{{$kls->id}}" @if($kelas->id == $kls->id) selected @endif>{{$kls->nama_kelas}}
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </form>
              </div>

              <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <thead class="bg-info text-white">
                    <tr>
                      <th rowspan="2" class="text-center align-middle" style="width:5%;">No</th>
                      <th rowspan="2" class="text-center align-middle">Mata Pelajaran</th>
                      <th rowspan="2" class="text-center align-middle">Nama Guru</th>
                      <th colspan="3" class="text-center">Status Kisi-Kisi</th>
                    </tr>
                    <tr>
                      <th class="text-center" style="width:15%;">Rencana<br>Kisi-Kisi</th>
                      <th class="text-center" style="width:15%;">Input<br>Nilai</th>
                      <th class="text-center" style="width:15%;">Kirim<br>Nilai Akhir</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $no = 0; ?>
                    @forelse($data_pembelajaran_kelas->sortBy('mapel.nama_mapel') as $pembelajaran)
                      <?php  $no++; ?>
                      <tr>
                        <td class="text-center">{{$no}}</td>
                        <td>{{$pembelajaran->mapel->nama_mapel}}</td>
                        <td>{{$pembelajaran->guru->nama_guru ?? ($pembelajaran->guru->nama_lengkap ?? '-')}}</td>

                        {{-- Status Rencana Kisi-Kisi --}}
                        @if($pembelajaran->rencana_kisi > 0)
                          <td class="text-center bg-success text-white">
                            <i class="fas fa-check"></i> {{$pembelajaran->rencana_kisi}}
                          </td>
                        @else
                          <td class="text-center bg-danger text-white">
                            <i class="fas fa-times"></i>
                          </td>
                        @endif

                        {{-- Status Input Nilai Kisi-Kisi --}}
                        @if($pembelajaran->nilai_kisi > 0)
                          <td class="text-center bg-success text-white">
                            <i class="fas fa-check"></i> {{$pembelajaran->nilai_kisi}}
                          </td>
                        @else
                          <td class="text-center bg-danger text-white">
                            <i class="fas fa-times"></i>
                          </td>
                        @endif

                        {{-- Status Kirim Nilai Akhir --}}
                        @if($pembelajaran->nilai_akhir_kisi > 0)
                          <td class="text-center bg-success text-white">
                            <i class="fas fa-check"></i> {{$pembelajaran->nilai_akhir_kisi}}
                          </td>
                        @else
                          <td class="text-center bg-danger text-white">
                            <i class="fas fa-times"></i>
                          </td>
                        @endif

                      </tr>
                    @empty
                      <tr>
                        <td colspan="6" class="text-center text-muted">Tidak ada data pembelajaran untuk kelas ini.</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>

      </div>
      <!-- /.row -->
    </div>
    <!--/. container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

@include('layouts.main.footer')