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
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">{{$title}}</li>
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
                            <h3 class="card-title"><i class="fas fa-paper-plane"></i> {{$title}}</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="status-table" class="table table-bordered table-striped table-hover">
                                    <thead class="bg-info text-white">
                                        <tr>
                                            <th style="width:5%">No</th>
                                            <th>Mata Pelajaran</th>
                                            <th>Kelas</th>
                                            <th class="text-center">Jumlah Siswa</th>
                                            <th class="text-center">Jumlah Kisi</th>
                                            <th class="text-center">Nilai Terkirim</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 0; ?>
                                        @forelse($data_pembelajaran as $pembelajaran)
                                            <?php    $no++; ?>
                                            <tr>
                                                <td class="text-center">{{$no}}</td>
                                                <td>{{$pembelajaran->mapel->nama_mapel}}</td>
                                                <td>{{$pembelajaran->kelas->nama_kelas}}</td>
                                                <td class="text-center">{{$pembelajaran->jumlah_siswa}}</td>
                                                <td class="text-center">
                                                    @if($pembelajaran->jumlah_kisi > 0)
                                                        <span class="badge badge-info">{{$pembelajaran->jumlah_kisi}}
                                                            kisi</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <strong>{{$pembelajaran->sudah_kirim}}</strong> /
                                                    {{$pembelajaran->jumlah_siswa}}
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge {{$pembelajaran->status_class}} px-3 py-1"
                                                        style="font-size:0.85em;">
                                                        {{$pembelajaran->status_label}}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('kirimnilaiakhir.index') }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fas fa-paper-plane"></i> Kirim Nilai
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">Tidak ada data mata pelajaran
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Legenda Status -->
                            <div class="mt-3">
                                <small class="text-muted">
                                    <strong>Keterangan Status:</strong>
                                    <span class="badge badge-success ml-2">Sudah Terkirim</span> = Semua siswa sudah
                                    mempunyai nilai akhir
                                    <span class="badge badge-warning ml-2">Sebagian Terkirim</span> = Sebagian siswa
                                    sudah mempunyai nilai akhir
                                    <span class="badge badge-danger ml-2">Belum Terkirim</span> = Belum ada nilai akhir
                                    yang dikirim
                                    <span class="badge badge-secondary ml-2">Belum Ada Kisi</span> = Rencana penilaian
                                    kisi-kisi belum dibuat
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('layouts.main.footer')