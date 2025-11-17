<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>{{$title}}</title>
  <style>
    @page { margin: 25mm; }
    body {
      font-family: "Times New Roman", serif;
      font-size: 12pt;
      line-height: 1.4;
      position: relative;
      background-image: url('{{ str_replace('\\', '/', public_path('assets/images/logo/bg_raport.png')) }}');
      background-repeat: no-repeat;
      background-position: center center;
      background-size: 80% auto;
    }

    /* Watermark sebagai layer belakang */
 

    .header-title {
      font-size: 18pt;
      font-weight: bold;
      text-align: left;
      margin-bottom: 10px;
    }

    table { width: 100%; border-collapse: collapse; }

    .info td { padding: 2px 0; }

    .box {
      border: 1.5px solid #000;
      margin-top: 10px;
    }
    .box th {
      border: 1.5px solid #000;
      padding: 6px;
      font-weight: bold;
      text-align: center;
      background: #fff;
    }
    .box td {
      border: 1px solid #000;
      padding: 8px;
      vertical-align: top;
    }

    .signature {
      margin-top: 35px;
      width: 100%;
      text-align: center;
    }
  </style>
</head>

<body>


<div class="header-title">Laporan Program Personal</div>

<table class="info">
  <tr>
    <td style="width: 18%;">Nama</td><td style="width:2%;">:</td>
    <td style="width:30%;">{{ ucwords(strtolower($siswa->nama_lengkap)) }}</td>
    <td style="width:18%;">Semester</td><td style="width:2%;">:</td>
    <td style="width:30%;">{{$semester}}</td>
  </tr>
  <tr>
    <td>Kelas/Program</td><td>:</td>
    <td>{{$siswa->kelas->nama_kelas ?? '-'}}</td>
    <td>Tahun Ajaran</td><td>:</td>
    <td>{{$siswa->kelas->tapel->tahun_pelajaran ?? '-'}}</td>
  </tr>
</table>

<table class="box">
  <tr>
    <th style="width:50%;">Laporan Program Personal {{ ucwords(strtolower($siswa->nama_lengkap)) }}</th>
    <th style="width:50%;">Evaluasi Perkembangan Personal {{ ucwords(strtolower($siswa->nama_lengkap)) }}</th>
  </tr>
  <tr>
    <td>
      <ol style="margin:0; padding-left:18px;">
        <li><strong>Motorik Kasar</strong>, {{$pp->motorik_kasar ?? '-'}}</li>
        <li><strong>Sosialisasi</strong>, {{$pp->sosialisasi ?? '-'}}</li>
        <li><strong>Rentang Akademis</strong>, {{$pp->rentang_akademis ?? '-'}}</li>
      </ol>
    </td>
    <td>
      <ol style="margin:0; padding-left:18px;">
        <li><strong>Sosialisasi</strong>, {{$pp->evaluasi_sosialisasi ?? '-'}}</li>
        <li><strong>Rentang Akademis</strong>, {{$pp->evaluasi_rentang_akademis ?? '-'}}</li>
      </ol>
    </td>
  </tr>
</table>

<table class="signature">
  <tr>
    <td style="text-align:left; width:50%;"></td>
    <td style="text-align:right; width:50%;">
      {{$siswa->kelas->tapel->kota ?? ''}}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
    </td>
  </tr>

  <tr>
    <td style="padding-top:50px; text-align:left;">
      Guru Kelas<br><br><strong>({{ $pp->guru->nama_lengkap ? ucwords(strtolower($pp->guru->nama_lengkap)) : '-' }})</strong>
    </td>

    <td style="padding-top:50px; text-align:right;">
      Orang Tua / Wali<br><br><strong>(....................)</strong>
    </td>
  </tr>

  <tr>
    <td colspan="2" style="text-align:center; padding-top:40px;">
      Mengetahui,<br>Kepala Sekolah<br><br><br>
      <strong>( Ni Made Desi Handayani, S.Pd.I, MM. )</strong>
    </td>
  </tr>
</table>

</body>
</html>
