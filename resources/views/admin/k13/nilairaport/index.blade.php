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
              <h3 class="card-title"><i class="fas fa-clipboard-check"></i> {{$title}}</h3>
            </div>

            <div class="card-body">
              <div class="callout callout-info">
                <form action="{{ route('k13nilairaport.store') }}" method="POST">
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
                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Mata Pelajaran</label>
                    <div class="col-sm-10">
                      <select class="form-control select2" name="mapel_id" style="width: 100%;" required
                        onchange="this.form.submit();">
                        <option value="" disabled>-- Pilih Mata Pelajaran --</option>
                        @foreach($data_mapel->sortBy('nama_mapel') as $mpl)
                          <option value="{{$mpl->id}}" @if($mpl->id == $mapel->id) selected @endif>{{$mpl->nama_mapel}}
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </form>
              </div>

              <form action="{{ route('k13nilairaport.store') }}" method="POST" id="form-simpan-nilai">
                @csrf
                <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                <input type="hidden" name="mapel_id" value="{{ $mapel->id }}">
                <input type="hidden" name="simpan_nilai" value="1">

                <div class="table-responsive">
                  <table class="table table-bordered table-striped">
                    <thead class="bg-info">
                      <tr>
                        <th class="text-center" style="width: 5%;">No</th>
                        <th class="text-center" style="width: 5%;">NIS</th>
                        <th class="text-center" style="width: 40%;">Nama Siswa</th>
                        <th class="text-center" style="width: 8%;">KKM</th>
                        <th class="text-center" style="width: 15%;">Nilai Akhir</th>
                        <th class="text-center" style="width: 10%;">Predikat</th>
                        <th class="text-center" style="width: 17%;">Deskripsi / Catatan</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $no = 0; ?>
                      @foreach($data_anggota_kelas->sortBy('siswa.nama_lengkap') as $anggota_kelas)
                        <?php  $no++; ?>
                        <tr>
                          <td class="text-center">{{$no}}</td>
                          <td class="text-center">{{$anggota_kelas->siswa->nis}}</td>
                          <td>{{$anggota_kelas->siswa->nama_lengkap}}</td>
                          <td class="text-center">
                            @if(!is_null($anggota_kelas->nilai_raport))
                              {{$anggota_kelas->nilai_raport->kkm}}
                            @else
                              -
                            @endif
                          </td>
                          <td class="text-center">
                            <input type="number" class="form-control text-center nilai-input"
                              name="nilai[{{$anggota_kelas->id}}]" min="0" max="100"
                              data-kkm="{{ $anggota_kelas->nilai_raport->kkm ?? 75 }}"
                              data-anggota="{{ $anggota_kelas->id }}"
                              value="{{ $anggota_kelas->nilai_raport->nilai_akhir ?? '' }}" placeholder="0-100">
                          </td>
                          <td class="text-center predikat-cell" id="predikat-{{$anggota_kelas->id}}">
                            @if(!is_null($anggota_kelas->nilai_raport) && !is_null($anggota_kelas->nilai_raport->predikat_akhir))
                              <span
                                class="badge badge-{{ $anggota_kelas->nilai_raport->predikat_akhir == 'A' ? 'success' : ($anggota_kelas->nilai_raport->predikat_akhir == 'B' ? 'primary' : ($anggota_kelas->nilai_raport->predikat_akhir == 'C' ? 'warning' : 'danger')) }}">
                                {{ $anggota_kelas->nilai_raport->predikat_akhir }}
                              </span>
                            @else
                              -
                            @endif
                          </td>
                          <td>
                            <input type="text" class="form-control" name="deskripsi[{{$anggota_kelas->id}}]"
                              value="{{ $anggota_kelas->nilai_raport->k13_deskripsi_nilai_siswa->deskripsi_pengetahuan ?? '' }}"
                              placeholder="Catatan (opsional)">
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>

                <div class="mt-3">
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Nilai
                  </button>
                </div>
              </form>

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

<script>
  // Auto-calculate predikat when nilai changes
  document.querySelectorAll('.nilai-input').forEach(function (input) {
    input.addEventListener('input', function () {
      var nilai = parseInt(this.value) || 0;
      var kkm = parseInt(this.dataset.kkm) || 75;
      var anggotaId = this.dataset.anggota;
      var predikatCell = document.getElementById('predikat-' + anggotaId);

      var range = (100 - kkm) / 3;
      var predikat = '-';
      var badgeClass = 'secondary';

      if (nilai > 0) {
        if (nilai >= kkm + (range * 2)) {
          predikat = 'A'; badgeClass = 'success';
        } else if (nilai >= kkm + range) {
          predikat = 'B'; badgeClass = 'primary';
        } else if (nilai >= kkm) {
          predikat = 'C'; badgeClass = 'warning';
        } else {
          predikat = 'D'; badgeClass = 'danger';
        }
      }

      predikatCell.innerHTML = predikat !== '-'
        ? '<span class="badge badge-' + badgeClass + '">' + predikat + '</span>'
        : '-';
    });
  });
</script>

@include('layouts.main.footer')