@include('layouts.main.header')
@include('layouts.sidebar.siswa')

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
        <div class="col-12 col-sm-6 col-md-6">
          <div class="info-box mb-3">
            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-book"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Jumlah Mata Pelajaran</span>
              <span class="info-box-number">
                {{$jumlah_mapel}} <small>yang dipelajari</small>
              </span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->


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
                            @if($pengumuman->created_at != $pengumuman->updated_at)<small><i>edited</i></small>@endif</h3>

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