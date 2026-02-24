<?php

/**
 * Script untuk cek dan restore data yang hilang
 * 
 * PENYEBAB MASALAH:
 * 1. Session tapel_id mungkin tidak ada atau salah
 * 2. Data siswa.kelas_id mungkin sudah null karena code lama
 * 
 * SOLUSI:
 * 1. Cek apakah ada data di database
 * 2. Restore siswa.kelas_id dari anggota_kelas
 */

use Illuminate\Support\Facades\DB;
use App\Tapel;
use App\Kelas;
use App\Siswa;
use App\AnggotaKelas;
use App\Mapel;

// 1. CEK TAHUN PELAJARAN
echo "=== CEK TAHUN PELAJARAN ===\n";
$tapels = Tapel::orderBy('id', 'DESC')->get();
echo "Total tahun pelajaran: " . $tapels->count() . "\n\n";

foreach ($tapels as $tapel) {
    echo "ID: {$tapel->id} | {$tapel->tahun_pelajaran} | Semester: {$tapel->semester}\n";

    // Hitung kelas per tahun
    $kelas_count = Kelas::where('tapel_id', $tapel->id)->count();
    echo "  └─ Jumlah kelas: {$kelas_count}\n";

    // Hitung mapel per tahun  
    $mapel_count = Mapel::where('tapel_id', $tapel->id)->count();
    echo "  └─ Jumlah mapel: {$mapel_count}\n\n";
}

// 2. CEK SESSION
echo "=== CEK SESSION TAPEL_ID ===\n";
$session_tapel_id = session()->get('tapel_id');
if ($session_tapel_id) {
    echo "Session tapel_id: {$session_tapel_id}\n";
    $active_tapel = Tapel::find($session_tapel_id);
    if ($active_tapel) {
        echo "Tahun aktif: {$active_tapel->tahun_pelajaran} | Semester: {$active_tapel->semester}\n\n";
    } else {
        echo "⚠️ WARNING: Session tapel_id ada tapi data tidak ditemukan!\n\n";
    }
} else {
    echo "⚠️ WARNING: Session tapel_id TIDAK ADA!\n";
    echo "Silakan login dan pilih tahun pelajaran di Setting Tahun Pelajaran\n\n";
}

// 3. CEK DATA SISWA
echo "=== CEK DATA SISWA ===\n";
$total_siswa = Siswa::where('status', 1)->count();
echo "Total siswa aktif: {$total_siswa}\n";

$siswa_without_kelas = Siswa::where('status', 1)->where('kelas_id', null)->count();
echo "Siswa tanpa kelas_id: {$siswa_without_kelas}\n";

$siswa_with_kelas = Siswa::where('status', 1)->whereNotNull('kelas_id')->count();
echo "Siswa dengan kelas_id: {$siswa_with_kelas}\n\n";

// 4. CEK ANGGOTA KELAS (HISTORI)
echo "=== CEK ANGGOTA KELAS (HISTORI) ===\n";
$total_anggota = AnggotaKelas::count();
echo "Total record anggota_kelas: {$total_anggota}\n\n";

// 5. TAWARAN RESTORE
if ($siswa_without_kelas > 0 && $total_anggota > 0) {
    echo "=== ⚠️ PERLU RESTORE DATA ===\n";
    echo "Ada {$siswa_without_kelas} siswa yang kelas_id-nya null\n";
    echo "Tapi ada {$total_anggota} record di anggota_kelas (histori masih ada!)\n\n";

    echo "SOLUSI: Restore siswa.kelas_id dari anggota_kelas terakhir\n";
    echo "Jalankan query berikut di phpMyAdmin atau Artisan Tinker:\n\n";

    echo "SQL untuk restore:\n";
    echo "--------------------\n";
    echo "UPDATE siswa s\n";
    echo "INNER JOIN (\n";
    echo "    SELECT siswa_id, kelas_id, MAX(id) as latest_id\n";
    echo "    FROM anggota_kelas\n";
    echo "    GROUP BY siswa_id\n";
    echo ") ak ON s.id = ak.siswa_id\n";
    echo "SET s.kelas_id = ak.kelas_id\n";
    echo "WHERE s.kelas_id IS NULL AND s.status = 1;\n\n";
}

// 6. CEK MAPEL
echo "=== CEK MATA PELAJARAN ===\n";
if ($session_tapel_id) {
    $mapel_count = Mapel::where('tapel_id', $session_tapel_id)->count();
    echo "Mapel untuk tahun aktif (tapel_id={$session_tapel_id}): {$mapel_count}\n";

    if ($mapel_count == 0) {
        echo "⚠️ TIDAK ADA MAPEL untuk tahun aktif!\n";

        // Cek apakah ada mapel di tahun lain
        $total_mapel = Mapel::count();
        echo "Total mapel di semua tahun: {$total_mapel}\n";

        if ($total_mapel > 0) {
            echo "\n💡 SOLUSI: Ganti tahun pelajaran aktif di Setting > Setting Tahun Pelajaran\n";
            echo "Pilih tahun yang memiliki data mata pelajaran\n";
        }
    }
} else {
    $total_mapel = Mapel::count();
    echo "Total mata pelajaran (semua tahun): {$total_mapel}\n";
}

