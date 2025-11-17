<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>{{$title}} | {{$anggota_kelas->siswa->nama_lengkap}}</title>
  <style>
    body { font-family: DejaVu Sans, Helvetica, Arial, sans-serif; font-size: 12px; }
    .title { font-size: 20px; font-weight: 700; margin-bottom: 10px; }
    .table { width: 100%; border-collapse: collapse; }
    .table td, .table th { border: 1px solid #000; padding: 8px; vertical-align: top; }
    .heading { background: #eee; font-weight: 700; text-align: center; }
    .no-border td { border: 0; }
  </style>
  <link href="./assets/invoice_raport.css" rel="stylesheet">
  <!-- optional watermark bg if needed by existing css -->
  </head>
<body>

  <div class="title">Laporan Program Personal</div>

  <table class="table no-border">
    <tr>
      <td>Nama</td><td>: {{$anggota_kelas->siswa->nama_lengkap}}</td>
      <td>Semester</td><td>: {{$anggota_kelas->kelas->tapel->semester == 1 ? 'I / Ganjil' : 'II / Genap'}}</td>
    </tr>
    <tr>
      <td>Kelas/program</td><td>: {{$anggota_kelas->kelas->nama_kelas}}</td>
      <td>Tahun Ajaran</td><td>: {{$anggota_kelas->kelas->tapel->tahun_pelajaran}}</td>
    </tr>
    <tr>
      <td>Tanggal Minggu</td><td>: {{$tanggal}}</td>
      <td></td><td></td>
    </tr>
  </table>

  <table class="table" style="margin-top:10px;">
    <tr>
      <th class="heading" style="width:50%">Laporan Program Personal Ananda {{$anggota_kelas->siswa->nama_lengkap}}</th>
      <th class="heading" style="width:50%">Evaluasi Perkembangan Personal Ananda {{$anggota_kelas->siswa->nama_lengkap}}</th>
    </tr>
    <tr>
      <td>
        <ol>
          <li><b>Motorik Kasar,</b> {!! nl2br(e(optional($progress)->motorik_kasar)) !!}</li>
          <li><b>Sosialisasi,</b> {!! nl2br(e(optional($progress)->sosialisasi)) !!}</li>
          <li><b>Rentang Akademis</b> {!! nl2br(e(optional($progress)->rentang_akademis)) !!}</li>
        </ol>
      </td>
      <td>
        <ol>
          <li><b>Sosialisasi,</b> {!! nl2br(e(optional($progress)->evaluasi_sosialisasi)) !!}</li>
          <li><b>Rentang Akademis,</b> {!! nl2br(e(optional($progress)->evaluasi_rentang_akademis)) !!}</li>
        </ol>
      </td>
    </tr>
  </table>

  <table class="table no-border" style="margin-top:20px;">
    <tr>
      <td style="width:50%"></td>
      <td style="width:50%; text-align:right;">Depok, {{ \Carbon\Carbon::parse($tanggal)->isoFormat('D MMMM Y') }}</td>
    </tr>
    <tr class="no-border">
      <td>
        Guru Kelas<br><br><br>
        <b>({{$anggota_kelas->kelas->guru->nama_lengkap}}, {{$anggota_kelas->kelas->guru->gelar}})</b>
      </td>
      <td style="text-align:right;">
        Orang Tua / Wali<br><br><br>
        (...................)
      </td>
    </tr>
  </table>

</body>
</html>


