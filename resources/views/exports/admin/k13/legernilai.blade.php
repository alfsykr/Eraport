<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Leger K13 {{$kelas->nama_kelas}}</title>
</head>

<body>
  <table>
    <thead>
      <tr>
        <td colspan="9"><strong>LEGER NILAI SISWA KELAS {{$kelas->nama_kelas}}</strong></td>
      </tr>
      <tr>
        <td colspan="9"><strong>{{$sekolah->nama_sekolah}}</strong></td>
      </tr>
      <tr>
        <td colspan="9"><strong>TAHUN PELAJARAN {{$kelas->tapel->tahun_pelajaran}} SEMESTER
            {{$kelas->tapel->semester}}</strong></td>
      </tr>
      <tr>
        <td colspan="9">Waktu download : {{$time_download}}</td>
      </tr>
      <tr>
        <td colspan="9">Didownload oleh : {{Auth::user()->admin->nama_lengkap}} ({{Auth::user()->username}})</td>
      </tr>
    </thead>
    <tbody>
      <!-- Header -->
      <tr>
        <td rowspan="2" align="center" style="border: 1px solid #000000; background-color: #d9ecd0;"><strong>NO</strong>
        </td>
        <td rowspan="2" align="center" style="border: 1px solid #000000; background-color: #d9ecd0;">
          <strong>NIS</strong></td>
        <td rowspan="2" align="center" style="border: 1px solid #000000; background-color: #d9ecd0;"><strong>NAMA
            SISWA</strong></td>
        <td rowspan="2" align="center" style="border: 1px solid #000000; background-color: #d9ecd0;"><strong>Nilai
            Rata-rata</strong></td>
        <td colspan="3" align="center" style="border: 1px solid #000000; background-color: #d9ecd0;">
          <strong>Kehadiran</strong></td>
      </tr>
      <tr>
        <td align="center" style="border: 1px solid #000000; background-color: #d9ecd0;"><strong>S</strong></td>
        <td align="center" style="border: 1px solid #000000; background-color: #d9ecd0;"><strong>I</strong></td>
        <td align="center" style="border: 1px solid #000000; background-color: #d9ecd0;"><strong>A</strong></td>
      </tr>
      <!-- /Header -->

      <!-- Data -->
      <?php $no = 0; ?>
      @foreach($data_anggota_kelas->sortBy('siswa.nama_lengkap') as $anggota_kelas)
        <?php  $no++; ?>
        <tr>
          <td align="center" style="border: 1px solid #000000;">{{$no}}</td>
          <td align="center" style="border: 1px solid #000000;">{{$anggota_kelas->siswa->nis}}</td>
          <td style="border: 1px solid #000000;">{{$anggota_kelas->siswa->nama_lengkap}}</td>
          <td align="center" style="border: 1px solid #000000;"><strong>{{$anggota_kelas->rata_rata_semua}}</strong></td>

          @if(!is_null($anggota_kelas->kehadiran_siswa))
            <td align="center" style="border: 1px solid #000000;">{{$anggota_kelas->kehadiran_siswa->sakit}}</td>
            <td align="center" style="border: 1px solid #000000;">{{$anggota_kelas->kehadiran_siswa->izin}}</td>
            <td align="center" style="border: 1px solid #000000;">{{$anggota_kelas->kehadiran_siswa->tanpa_keterangan}}</td>
          @else
            <td align="center" style="border: 1px solid #000000;">-</td>
            <td align="center" style="border: 1px solid #000000;">-</td>
            <td align="center" style="border: 1px solid #000000;">-</td>
          @endif
        </tr>
      @endforeach
      <!-- End Data -->
    </tbody>
  </table>
</body>

</html>