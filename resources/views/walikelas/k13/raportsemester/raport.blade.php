<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>{{ $title }} | {{ $anggota_kelas->siswa->nama_lengkap }} ({{ $anggota_kelas->siswa->nis }})</title>
    <link href="./assets/invoice_raport.css" rel="stylesheet">
    <style>
        @page {
            margin: 20px;
        }

        body {
            background-image: url('{{ str_replace('\\', '/', public_path('assets/images/logo/bg_raport.png')) }}');
            background-repeat: no-repeat;
            background-position: center center;
            background-size: 80% auto;
        }
    </style>
</head>

<body>
    <!-- Page 1: Nilai Hasil Belajar -->
    <div class="invoice-box">
        <div class="header">
            <table>
                <tr>
                    <td style="width: 19%;">Nama Sekolah</td>
                    <td style="width: 52%;">: {{ $sekolah->nama_sekolah }}</td>
                    <td style="width: 16%;">Kelas</td>
                    <td style="width: 13%;">: {{ $anggota_kelas->kelas->nama_kelas }}</td>
                </tr>
                <tr>
                    <td style="width: 19%;">Alamat</td>
                    <td style="width: 52%;">: {{ $sekolah->alamat }}</td>
                    <td style="width: 16%;">Semester</td>
                    <td style="width: 13%;">:
                        @if ($anggota_kelas->kelas->tapel->semester == 1)
                            1 (Ganjil)
                        @else
                            2 (Genap)
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 19%;">Nama Peserta Didik</td>
                    <td style="width: 52%;">: {{ ucwords(strtolower($anggota_kelas->siswa->nama_lengkap)) }} </td>
                    <td style="width: 16%;">Tahun Pelajaran</td>
                    <td style="width: 13%;">: {{ $anggota_kelas->kelas->tapel->tahun_pelajaran }}</td>
                </tr>
                <tr>
                    <td style="width: 19%;">Nomor Induk/NISN</td>
                    <td style="width: 52%;">: {{ $anggota_kelas->siswa->nis }} / {{ $anggota_kelas->siswa->nisn }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="content">
            <table cellspacing="0">
                <tr>
                    <td colspan="5" style="height: 30px;"><strong>NILAI HASIL BELAJAR</strong></td>
                </tr>
                <tr class="heading">
                    <td style="width: 5%;">NO</td>
                    <td style="width: 45%;">Mata Pelajaran</td>
                    <td style="width: 10%;">KKM</td>
                    <td style="width: 25%;">Nilai Akhir</td>
                    <td style="width: 15%;">Predikat</td>
                </tr>

                <?php $no = 0; ?>
                @foreach ($data_nilai_mapel as $nilai)
                    <?php    $no++; ?>
                    <tr class="nilai">
                        <td class="center">{{ $no }}</td>
                        <td>{{ $nilai['nama_mapel'] }}</td>
                        <td class="center">{{ $nilai['kkm'] }}</td>
                        <td class="center">{{ $nilai['nilai_akhir'] }}</td>
                        <td class="center">{{ $nilai['predikat_akhir'] }}</td>
                    </tr>
                @endforeach

                <tr class="nilai">
                    <td colspan="3"><strong>Jumlah</strong></td>
                    <td class="center"><strong>{{ $total_nilai_akhir }}</strong></td>
                    <td class="center"></td>
                </tr>
                <tr class="nilai">
                    <td colspan="3"><strong>Rata-rata</strong></td>
                    <td class="center"><strong>{{ $rata_rata_nilai_akhir }}</strong></td>
                    <td class="center"></td>
                </tr>
            </table>
        </div>

        <div
            style="padding-left:60%; padding-top:1rem; font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;">
            {{ $anggota_kelas->kelas->tapel->k13_tgl_raport->tempat_penerbitan }},
            {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}<br>
            Wali Kelas, <br><br><br><br>
            <b><u>{{ $anggota_kelas->kelas->guru->nama_lengkap }},
                    {{ $anggota_kelas->kelas->guru->gelar }}</u></b><br>
            NIP. {{ konversi_nip($anggota_kelas->kelas->guru->nip) }}
        </div>
        <div class="footer">
            <i>{{ $anggota_kelas->kelas->nama_kelas }} | {{ $anggota_kelas->siswa->nama_lengkap }} |
                {{ $anggota_kelas->siswa->nis }}</i> <b style="float: right;"><i>Halaman 1</i></b>
        </div>
    </div>
    <div class="page-break"></div>

    <!-- Page 2: Prestasi, Ketidakhadiran, dan Lainnya -->
    <div class="invoice-box">
        <div class="header">
            <table>
                <tr>
                    <td style="width: 19%;">Nama Sekolah</td>
                    <td style="width: 52%;">: {{ $sekolah->nama_sekolah }}</td>
                    <td style="width: 16%;">Kelas</td>
                    <td style="width: 13%;">: {{ $anggota_kelas->kelas->nama_kelas }}</td>
                </tr>
                <tr>
                    <td style="width: 19%;">Alamat</td>
                    <td style="width: 52%;">: {{ $sekolah->alamat }}</td>
                    <td style="width: 16%;">Semester</td>
                    <td style="width: 13%;">:
                        @if ($anggota_kelas->kelas->tapel->semester == 1)
                            1 (Ganjil)
                        @else
                            2 (Genap)
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 19%;">Nama Peserta Didik</td>
                    <td style="width: 52%;">: {{ $anggota_kelas->siswa->nama_lengkap }} </td>
                    <td style="width: 16%;">Tahun Pelajaran</td>
                    <td style="width: 13%;">: {{ $anggota_kelas->kelas->tapel->tahun_pelajaran }}</td>
                </tr>
                <tr>
                    <td style="width: 19%;">Nomor Induk/NISN</td>
                    <td style="width: 52%;">: {{ $anggota_kelas->siswa->nis }} / {{ $anggota_kelas->siswa->nisn }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="content">
            <table cellspacing="0">

                <!-- A. Talents Mapping (was: Prestasi) -->
                <tr>
                    <td colspan="4" style="height: 25px; padding-top: 5px"><strong>A. TALENTS MAPPING</strong></td>
                </tr>
                <tr class="heading">
                    <td style="width: 5%;">NO</td>
                    <td style="width: 28%;">Jenis Bakat</td>
                    <td colspan="2">Keterangan</td>
                </tr>
                @if (count($data_prestasi_siswa) == 0)
                    <tr class="nilai">
                        <td class="center">1</td>
                        <td></td>
                        <td colspan="2" class="description"><span></span></td>
                    </tr>
                    <tr class="nilai">
                        <td class="center">2</td>
                        <td></td>
                        <td colspan="2" class="description"><span></span></td>
                    </tr>
                @elseif(count($data_prestasi_siswa) == 1)
                    <?php    $no = 0; ?>
                    @foreach ($data_prestasi_siswa as $prestasi)
                        <?php        $no++; ?>
                        <tr class="nilai">
                            <td class="center">{{ $no }}</td>
                            <td>
                                @if ($prestasi->jenis_prestasi == 1)
                                    Akademik
                                @elseif($prestasi->jenis_prestasi == 2)
                                    Non Akademik
                                @endif
                            </td>
                            <td colspan="2" class="description">
                                <span>{!! nl2br($prestasi->deskripsi) !!}</span>
                            </td>
                        </tr>
                    @endforeach
                    <tr class="nilai">
                        <td class="center">2</td>
                        <td></td>
                        <td colspan="2" class="description"><span></span></td>
                    </tr>
                @else
                    <?php    $no = 0; ?>
                    @foreach ($data_prestasi_siswa as $prestasi)
                        <?php        $no++; ?>
                        <tr class="nilai">
                            <td class="center">{{ $no }}</td>
                            <td>
                                @if ($prestasi->jenis_prestasi == 1)
                                    Akademik
                                @elseif($prestasi->jenis_prestasi == 2)
                                    Non Akademik
                                @endif
                            </td>
                            <td colspan="2" class="description">
                                <span>{!! nl2br($prestasi->deskripsi) !!}</span>
                            </td>
                        </tr>
                    @endforeach
                @endif
                <!-- End Talents Mapping -->

                <!-- B. Catatan (was: Ekstrakulikuler) -->
                <tr>
                    <td colspan="4" style="height: 25px; padding-top: 5px"><strong>B. CATATAN</strong></td>
                </tr>
                <tr class="sikap">
                    <td colspan="4" class="description" style="height: 60px;">
                        @if (!is_null($catatan_wali_kelas))
                            <i><b>{{ $catatan_wali_kelas->catatan }}</b></i>
                        @endif
                    </td>
                </tr>
                <!-- End Catatan -->

                <!-- C. Ketidakhadiran -->
                <tr>
                    <td colspan="4" style="height: 25px; padding-top: 5px"><strong>C. KETIDAKHADIRAN</strong></td>
                </tr>
                @if (!is_null($kehadiran_siswa))
                    <tr class="nilai">
                        <td colspan="2" style="border-right:0 ;">Sakit</td>
                        <td style="border-left:0 ;">: {{ $kehadiran_siswa->sakit }} hari</td>
                        <td class="false"></td>
                    </tr>
                    <tr class="nilai">
                        <td colspan="2" style="border-right:0 ;">Izin</td>
                        <td style="border-left:0 ;">: {{ $kehadiran_siswa->izin }} hari</td>
                        <td class="false"></td>
                    </tr>
                    <tr class="nilai">
                        <td colspan="2" style="border-right:0 ;">Tanpa Keterangan</td>
                        <td style="border-left:0 ;">: {{ $kehadiran_siswa->tanpa_keterangan }} hari</td>
                        <td class="false"></td>
                    </tr>
                @else
                    <tr class="nilai">
                        <td colspan="4"><b>Data kehadiran belum diinput</b></td>
                    </tr>
                @endif
                <!-- End Ketidakhadiran -->

                <!-- Keputusan (tampil selalu, bukan hanya semester genap) -->
                <tr>
                    <td colspan="4" style="height: 15px; padding-top: 8px; border: none;"></td>
                </tr>
                <tr>
                    <td colspan="4" style="padding: 8px 4px; border: none; line-height: 1.8;">
                        <strong>Keputusan :</strong> Dengan mempertimbangkan<br>
                        Hasil yang dicapai pada semester 1 dan 2<br>
                        Maka Ananda
                        <strong>{{ ucwords(strtolower($anggota_kelas->siswa->nama_lengkap)) }}</strong>
                        ditetapkan :
                        @if (!is_null($anggota_kelas->kenaikan_kelas))
                            @if ($anggota_kelas->kenaikan_kelas->keputusan == 1)
                                <strong>Naik</strong> / <s>Tidak Naik</s>
                            @elseif($anggota_kelas->kenaikan_kelas->keputusan == 2)
                                <s>Naik</s> / <strong>Tidak Naik</strong>
                            @elseif($anggota_kelas->kenaikan_kelas->keputusan == 3)
                                <strong>Lulus</strong>
                            @elseif($anggota_kelas->kenaikan_kelas->keputusan == 4)
                                <strong>Tidak Lulus</strong>
                            @endif
                            <br>
                            Ke kelas : <strong>{{ $anggota_kelas->kenaikan_kelas->kelas_tujuan }}</strong>
                        @else
                            <strong>Naik</strong> / <s>Tidak Naik</s><br>
                            Ke kelas : <strong>-</strong>
                        @endif
                    </td>
                </tr>
                <!-- End Keputusan -->

            </table>
        </div>

        <div style="padding-top:1rem; font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;">
            <table>
                <tr>
                    <td style="width: 30%;">
                        Mengetahui <br>
                        Orang Tua/Wali, <br><br><br><br>
                        .............................
                    </td>
                    <td style="width: 35%;"></td>
                    <td style="width: 35%;">
                        {{ $anggota_kelas->kelas->tapel->k13_tgl_raport->tempat_penerbitan }},
                        {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}<br>
                        Wali Kelas, <br><br><br><br>
                        <b><u>{{ $anggota_kelas->kelas->guru->nama_lengkap }},
                                {{ $anggota_kelas->kelas->guru->gelar }}</u></b><br>
                        NIP. {{ konversi_nip($anggota_kelas->kelas->guru->nip) }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 30%;"></td>
                    <td style="width: 35%;">
                        Mengetahui <br>
                        Kepala Sekolah, <br><br><br><br>
                        <b><u>{{ $sekolah->kepala_sekolah }}</u></b><br>
                        NIP. {{ konversi_nip($sekolah->nip_kepala_sekolah) }}
                    </td>
                    <td style="width: 35%;"></td>
                </tr>
            </table>
        </div>
        <div class="footer">
            <i>{{ $anggota_kelas->kelas->nama_kelas }} | {{ $anggota_kelas->siswa->nama_lengkap }} |
                {{ $anggota_kelas->siswa->nis }}</i> <b style="float: right;"><i>Halaman 2</i></b>
        </div>
    </div>

</body>

</html>