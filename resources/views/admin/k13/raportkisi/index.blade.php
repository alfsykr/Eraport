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
            <li class="breadcrumb-item "><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('k13raportkisi.index') }}">{{$title}}</a></li>
            <li class="breadcrumb-item active">{{$kelas->nama_kelas}}</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <!-- ./row -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-print"></i> {{$title}} - {{$kelas->nama_kelas}}</h3>
            </div>

            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                  <thead class="bg-info">
                    <tr>
                      <th class="text-center" style="width: 5%;">No</th>
                      <th class="text-center" style="width: 10%;">NIS</th>
                      <th class="text-center" style="width: 55%;">Nama Siswa</th>
                      <th class="text-center" style="width: 10%;">L/P</th>
                      <th class="text-center" style="width: 20%;">Cetak Raport</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $no = 0; ?>
                    @foreach($data_anggota_kelas->sortBy('siswa.nama_lengkap') as $anggota_kelas)
                    <?php $no++; ?>
                    <tr>
                      <td class="text-center">{{$no}}</td>
                      <td class="text-center">{{$anggota_kelas->siswa->nis}}</td>
                      <td>{{$anggota_kelas->siswa->nama_lengkap}}</td>
                      <td class="text-center">{{$anggota_kelas->siswa->jenis_kelamin}}</td>
                      <td class="text-center">
                        <form action="{{ route('k13raportkisi.show', $anggota_kelas->id) }}" target="_blank" method="GET">
                          <input type="hidden" name="paper_size" value="{{$paper_size}}">
                          <input type="hidden" name="orientation" value="{{$orientation}}">
                          <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-print"></i> Cetak Raport Kisi-Kisi
                          </button>
                        </form>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
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


