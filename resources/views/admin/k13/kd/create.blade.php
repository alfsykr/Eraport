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
            <li class="breadcrumb-item "><a href="{{ route('k13kd.index') }}">Data Kisi-Kisi</a></li>
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
              <h3 class="card-title"><i class="fas fa-clipboard-list"></i> {{$title}}</h3>
            </div>

            <div class="card-body">
              {{-- Pilih Mata Pelajaran --}}
              <div class="callout callout-info">
                <form action="{{ route('k13kd.create') }}" method="GET">
                  @csrf
                  <div class="form-group row">
                    <label for="mapel_id" class="col-sm-2 col-form-label">Mata Pelajaran</label>
                    <div class="col-sm-6">
                      <select class="form-control select2" name="mapel_id" style="width: 100%;" required
                        onchange="this.form.submit();">
                        <option value="" disabled>-- Pilih Mapel --</option>
                        @foreach($data_mapel as $mapel)
                          <option value="{{$mapel->id}}" @if ($mapel->id == $mapel_id) selected @endif>
                            {{$mapel->nama_mapel}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </form>
              </div>

              <form id="dynamic_form" action="{{ route('k13kd.store') }}" method="POST">
                @csrf
                <input type="hidden" name="mapel_id" value="{{$mapel_id}}">
                <input type="hidden" name="tingkatan_kelas" value="0">
                <input type="hidden" name="semester" value="{{$tapel->semester}}">

                <div class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Deskripsi Kisi-Kisi</th>
                        <th style="width: 40px;">Baris</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!--  -->
                    </tbody>
                  </table>
                </div>
            </div>

            <div class="card-footer clearfix">
              <button type="submit" class="btn btn-primary float-right">Simpan</button>
              <a href="{{ route('k13kd.index') }}" class="btn btn-default float-right mr-2">Batal</a>
            </div>
            </form>
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

<script type="text/javascript">
  $(document).ready(function () {

    var count = 1;

    dynamic_field(count);

    function dynamic_field(number) {
      html = '<tr>';
      html += `<td>
                  <textarea class="form-control" name="kompetensi_dasar[]" rows="2" required
                    placeholder="Tuliskan deskripsi kisi-kisi penilaian..."
                    oninvalid="this.setCustomValidity('data tidak boleh kosong')"
                    oninput="setCustomValidity('')"></textarea>
              </td>`;

      if (number > 1) {
        html += '<td><button type="button" name="remove" class="btn btn-danger shadow btn-xs sharp remove"><i class="fa fa-trash"></i></button></td></tr>';
        $('tbody').append(html);
      } else {
        html += '<td><button type="button" name="add" id="add" class="btn btn-primary shadow btn-xs sharp"><i class="fa fa-plus"></i></button></td></tr>';
        $('tbody').html(html);
      }
    }

    $(document).on('click', '#add', function () {
      count++;
      dynamic_field(count);
    });

    $(document).on('click', '.remove', function () {
      count--;
      $(this).closest("tr").remove();
    });

  });
</script>