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
              <h3 class="card-title"><i class="fas fa-check-square"></i> {{$title}}</h3>
            </div>
            <div class="card-body">
              <div class="callout callout-info">
                <form action="{{ route('k13pengelolaannilai.store') }}" method="POST">
                  @csrf
                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Kelas</label>
                    <div class="col-sm-10">
                      <select class="form-control select2" name="kelas_id" style="width: 100%;" required
                        onchange="this.form.submit();">
                        <option value="" disabled>-- Pilih Kelas --</option>
                        @foreach($data_kelas->sortBy('tingkatan_kelas') as $kls)
                          <option value="{{$kls->id}}" @if($kls->id == $kelas->id) selected @endif>{{$kls->nama_kelas}}
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </form>
              </div>

              <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                  <thead class="bg-info text-white">
                    <tr>
                      <th class="text-center" rowspan="2" style="width:5%; vertical-align:middle;">No</th>
                      <th class="text-center" rowspan="2" style="vertical-align:middle;">Mata Pelajaran</th>
                      <th class="text-center" rowspan="2" style="vertical-align:middle;">Nama Guru</th>
                      <th class="text-center" colspan="3">Status Kisi-Kisi</th>
                    </tr>
                    <tr>
                      <th class="text-center" style="width:15%;">Rencana<br>Kisi-Kisi</th>
                      <th class="text-center" style="width:15%;">Input<br>Nilai</th>
                      <th class="text-center" style="width:15%;">Kirim<br>Nilai Akhir</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $no = 0; ?>
                    @forelse($data_pembelajaran->sortBy('mapel.nama_mapel') as $pembelajaran)
                      <?php  $no++; ?>
                      <tr>
                        <td class="text-center">{{$no}}</td>
                        <td>{{$pembelajaran->mapel->nama_mapel}}</td>
                        <td>{{$pembelajaran->guru->nama_guru ?? '-'}}</td>

                        {{-- Status Rencana Kisi-Kisi --}}
                        <td class="text-center">
                          @if($pembelajaran->jumlah_rencana_kisi > 0)
                            <span class="badge badge-success p-2">
                              <i class="fas fa-check"></i> {{ $pembelajaran->jumlah_rencana_kisi }} indikator
                            </span>
                          @else
                            <span class="badge badge-danger p-2">
                              <i class="fas fa-times"></i> Belum ada
                            </span>
                          @endif
                        </td>

                        {{-- Status Input Nilai Kisi-Kisi --}}
                        <td class="text-center">
                          @if($pembelajaran->jumlah_nilai_kisi > 0)
                            <span class="badge badge-success p-2">
                              <i class="fas fa-check"></i> {{ $pembelajaran->jumlah_nilai_kisi }} nilai
                            </span>
                          @else
                            <span class="badge badge-danger p-2">
                              <i class="fas fa-times"></i> Belum ada
                            </span>
                          @endif
                        </td>

                        {{-- Status Kirim Nilai Akhir --}}
                        <td class="text-center">
                          @if($pembelajaran->jumlah_kirim_nilai > 0)
                            @if($pembelajaran->jumlah_kirim_nilai >= $pembelajaran->jumlah_anggota)
                              <span class="badge badge-success p-2">
                                <i class="fas fa-check"></i> Selesai ({{ $pembelajaran->jumlah_kirim_nilai }})
                              </span>
                            @else
                              <span class="badge badge-warning p-2">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ $pembelajaran->jumlah_kirim_nilai }}/{{ $pembelajaran->jumlah_anggota }}
                              </span>
                            @endif
                          @else
                            <span class="badge badge-danger p-2">
                              <i class="fas fa-times"></i> Belum ada
                            </span>
                          @endif
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="6" class="text-center text-muted">Tidak ada data pembelajaran untuk kelas ini.</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>

              {{-- Keterangan --}}
              <div class="mt-2">
                <small class="text-muted">
                  <span class="badge badge-success">✓</span> Sudah diisi &nbsp;
                  <span class="badge badge-warning">!</span> Sebagian &nbsp;
                  <span class="badge badge-danger">✗</span> Belum diisi
                </small>
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