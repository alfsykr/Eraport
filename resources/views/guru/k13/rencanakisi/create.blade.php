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
                        <li class="breadcrumb-item"><a href="{{ route('rencanakisi.index') }}">Rencana Penilaian</a>
                        </li>
                        <li class="breadcrumb-item active">Kelola</li>
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

            {{-- Form tambah rencana --}}
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-plus mr-2"></i>Tambah Indikator Penilaian</h3>
                </div>
                <div class="card-body">

                    {{-- Tab Pilihan --}}
                    <ul class="nav nav-tabs mb-3" id="tabTambah" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-pilih" data-toggle="tab" href="#panel-pilih" role="tab">
                                <i class="fas fa-list mr-1"></i> Pilih Kisi-Kisi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-baru" data-toggle="tab" href="#panel-baru" role="tab">
                                <i class="fas fa-pencil-alt mr-1"></i> Buat Kisi-Kisi Baru
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">

                        {{-- ===== TAB 1: PILIH DARI KISI-KISI YANG ADA ===== --}}
                        <div class="tab-pane fade show active" id="panel-pilih" role="tabpanel">
                            @if($data_kd->count() > 0)
                                <form action="{{ route('rencanakisi.store') }}" method="POST" id="form-pilih">
                                    @csrf
                                    <input type="hidden" name="pembelajaran_id" value="{{ $pembelajaran->id }}">
                                    <input type="hidden" name="mode" value="pilih">

                                    <div class="form-group">
                                        <label>Pilih Kisi-Kisi <span class="text-danger">*</span></label>
                                        <select name="k13_kd_mapel_id" class="form-control select2" id="select-kd"
                                            style="width:100%;" required onchange="isiDeskripsiDariKD(this)">
                                            <option value="">-- Pilih Kisi-Kisi --</option>
                                            @foreach($data_kd as $kd)
                                                <option value="{{ $kd->id }}" data-deskripsi="{{ $kd->kompetensi_dasar }}">
                                                    {{ Str::limit($kd->kompetensi_dasar, 80) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Pilih dari daftar kisi-kisi yang sudah tersedia untuk mata
                                            pelajaran ini</small>
                                    </div>

                                    <div class="form-group" id="preview-deskripsi" style="display:none;">
                                        <label>Deskripsi Kisi-Kisi</label>
                                        <textarea name="deskripsi_penilaian" class="form-control" rows="3"
                                            id="textarea-pilih" readonly style="background:#f8f9fa;"></textarea>
                                        <small class="text-muted">Deskripsi otomatis dari kisi-kisi yang dipilih. Bisa
                                            diedit jika perlu.</small>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-plus mr-1"></i>Tambah
                                    </button>
                                    <a href="{{ route('rencanakisi.index') }}" class="btn btn-secondary ml-2">Kembali</a>
                                </form>
                            @else
                                <div class="callout callout-warning">
                                    <p><i class="fas fa-exclamation-triangle mr-1"></i>
                                        Belum ada kisi-kisi yang tersedia untuk mata pelajaran ini.
                                        Silakan gunakan tab <strong>"Buat Kisi-Kisi Baru"</strong> atau minta admin untuk
                                        menambahkan kisi-kisi terlebih dahulu.
                                    </p>
                                </div>
                                <a href="{{ route('rencanakisi.index') }}" class="btn btn-secondary">Kembali</a>
                            @endif
                        </div>

                        {{-- ===== TAB 2: BUAT KISI-KISI BARU MANUAL ===== --}}
                        <div class="tab-pane fade" id="panel-baru" role="tabpanel">
                            <form action="{{ route('rencanakisi.store') }}" method="POST" id="form-baru">
                                @csrf
                                <input type="hidden" name="pembelajaran_id" value="{{ $pembelajaran->id }}">
                                <input type="hidden" name="mode" value="baru">

                                <div class="form-group">
                                    <label>Deskripsi Kisi-Kisi <span class="text-danger">*</span></label>
                                    <textarea name="deskripsi_penilaian" class="form-control" rows="3"
                                        placeholder="Contoh: Menonton film kisah Nabi Muhammad SAW tentang Isra Mi'raj"
                                        required></textarea>
                                    <small class="text-muted">Tuliskan deskripsi kompetensi/indikator yang akan
                                        dinilai</small>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus mr-1"></i>Tambah
                                </button>
                                <a href="{{ route('rencanakisi.index') }}" class="btn btn-secondary ml-2">Kembali</a>
                            </form>
                        </div>

                    </div>
                    {{-- end tab-content --}}

                </div>
            </div>

            {{-- Daftar rencana yang sudah ada --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list mr-2"></i>Daftar Indikator Penilaian</h3>
                    <div class="card-tools">
                        <span class="badge badge-info">{{ $data_rencana->count() }} indikator</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="bg-success text-white">
                            <tr>
                                <th class="text-center" style="width:5%">No</th>
                                <th>Deskripsi Indikator / Kisi-Kisi</th>
                                <th class="text-center" style="width:10%">Hapus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data_rencana as $i => $rencana)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>{{ $rencana->deskripsi_penilaian }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('rencanakisi.destroy', $rencana->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus indikator ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">Belum ada indikator penilaian</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
</div>

@include('layouts.main.footer')

<script>
    function isiDeskripsiDariKD(select) {
        var deskripsi = select.options[select.selectedIndex].getAttribute('data-deskripsi');
        var preview = document.getElementById('preview-deskripsi');
        var textarea = document.getElementById('textarea-pilih');

        if (deskripsi && select.value !== '') {
            textarea.value = deskripsi;
            textarea.removeAttribute('readonly');
            preview.style.display = 'block';
        } else {
            textarea.value = '';
            preview.style.display = 'none';
        }
    }
</script>

</body>

</html>