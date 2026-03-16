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

            <!-- Info Siswa -->
            <div class="callout callout-info">
                <div class="form-group row mb-1">
                    <div class="col-sm-3">Nama Lengkap</div>
                    <div class="col-sm-9">: {{$siswa->nama_lengkap}}</div>
                </div>
                <div class="form-group row mb-1">
                    <div class="col-sm-3">Nomor Induk / NISN</div>
                    <div class="col-sm-9">: {{$siswa->nis}} / {{$siswa->nisn}}</div>
                </div>
                @if(isset($kelas))
                    <div class="form-group row mb-0">
                        <div class="col-sm-3">Kelas</div>
                        <div class="col-sm-9">: {{$kelas->nama_kelas}}</div>
                    </div>
                @endif
            </div>
            <!-- End Info Siswa -->

            <!-- Catatan Terapi Timeline -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-notes-medical"></i> Catatan Perkembangan Terapi Mingguan
                    </h3>
                </div>
                <div class="card-body">

                    @if(!isset($data_terapi) || $data_terapi->isEmpty())
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-info-circle"></i> Belum ada catatan perkembangan terapi untuk Anda pada tahun
                            pelajaran ini.
                        </div>
                    @else

                        <div class="timeline">
                            @foreach($data_terapi as $terapi)
                                <!-- Timeline date label -->
                                <div class="time-label">
                                    <span
                                        class="bg-success">{{ \Carbon\Carbon::parse($terapi->minggu_tanggal)->isoFormat('D MMMM Y') }}</span>
                                </div>

                                <!-- Timeline item -->
                                <div>
                                    <i class="fas fa-notes-medical bg-primary"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="far fa-calendar-alt"></i> Minggu
                                            {{ \Carbon\Carbon::parse($terapi->minggu_tanggal)->isoFormat('D MMM Y') }}</span>
                                        <h3 class="timeline-header"><b>Catatan Terapi</b></h3>
                                        <div class="timeline-body">

                                            @if($terapi->motorik_kasar)
                                                <div class="mb-3">
                                                    <strong><i class="fas fa-running text-info"></i> Motorik Kasar</strong>
                                                    <p class="ml-3 mb-0">{!! nl2br(e($terapi->motorik_kasar)) !!}</p>
                                                </div>
                                            @endif

                                            @if($terapi->sosialisasi)
                                                <div class="mb-3">
                                                    <strong><i class="fas fa-users text-info"></i> Sosialisasi</strong>
                                                    <p class="ml-3 mb-0">{!! nl2br(e($terapi->sosialisasi)) !!}</p>
                                                </div>
                                            @endif

                                            @if($terapi->rentang_akademis)
                                                <div class="mb-3">
                                                    <strong><i class="fas fa-book text-info"></i> Rentang Akademis</strong>
                                                    <p class="ml-3 mb-0">{!! nl2br(e($terapi->rentang_akademis)) !!}</p>
                                                </div>
                                            @endif

                                            @if($terapi->evaluasi_sosialisasi || $terapi->evaluasi_rentang_akademis)
                                                <hr>
                                                <h6 class="text-muted"><i class="fas fa-clipboard-check"></i> Evaluasi</h6>
                                            @endif

                                            @if($terapi->evaluasi_sosialisasi)
                                                <div class="mb-3">
                                                    <strong><i class="fas fa-chart-line text-success"></i> Evaluasi
                                                        Sosialisasi</strong>
                                                    <p class="ml-3 mb-0">{!! nl2br(e($terapi->evaluasi_sosialisasi)) !!}</p>
                                                </div>
                                            @endif

                                            @if($terapi->evaluasi_rentang_akademis)
                                                <div class="mb-3">
                                                    <strong><i class="fas fa-chart-line text-success"></i> Evaluasi Rentang
                                                        Akademis</strong>
                                                    <p class="ml-3 mb-0">{!! nl2br(e($terapi->evaluasi_rentang_akademis)) !!}</p>
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Timeline end -->
                            <div>
                                <i class="fas fa-clock bg-gray"></i>
                            </div>
                        </div>

                    @endif

                </div>
            </div>
            <!-- End Catatan Terapi Timeline -->

        </div>
        <!--/. container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

@include('layouts.main.footer')