@include('layouts.main.header')
@include('layouts.sidebar.guru')

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">{{$title}}</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item "><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('personalprogram.index') }}">Personal Program</a></li>
            <li class="breadcrumb-item active">Edit</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-edit"></i> {{$title}}</h3>
            </div>
            <form action="{{ route('personalprogram.update', $personalprogram->id) }}" method="POST">
              @csrf
              @method('PATCH')
              <div class="card-body">
                <div class="form-group row">
                  <label class="col-sm-2 col-form-label">Siswa</label>
                  <div class="col-sm-10">
                    <select class="form-control select2" name="siswa_id" style="width: 100%;" required>
                      @foreach($data_siswa as $s)
                      <option value="{{$s->id}}" @if($s->id==$personalprogram->siswa_id) selected @endif>{{$s->nama_lengkap}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-2 col-form-label">Semester</label>
                  <div class="col-sm-10">
                    <select class="form-control" name="semester" required>
                      <option value="Ganjil" @if($personalprogram->semester=='Ganjil') selected @endif>Ganjil</option>
                      <option value="Genap" @if($personalprogram->semester=='Genap') selected @endif>Genap</option>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label>Motorik Kasar</label>
                  <textarea class="form-control" name="motorik_kasar" rows="2">{{$personalprogram->motorik_kasar}}</textarea>
                </div>
                <div class="form-group">
                  <label>Sosialisasi</label>
                  <textarea class="form-control" name="sosialisasi" rows="2">{{$personalprogram->sosialisasi}}</textarea>
                </div>
                <div class="form-group">
                  <label>Rentang Akademis</label>
                  <textarea class="form-control" name="rentang_akademis" rows="2">{{$personalprogram->rentang_akademis}}</textarea>
                </div>
                <hr/>
                <div class="form-group">
                  <label>Evaluasi Motorik Kasar</label>
                  <textarea class="form-control" name="evaluasi_motorik_kasar" rows="2">{{$personalprogram->evaluasi_motorik_kasar}}</textarea>
                </div>
                <div class="form-group">
                  <label>Evaluasi Sosialisasi</label>
                  <textarea class="form-control" name="evaluasi_sosialisasi" rows="2">{{$personalprogram->evaluasi_sosialisasi}}</textarea>
                </div>
                <div class="form-group">
                  <label>Evaluasi Rentang Akademis</label>
                  <textarea class="form-control" name="evaluasi_rentang_akademis" rows="2">{{$personalprogram->evaluasi_rentang_akademis}}</textarea>
                </div>
              </div>
              <div class="card-footer text-right">
                <a href="{{ route('personalprogram.index') }}" class="btn btn-default">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

@include('layouts.main.footer')


