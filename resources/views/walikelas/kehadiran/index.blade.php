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

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">

      @if(session('toast_success'))
        <div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <i class="fas fa-check-circle mr-1"></i> {{ session('toast_success') }}
        </div>
      @endif
      @if(session('toast_error'))
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          {{ session('toast_error') }}
        </div>
      @endif

      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-info text-white">
              <h3 class="card-title"><i class="fas fa-user-check mr-2"></i>{{$title}}</h3>
            </div>
            <form action="{{ route('kehadiran.store') }}" method="POST">
              @csrf
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-bordered table-hover mb-0">
                    <thead class="bg-info text-white">
                      <tr>
                        <th class="text-center" style="width: 5%;">No</th>
                        <th class="text-center" style="width: 5%;">NIS</th>
                        <th style="width: 35%;">Nama Siswa</th>
                        <th class="text-center" style="width: 5%;">L/P</th>
                        <th class="text-center" style="width: 5%;">Kelas</th>
                        <th class="text-center" style="width: 15%;">
                          <i class="fas fa-procedures mr-1"></i>Sakit<br>
                          <small>(hari)</small>
                        </th>
                        <th class="text-center" style="width: 15%;">
                          <i class="fas fa-hand-paper mr-1"></i>Izin<br>
                          <small>(hari)</small>
                        </th>
                        <th class="text-center" style="width: 15%;">
                          <i class="fas fa-times-circle mr-1"></i>Tanpa Keterangan<br>
                          <small>(hari)</small>
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $no = 0; ?>
                      @forelse($data_anggota_kelas->sortBy('siswa.nama_lengkap') as $anggota_kelas)
                        <?php  $no++; ?>
                        <tr>
                          <input type="hidden" name="anggota_kelas_id[]" value="{{$anggota_kelas->id}}">
                          <td class="text-center">{{$no}}</td>
                          <td class="text-center">{{$anggota_kelas->siswa->nis}}</td>
                          <td>{{$anggota_kelas->siswa->nama_lengkap}}</td>
                          <td class="text-center">{{$anggota_kelas->siswa->jenis_kelamin}}</td>
                          <td class="text-center">{{$anggota_kelas->kelas->nama_kelas}}</td>
                          <td class="p-1">
                            <input type="number" class="form-control text-center" name="sakit[]"
                              value="{{$anggota_kelas->sakit}}" min="0" max="365" required
                              oninvalid="this.setCustomValidity('Isian tidak boleh kosong')"
                              oninput="setCustomValidity('')">
                          </td>
                          <td class="p-1">
                            <input type="number" class="form-control text-center" name="izin[]"
                              value="{{$anggota_kelas->izin}}" min="0" max="365" required
                              oninvalid="this.setCustomValidity('Isian tidak boleh kosong')"
                              oninput="setCustomValidity('')">
                          </td>
                          <td class="p-1">
                            <input type="number" class="form-control text-center" name="tanpa_keterangan[]"
                              value="{{$anggota_kelas->tanpa_keterangan}}" min="0" max="365" required
                              oninvalid="this.setCustomValidity('Isian tidak boleh kosong')"
                              oninput="setCustomValidity('')">
                          </td>
                        </tr>
                      @empty
                        <tr>
                          <td colspan="8" class="text-center text-muted py-3">
                            <i class="fas fa-info-circle mr-1"></i>Tidak ada data siswa di kelas Anda
                          </td>
                        </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="card-footer">
                <small class="text-muted">
                  <i class="fas fa-info-circle mr-1"></i>
                  Isi jumlah hari ketidakhadiran siswa selama satu semester. Nilai 0 berarti tidak pernah tidak hadir.
                </small>
                <button type="submit" class="btn btn-primary float-right">
                  <i class="fas fa-save mr-1"></i>Simpan
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>

@include('layouts.main.footer')