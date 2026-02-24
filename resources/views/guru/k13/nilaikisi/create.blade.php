@include('layouts.main.header')
@include('layouts.sidebar.guru')

<!-- Content Wrapper. Contains page content -->
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
            <li class="breadcrumb-item"><a href="{{ route('nilaikisi.index') }}">Input Nilai Kisi-kisi</a></li>
            <li class="breadcrumb-item active">Input</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">

      @if(session('toast_success'))
        <div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          {{ session('toast_success') }}
        </div>
      @endif
      @if(session('toast_error'))
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          {{ session('toast_error') }}
        </div>
      @endif
      @if(session('toast_warning'))
        <div class="alert alert-warning alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          {{ session('toast_warning') }}
        </div>
      @endif

      <div class="card">
        <div class="card-header bg-primary text-white">
          <h3 class="card-title">
            <i class="fas fa-edit mr-2"></i>
            {{ $pembelajaran->mapel->nama_mapel }} - {{ $pembelajaran->kelas->nama_kelas }}
          </h3>
        </div>

        <form action="{{ route('nilaikisi.store') }}" method="POST">
          @csrf
          <input type="hidden" name="pembelajaran_id" value="{{ $pembelajaran->id }}">

          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-bordered table-sm mb-0">
                <thead>
                  <tr class="bg-success text-white">
                    <th class="text-center" style="width:4%">No</th>
                    <th style="min-width:200px">Nama Siswa</th>
                    @foreach($data_rencana as $i => $rencana)
                      <th class="text-center" style="min-width:80px">{{ $i + 1 }}</th>
                    @endforeach
                  </tr>
                  <tr class="bg-light">
                    <th colspan="2" class="text-center text-muted small">Indikator &rarr;</th>
                    @foreach($data_rencana as $rencana)
                      <th class="text-center small text-muted"
                        style="font-weight:normal; font-size:11px; white-space:normal; min-width:120px; max-width:200px; word-wrap:break-word;">
                        {{ $rencana->deskripsi_penilaian }}
                      </th>
                    @endforeach
                  </tr>
                </thead>
                <tbody>
                  @foreach($data_anggota_kelas as $i => $anggota)
                    <tr>
                      <td class="text-center">{{ $i + 1 }}</td>
                      <td>{{ $anggota->siswa->nama_lengkap }}</td>
                      @foreach($data_rencana as $rencana)
                        <td class="text-center p-1">
                          <input type="number" name="nilai[{{ $rencana->id }}][{{ $anggota->id }}]"
                            class="form-control form-control-sm text-center nilai-input" min="0" max="100"
                            value="{{ $nilai_existing[$anggota->id][$rencana->id] ?? '' }}" placeholder="-"
                            style="width:65px; margin:auto;">
                        </td>
                      @endforeach
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>

          <div class="card-footer">
            <button type="submit" class="btn btn-success">
              <i class="fas fa-save mr-1"></i>Simpan Nilai
            </button>
            <a href="{{ route('nilaikisi.index') }}" class="btn btn-secondary ml-2">Kembali</a>
          </div>
        </form>
      </div>

      {{-- Legenda indikator --}}
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Keterangan Indikator</h3>
        </div>
        <div class="card-body">
          <table class="table table-sm table-bordered">
            <thead class="bg-light">
              <tr>
                <th class="text-center" style="width:5%">No</th>
                <th>Deskripsi Indikator / KD</th>
              </tr>
            </thead>
            <tbody>
              @foreach($data_rencana as $i => $rencana)
                <tr>
                  <td class="text-center">{{ $i + 1 }}</td>
                  <td>{{ $rencana->deskripsi_penilaian }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </section>
</div>

<script>
  document.querySelectorAll('.nilai-input').forEach(function (input) {
    input.addEventListener('change', function () {
      var val = parseInt(this.value);
      if (this.value !== '' && (isNaN(val) || val < 0 || val > 100)) {
        this.value = '';
        alert('Nilai harus antara 0 sampai 100');
      }
    });
  });
</script>

@include('layouts.main.footer')

</body>

</html>