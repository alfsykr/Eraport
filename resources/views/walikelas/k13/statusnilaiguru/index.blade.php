@include('layouts.main.header')
@include('layouts.sidebar.guru')

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
      <!-- ./row -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-check-circle"></i> {{$title}}</h3>
            </div>
            <div class="card-body">

              <!-- Legend -->
              <div class="mb-3">
                <span class="badge badge-success px-2 py-1 mr-2"><i class="fas fa-check"></i> Sudah</span>
                <span class="badge badge-danger px-2 py-1 mr-2"><i class="fas fa-times"></i> Belum</span>
                <span class="badge badge-warning px-2 py-1"><i class="fas fa-clock"></i> Sebagian</span>
                <small class="text-muted ml-2">— Angka menunjukkan jumlah data yang sudah diinput</small>
              </div>

              <div class="table-responsive">
                <table class="table table-bordered table-hover">
                  <thead>
                    <tr class="bg-info text-white text-center">
                      <th rowspan="2" class="align-middle" style="width: 40px;">No</th>
                      <th rowspan="2" class="align-middle">Mata Pelajaran</th>
                      <th rowspan="2" class="align-middle" style="width: 70px;">Kelas</th>
                      <th rowspan="2" class="align-middle">Nama Guru</th>
                      <th colspan="2" class="text-center" style="background-color: #0d7a8a;">Sistem Kisi-Kisi</th>
                      <th colspan="2" class="text-center" style="background-color: #1a6b3a;">Nilai Raport</th>
                    </tr>
                    <tr class="text-center" style="font-size: 0.82rem;">
                      <th style="background-color: #0d7a8a; color: white; width: 110px;">Rencana Kisi-kisi</th>
                      <th style="background-color: #0d7a8a; color: white; width: 110px;">Input Nilai Kisi-kisi</th>
                      <th style="background-color: #1a6b3a; color: white; width: 100px;">Kirim Nilai Akhir</th>
                      <th style="background-color: #1a6b3a; color: white; width: 100px;">Proses Deskripsi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $no = 0; ?>
                    @forelse($data_pembelajaran_kelas as $pembelajaran)
                      <?php  $no++; ?>
                      <tr>
                        <td class="text-center">{{$no}}</td>
                        <td><strong>{{$pembelajaran->mapel->nama_mapel}}</strong></td>
                        <td class="text-center">{{$pembelajaran->kelas->nama_kelas}}</td>
                        <td>{{$pembelajaran->guru->nama_lengkap}}</td>

                        {{-- Rencana Kisi-kisi --}}
                        @if($pembelajaran->rencana_kisi == 0)
                          <td class="text-center bg-danger text-white">
                            <i class="fas fa-times"></i> <small>Belum</small>
                          </td>
                        @else
                          <td class="text-center bg-success text-white">
                            <i class="fas fa-check"></i> <small>{{$pembelajaran->rencana_kisi}} kisi</small>
                          </td>
                        @endif

                        {{-- Input Nilai Kisi-kisi --}}
                        @if($pembelajaran->rencana_kisi == 0)
                          <td class="text-center bg-secondary text-white">
                            <small>—</small>
                          </td>
                        @elseif($pembelajaran->nilai_kisi == 0)
                          <td class="text-center bg-danger text-white">
                            <i class="fas fa-times"></i> <small>Belum</small>
                          </td>
                        @elseif($pembelajaran->nilai_kisi < $pembelajaran->rencana_kisi)
                          <td class="text-center bg-warning text-dark">
                            <i class="fas fa-clock"></i>
                            <small>{{$pembelajaran->nilai_kisi}}/{{$pembelajaran->rencana_kisi}}</small>
                          </td>
                        @else
                          <td class="text-center bg-success text-white">
                            <i class="fas fa-check"></i> <small>{{$pembelajaran->nilai_kisi}} nilai</small>
                          </td>
                        @endif

                        {{-- Kirim Nilai Akhir --}}
                        @if($pembelajaran->nilai_akhir == 0)
                          <td class="text-center bg-danger text-white">
                            <i class="fas fa-times"></i> <small>Belum</small>
                          </td>
                        @else
                          <td class="text-center bg-success text-white">
                            <i class="fas fa-check"></i> <small>{{$pembelajaran->nilai_akhir}} siswa</small>
                          </td>
                        @endif

                        {{-- Proses Deskripsi --}}
                        @if($pembelajaran->deskripsi == 0)
                          <td class="text-center bg-danger text-white">
                            <i class="fas fa-times"></i> <small>Belum</small>
                          </td>
                        @else
                          <td class="text-center bg-success text-white">
                            <i class="fas fa-check"></i> <small>{{$pembelajaran->deskripsi}} siswa</small>
                          </td>
                        @endif

                      </tr>
                    @empty
                      <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                          <i class="fas fa-info-circle mr-1"></i> Tidak ada data pembelajaran untuk kelas ini.
                        </td>
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