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
            <!-- <li class="breadcrumb-item "><a href="{{ route('dashboard') }}">{{$title}}</a></li> -->
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

      <!-- Info -->
      <div class="callout callout-success">
        <h5>{{$sekolah->nama_sekolah}}</h5>
        <p>Tahun Pelajaran {{$tapel->tahun_pelajaran}}
          @if($tapel->semester == 1)
            Semester Ganjil
          @else
            Semester Genap
          @endif
        </p>
      </div>
      <!-- End Info  -->

      <!-- Info boxes -->
      <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-layer-group"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Jumlah Kelas</span>
              <span class="info-box-number">{{$jumlah_kelas_diampu}} <small>kelas diampu</small></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box mb-3">
            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-book"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Jumlah Mapel</span>
              <span class="info-box-number">{{$jumlah_mapel_diampu}} <small>mapel diampu</small></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix hidden-md-up"></div>

        <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box mb-3">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-users"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Jumlah Siswa</span>
              <span class="info-box-number">{{$jumlah_siswa_diampu}} <small>siswa diampu</small></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Capaian Proses Penilaian</h3>

              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              @if(Session::get('kurikulum') == '2013')
                <div class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <thead class="bg-success text-white">
                      <tr>
                        <th rowspan="2" class="text-center align-middle" style="width:4%">No</th>
                        <th rowspan="2" class="text-center align-middle">Kelas</th>
                        <th rowspan="2" class="text-center align-middle">Mata Pelajaran</th>
                        <th colspan="3" class="text-center">Status Kisi-Kisi</th>
                      </tr>
                      <tr>
                        <th class="text-center" style="width:16%">Rencana<br>Kisi-Kisi</th>
                        <th class="text-center" style="width:16%">Input<br>Nilai</th>
                        <th class="text-center" style="width:16%">Kirim<br>Nilai Akhir</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php  $no = 0; ?>
                      @foreach($data_capaian_penilaian as $penilaian)
                        <?php    $no++; ?>
                        <tr>
                          <td class="text-center">{{$no}}</td>
                          <td class="text-center">{{$penilaian->kelas->nama_kelas}}</td>
                          <td>{{$penilaian->mapel->nama_mapel}}</td>

                          {{-- Rencana Kisi-Kisi --}}
                          <td class="text-center">
                            @if($penilaian->jumlah_rencana_kisi > 0)
                              <span class="badge badge-success p-2">
                                <i class="fas fa-check"></i> {{$penilaian->jumlah_rencana_kisi}} indikator
                              </span>
                            @else
                              <span class="badge badge-danger p-2">
                                <i class="fas fa-times"></i> Belum ada
                              </span>
                            @endif
                          </td>

                          {{-- Input Nilai Kisi-Kisi --}}
                          <td class="text-center">
                            @if($penilaian->jumlah_nilai_kisi > 0)
                              <span class="badge badge-success p-2">
                                <i class="fas fa-check"></i> {{$penilaian->jumlah_nilai_kisi}} nilai
                              </span>
                            @else
                              <span class="badge badge-danger p-2">
                                <i class="fas fa-times"></i> Belum ada
                              </span>
                            @endif
                          </td>

                          {{-- Kirim Nilai Akhir --}}
                          <td class="text-center">
                            @if($penilaian->jumlah_kirim_nilai > 0)
                              @if($penilaian->jumlah_kirim_nilai >= $penilaian->jumlah_anggota)
                                <span class="badge badge-success p-2">
                                  <i class="fas fa-check"></i> Selesai ({{$penilaian->jumlah_kirim_nilai}})
                                </span>
                              @else
                                <span class="badge badge-warning p-2">
                                  <i class="fas fa-exclamation-triangle"></i>
                                  {{$penilaian->jumlah_kirim_nilai}}/{{$penilaian->jumlah_anggota}}
                                </span>
                              @endif
                            @else
                              <span class="badge badge-danger p-2">
                                <i class="fas fa-times"></i> Belum ada
                              </span>
                            @endif
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
                <!-- /.table-responsive -->

              @elseif(Session::get('kurikulum') == '2006')
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <thead class="bg-success">
                      <tr>
                        <th rowspan="2" class="text-center">No</th>
                        <th rowspan="2" class="text-center">Kelas</th>
                        <th rowspan="2" class="text-center">Mata Pelajaran</th>
                        <th rowspan="2" class="text-center" style="width: 50px;">KKM</th>
                        <th rowspan="2" class="text-center" style="width: 100px;">Bobot</th>
                        <th colspan="4" class="text-center" style="width: 200px;">Input Nilai</th>
                        <th colspan="2" class="text-center" style="width: 100px;">Status Nilai Raport</th>
                      </tr>
                      <tr>
                        <th class="text-center" style="width: 100px;">Tugas</th>
                        <th class="text-center" style="width: 100px;">UH</th>
                        <th class="text-center" style="width: 100px;">UTS</th>
                        <th class="text-center" style="width: 100px;">UAS</th>

                        <th class="text-center" style="width: 100px;">Kirim Nilai</th>
                        <th class="text-center" style="width: 100px;">Input Deskripsi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php  $no = 0; ?>
                      @foreach($data_capaian_penilaian as $penilaian)
                        <?php    $no++; ?>
                        <tr>
                          <td class="text-center">{{$no}}</td>
                          <td class="text-center">{{$penilaian->kelas->nama_kelas}}</td>
                          <td>{{$penilaian->mapel->nama_mapel}}</td>
                          <td class="text-center">
                            @if(is_null($penilaian->kkm))
                              <span class="badge badge-danger">0</span>
                            @else
                              <span class="badge badge-success">{{$penilaian->kkm}}</span>
                            @endif
                          </td>

                          <td class="text-center">
                            @if(is_null($penilaian->bobot_tugas))
                              <b class="text-danger">
                                0
                              </b>
                            @else
                              <b class="text-success">
                                {{$penilaian->bobot_tugas}};{{$penilaian->bobot_uh}};{{$penilaian->bobot_uts}};{{$penilaian->bobot_uas}}
                              </b>
                            @endif
                          </td>

                          @if($penilaian->nilai_tugas == 0)
                            <td><span class="badge badge-danger">Belum Input</span></td>
                          @else
                            <td><span class="badge badge-success">Sudah Input</span></td>
                          @endif

                          @if($penilaian->nilai_uh == 0)
                            <td><span class="badge badge-danger">Belum Input</span></td>
                          @else
                            <td><span class="badge badge-success">Sudah Input</span></td>
                          @endif

                          @if($penilaian->nilai_uts_uas == 0)
                            <td><span class="badge badge-danger">Belum Input</span></td>
                            <td><span class="badge badge-danger">Belum Input</span></td>
                          @else
                            <td><span class="badge badge-success">Sudah Input</span></td>
                            <td><span class="badge badge-success">Sudah Input</span></td>
                          @endif

                          @if($penilaian->kirim_nilai == 0)
                            <td><span class="badge badge-danger">Belum Kirim</span></td>
                          @else
                            <td><span class="badge badge-success">Sudah Kirim</span></td>
                          @endif

                          @if($penilaian->deskripsi == 0)
                            <td><span class="badge badge-danger">Belum Input</span></td>
                          @else
                            <td><span class="badge badge-success">Sudah Input</span></td>
                          @endif

                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @endif
            </div>
            <!-- /.card-body -->
          </div>
        </div>
      </div>


      <!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <div class="col-md-12">
          <!-- MAP & BOX PANE -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Pengumuman</h3>

              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body pr-1">
              <div class="row">
                <div class="col-md-12">
                  <!-- The time line -->
                  <div class="timeline">
                    <!-- timeline time label -->
                    <div class="time-label">
                      <span class="bg-success">Pengumuman Terakhir</span>
                    </div>
                    <!-- /.timeline-label -->
                    <!-- timeline item -->
                    @foreach($data_pengumuman->sortByDesc('created_at') as $pengumuman)
                      <div>
                        <i class="fas fa-envelope bg-primary"></i>
                        <div class="timeline-item">
                          <span class="time"><i class="far fa-clock"></i> {{$pengumuman->created_at}}</span>

                          <h3 class="timeline-header"><a href="#">{{$pengumuman->user->admin->nama_lengkap}}</a>
                            {{$pengumuman->judul}}
                            @if($pengumuman->created_at != $pengumuman->updated_at)<small><i>edited</i></small>@endif
                          </h3>

                          <div class="timeline-body">
                            {!! $pengumuman->isi !!}
                          </div>
                        </div>
                      </div>
                    @endforeach
                    <!-- END timeline item -->
                    <div>
                      <i class="fas fa-clock bg-gray"></i>
                    </div>
                  </div>
                </div>
                <!-- /.col -->
              </div>
            </div>
            <!-- /.card-body -->
          </div>

        </div>
        <!-- /.col -->

      </div>
      <!-- /.row -->
    </div>
    <!--/. container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

@include('layouts.main.footer')