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
              <h3 class="card-title"><i class="fas fa-user-edit"></i> {{$title}}</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool btn-sm" data-toggle="modal" data-target="#modal-tambah">
                  <i class="fas fa-plus"></i>
                </button>
              </div>
            </div>

            <!-- Modal tambah  -->
            <div class="modal fade" id="modal-tambah">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Tambah {{$title}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <form action="{{ route('personalprogram.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                      <div class="form-group row">
                        <label for="siswa_id" class="col-sm-3 col-form-label">Siswa</label>
                        <div class="col-sm-9">
                          <select class="form-control select2" name="siswa_id" style="width: 100%;" required>
                            <option value="">-- Pilih Siswa --</option>
                            @foreach($data_siswa as $s)
                            <option value="{{$s->id}}">{{$s->nama_lengkap}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="semester" class="col-sm-3 col-form-label">Semester</label>
                        <div class="col-sm-9">
                          <select class="form-control" name="semester" required>
                            <option value="">-- Pilih Semester --</option>
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                          </select>
                        </div>
                      </div>
                      <div class="form-group">
                        <label>Motorik Kasar</label>
                        <textarea class="form-control" name="motorik_kasar" rows="2"></textarea>
                      </div>
                      <div class="form-group">
                        <label>Sosialisasi</label>
                        <textarea class="form-control" name="sosialisasi" rows="2"></textarea>
                      </div>
                      <div class="form-group">
                        <label>Rentang Akademis</label>
                        <textarea class="form-control" name="rentang_akademis" rows="2"></textarea>
                      </div>
                      <hr/>
                      <div class="form-group">
                        <label>Evaluasi Motorik Kasar</label>
                        <textarea class="form-control" name="evaluasi_motorik_kasar" rows="2"></textarea>
                      </div>
                      <div class="form-group">
                        <label>Evaluasi Sosialisasi</label>
                        <textarea class="form-control" name="evaluasi_sosialisasi" rows="2"></textarea>
                      </div>
                      <div class="form-group">
                        <label>Evaluasi Rentang Akademis</label>
                        <textarea class="form-control" name="evaluasi_rentang_akademis" rows="2"></textarea>
                      </div>
                    </div>
                    <div class="modal-footer justify-content-end">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                      <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <!-- End Modal tambah -->

            <div class="card-body">
              <div class="table-responsive">
                <table id="example1" class="table table-striped table-valign-middle table-hover">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Siswa</th>
                      <th>Semester</th>
                      <th>Diinput Oleh</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $no = 0; ?>
                    @foreach($data_pp as $pp)
                    <?php $no++; ?>
                    <tr>
                      <td>{{$no}}</td>
                      <td>{{$pp->siswa->nama_lengkap}}</td>
                      <td>{{$pp->semester}}</td>
                      <td>{{$pp->guru->nama_lengkap ?? '-'}}</td>
                      <td>
                        <a href="{{ route('personalprogram.edit', $pp->id) }}" class="btn btn-warning btn-sm mt-1">
                          <i class="fas fa-pencil-alt"></i>
                        </a>
                        <form action="{{ route('personalprogram.destroy', $pp->id) }}" method="POST" style="display:inline-block">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger btn-sm mt-1" onclick="return confirm('Hapus data ini?')">
                            <i class="fas fa-trash-alt"></i>
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


