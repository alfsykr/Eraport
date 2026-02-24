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
                        <li class="breadcrumb-item active">{{$title}}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-edit mr-2"></i>{{$title}}</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="text-center" style="width:5%">No</th>
                                <th>Mata Pelajaran</th>
                                <th class="text-center">Kelas</th>
                                <th class="text-center" title="Jumlah indikator/kisi-kisi yang direncanakan">
                                    Rencana<br>Kisi-Kisi</th>
                                <th class="text-center" title="Jumlah kisi-kisi yang sudah dinilai semua siswa">
                                    Kisi-Kisi<br>Dinilai</th>
                                <th class="text-center" title="Jumlah siswa yang semua kisi-kisinya sudah terisi">
                                    Siswa<br>Lengkap</th>
                                <th class="text-center" style="width:15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data_pembelajaran as $i => $pembelajaran)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>{{ $pembelajaran->mapel->nama_mapel }}</td>
                                    <td class="text-center">{{ $pembelajaran->kelas->nama_kelas }}</td>

                                    {{-- Kolom Rencana Kisi-Kisi --}}
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $pembelajaran->jumlah_rencana }}</span>
                                    </td>

                                    {{-- Kolom Kisi-Kisi Dinilai (semua siswa sudah dinilai per kisi) --}}
                                    <td class="text-center">
                                        @if($pembelajaran->jumlah_rencana > 0)
                                            @if($pembelajaran->telah_dinilai >= $pembelajaran->jumlah_rencana)
                                                <span class="badge badge-success">Lengkap</span>
                                            @else
                                                <span class="badge badge-warning">
                                                    {{ $pembelajaran->telah_dinilai }}/{{ $pembelajaran->jumlah_rencana }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge badge-secondary">-</span>
                                        @endif
                                    </td>

                                    {{-- Kolom Siswa Lengkap (siswa yang semua kisi-kisinya terisi) --}}
                                    <td class="text-center">
                                        @if($pembelajaran->jumlah_rencana > 0)
                                            @if($pembelajaran->siswa_lengkap >= $pembelajaran->jumlah_siswa && $pembelajaran->jumlah_siswa > 0)
                                                <span class="badge badge-success p-2">
                                                    <i class="fas fa-check mr-1"></i>
                                                    {{ $pembelajaran->siswa_lengkap }}/{{ $pembelajaran->jumlah_siswa }}
                                                </span>
                                            @elseif($pembelajaran->siswa_lengkap > 0)
                                                <span class="badge badge-warning p-2">
                                                    {{ $pembelajaran->siswa_lengkap }}/{{ $pembelajaran->jumlah_siswa }}
                                                </span>
                                            @else
                                                <span class="badge badge-danger p-2">
                                                    0/{{ $pembelajaran->jumlah_siswa }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge badge-secondary">-</span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="text-center">
                                        @if($pembelajaran->jumlah_rencana > 0)
                                            <form action="{{ route('nilaikisi.create') }}" method="GET">
                                                <input type="hidden" name="pembelajaran_id" value="{{ $pembelajaran->id }}">
                                                <button type="submit" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i> Input Nilai
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted small">Buat rencana dulu</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Tidak ada data pembelajaran</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <small class="text-muted">
                        <span class="badge badge-success">✓</span> Semua terisi &nbsp;
                        <span class="badge badge-warning">!</span> Sebagian &nbsp;
                        <span class="badge badge-danger">✗</span> Belum ada &nbsp;
                        <i class="fas fa-info-circle text-info"></i>
                        <strong>Siswa Lengkap</strong>: jumlah siswa yang <em>semua</em> kisi-kisinya sudah diisi nilai
                    </small>
                </div>
            </div>
        </div>
    </section>
</div>

@include('layouts.main.footer')

</body>

</html>