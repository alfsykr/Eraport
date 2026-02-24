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
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list-alt mr-2"></i>{{$title}}</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="text-center" style="width:5%">No</th>
                                <th>Mata Pelajaran</th>
                                <th class="text-center">Kelas</th>
                                <th class="text-center">Jumlah Rencana</th>
                                <th class="text-center" style="width:25%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data_pembelajaran as $i => $pembelajaran)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>{{ $pembelajaran->mapel->nama_mapel }}</td>
                                    <td class="text-center">{{ $pembelajaran->kelas->nama_kelas }}</td>
                                    <td class="text-center">
                                        @if($pembelajaran->jumlah_rencana > 0)
                                            <span class="badge badge-success">{{ $pembelajaran->jumlah_rencana }}
                                                indikator</span>
                                        @else
                                            <span class="badge badge-danger">Belum ada</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('rencanakisi.create') }}" method="GET"
                                            style="display:inline">
                                            <input type="hidden" name="pembelajaran_id" value="{{ $pembelajaran->id }}">
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Kelola
                                            </button>
                                        </form>
                                        @if($pembelajaran->bisa_import)
                                            <form action="{{ route('rencanakisi.import') }}" method="POST"
                                                style="display:inline; margin-left:4px">
                                                @csrf
                                                <input type="hidden" name="pembelajaran_id" value="{{ $pembelajaran->id }}">
                                                <button type="submit" class="btn btn-sm btn-warning"
                                                    onclick="return confirm('Import otomatis dari data KD lama?')">
                                                    <i class="fas fa-file-import"></i> Import KD Lama
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Tidak ada data pembelajaran</td>
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

</body>

</html>