<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>{{$title}}</title>
  <style>
    @page { margin: 25px; }
    body {
      font-family: "Times New Roman", Times, serif;
      font-size: 12pt;
      background-image: url('{{ str_replace('\\', '/', public_path('assets/images/logo/bg_raport.png')) }}');
      background-repeat: no-repeat;
      background-position: center center;
      background-size: 80% auto;
    }
    h2, h3, h4 { text-align: center; margin: 6px 0; }
    table { width: 100%; border-collapse: collapse; }
    .info td { padding: 2px 0; vertical-align: top; }
    .box {
      border: 1px solid #000;
      margin-top: 12px;
    }
    .box th, .box td {
      border: 1px solid #000;
      padding: 6px;
      vertical-align: top;
    }
    .box th { background-color: #f0f0f0; text-align: center; }
    .signature { margin-top: 40px; }
    .signature td { vertical-align: top; }
  </style>
</head>
<body>
  <h2>Laporan Kisi-Kisi Kompetensi</h2>
  <table class="info">
    <tr>
      <td style="width: 18%;">Nama Siswa</td>
      <td style="width: 2%;">:</td>
      <td style="width: 30%;">{{ ucwords(strtolower($anggota_kelas->siswa->nama_lengkap)) }}</td>
      <td style="width: 20%;">Semester</td>
      <td style="width: 2%;">:</td>
      <td style="width: 28%;">{{$anggota_kelas->kelas->tapel->semester == 1 ? 'Ganjil' : 'Genap'}}</td>
    </tr>
    <tr>
      <td>Kelas/Program</td>
      <td>:</td>
      <td>{{$anggota_kelas->kelas->nama_kelas}}</td>
      <td>Tahun Ajaran</td>
      <td>:</td>
      <td>{{$anggota_kelas->kelas->tapel->tahun_pelajaran}}</td>
    </tr>
    <tr>
      <td>NIS / NISN</td>
      <td>:</td>
      <td>{{$anggota_kelas->siswa->nis}} / {{$anggota_kelas->siswa->nisn}}</td>
      <td>Tanggal</td>
      <td>:</td>
      <td>{{$tanggal_raport->tanggal_pembagian->isoFormat('D MMMM Y')}}</td>
    </tr>
  </table>

  @foreach($data_mapel as $mapel)
  <h3>{{$mapel['mapel']}}</h3>

  @if(count($mapel['pengetahuan']) > 0)
  <h4>Pengetahuan</h4>
  <table class="box">
    <thead>
      <tr>
        <th style="width: 6%;">No</th>
        <th style="width: 14%;">Kode KD</th>
        <th>Kompetensi Dasar</th>
        <th style="width: 12%;">Nilai</th>
      </tr>
    </thead>
    <tbody>
      <?php $no = 0; ?>
      @foreach($mapel['pengetahuan'] as $kd)
      <?php $no++; ?>
      <tr>
        <td style="text-align: center;">{{$no}}</td>
        <td style="text-align: center;">{{$kd['kode']}}</td>
        <td>{!! nl2br($kd['kompetensi']) !!}</td>
        <td style="text-align: center;">{{$kd['nilai']}}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endif

  @if(count($mapel['keterampilan']) > 0)
  <h4 style="margin-top: 12px;">Keterampilan</h4>
  <table class="box">
    <thead>
      <tr>
        <th style="width: 6%;">No</th>
        <th style="width: 14%;">Kode KD</th>
        <th>Kompetensi Dasar</th>
        <th style="width: 12%;">Nilai</th>
      </tr>
    </thead>
    <tbody>
      <?php $no = 0; ?>
      @foreach($mapel['keterampilan'] as $kd)
      <?php $no++; ?>
      <tr>
        <td style="text-align: center;">{{$no}}</td>
        <td style="text-align: center;">{{$kd['kode']}}</td>
        <td>{!! nl2br($kd['kompetensi']) !!}</td>
        <td style="text-align: center;">{{$kd['nilai']}}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endif
  @endforeach

  <table class="signature">
    <tr>
      <td style="width: 35%;">
        Mengetahui <br>
        Orang Tua / Wali, <br><br><br><br>
        (................................)
      </td>
      <td style="width: 30%;"></td>
      <td style="width: 35%;">
         {{$tanggal_raport->tanggal_pembagian->isoFormat('D MMMM Y')}}<br>
        Guru Kelas,<br><br><br><br>
        <b><u>{{$anggota_kelas->kelas->guru->nama_lengkap}}, {{$anggota_kelas->kelas->guru->gelar}}</u></b><br>
        NIP. {{konversi_nip($anggota_kelas->kelas->guru->nip)}}
      </td>
    </tr>
    <tr>
      <td></td>
      <td style="text-align: center;">
        Mengetahui <br>
        Kepala Sekolah,<br><br><br><br>
        <b><u>{{$sekolah->kepala_sekolah}}</u></b><br>
        NIP. {{konversi_nip($sekolah->nip_kepala_sekolah)}}
      </td>
      <td></td>
    </tr>
  </table>
</body>
</html>


