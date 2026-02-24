<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>{{$title}}</title>
  <style>
    @page {
      margin: 25px;
    }

    body {
      font-family: "Times New Roman", Times, serif;
      font-size: 11pt;
      background-image: url('{{ str_replace('\\', '/', public_path('assets/images/logo/bg_raport.png')) }}');
      background-repeat: no-repeat;
      background-position: center center;
      background-size: 80% auto;
    }

    h2,
    h3 {
      text-align: center;
      margin: 6px 0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    .info td {
      padding: 2px 0;
      vertical-align: top;
    }

    .box {
      border: 1px solid #000;
      margin-top: 10px;
    }

    .box th,
    .box td {
      border: 1px solid #000;
      padding: 5px 6px;
      vertical-align: top;
    }

    .box th {
      background-color: #f0f0f0;
      text-align: center;
    }

    .mapel-header td {
      font-weight: bold;
      background-color: #e8e8e8;
      padding: 4px 6px;
      border: 1px solid #000;
    }

    .signature {
      margin-top: 40px;
    }

    .signature td {
      vertical-align: top;
    }
  </style>
</head>

<body>
  <h2>Kisi-kisi Kompetensi Siswa Per Mata Pelajaran</h2>

  <table class="info">
    <tr>
      <td style="width:18%">Nama</td>
      <td style="width:2%">:</td>
      <td style="width:30%">{{ ucwords(strtolower($anggota_kelas->siswa->nama_lengkap)) }}</td>
      <td style="width:20%">Semester</td>
      <td style="width:2%">:</td>
      <td style="width:28%">
        {{ $anggota_kelas->kelas->tapel->semester == 1 ? 'I/Ganjil' : 'II/Genap' }}
      </td>
    </tr>
    <tr>
      <td>Kelas/program</td>
      <td>:</td>
      <td>{{ $anggota_kelas->kelas->nama_kelas }}</td>
      <td>Tahun ajaran</td>
      <td>:</td>
      <td>{{ $anggota_kelas->kelas->tapel->tahun_pelajaran }}</td>
    </tr>
  </table>

  {{-- Tabel kisi-kisi per mapel --}}
  @php $nomor_global = 0;
  $huruf_mapel = 'A'; @endphp

  <table class="box" style="margin-top: 14px;">
    <thead>
      <tr>
        <th style="width:6%">No</th>
        <th>Mata Pelajaran / Indikator Penilaian</th>
        <th style="width:12%">Nilai</th>
      </tr>
    </thead>
    <tbody>
      @forelse($data_mapel as $mapel_item)
        {{-- Baris header nama mapel --}}
        <tr>
          <td style="text-align:center; font-weight:bold; background:#f0f0f0;">{{ $huruf_mapel }}</td>
          <td style="font-weight:bold; background:#f0f0f0;">{{ $mapel_item['mapel'] }}</td>
          <td style="background:#f0f0f0;"></td>
        </tr>
        @php $nomor_mapel = 1; @endphp
        @foreach($mapel_item['detail'] as $kisi)
          <tr>
            <td style="text-align:center;">{{ $nomor_mapel++ }}.</td>
            <td>{{ $kisi['deskripsi'] }}</td>
            <td style="text-align:center; font-weight:bold;">
              {{ $kisi['nilai'] != '-' ? $kisi['nilai'] : '-' }}
            </td>
          </tr>
        @endforeach
        @php $huruf_mapel++; @endphp
      @empty
        <tr>
          <td colspan="3" style="text-align:center; padding:10px;">Belum ada data penilaian</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  {{-- Tanda tangan --}}
  <table class="signature">
    <tr>
      <td style="width:35%">
        Mengetahui <br>
        Orang Tua / Wali, <br><br><br><br>
        (................................)
      </td>
      <td style="width:30%"></td>
      <td style="width:35%">
        @if(isset($tanggal_raport))
          {{ \Carbon\Carbon::parse($tanggal_raport->tanggal_raport)->locale('id')->isoFormat('D MMMM Y') }}
        @else
          {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}
        @endif
        <br>Guru Kelas,<br><br><br><br>
        <b><u>{{ $anggota_kelas->kelas->guru->nama_lengkap }}, {{ $anggota_kelas->kelas->guru->gelar }}</u></b><br>
        NIP. {{ konversi_nip($anggota_kelas->kelas->guru->nip) }}
      </td>
    </tr>
    <tr>
      <td></td>
      <td style="text-align:center">
        Mengetahui <br>
        Kepala Sekolah,<br><br><br><br>
        <b><u>{{ $sekolah->kepala_sekolah }}</u></b><br>
        NIP. {{ konversi_nip($sekolah->nip_kepala_sekolah) }}
      </td>
      <td></td>
    </tr>
  </table>
</body>

</html>