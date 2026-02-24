<?php

/**
 * Script untuk RESTORE data siswa yang hilang
 * 
 * Script ini akan:
 * 1. Restore siswa.kelas_id dari anggota_kelas terakhir
 * 2. Hanya update siswa yang kelas_id-nya null
 * 
 * CARA PAKAI:
 * php artisan tinker
 * include 'restore_data.php';
 */

use App\Siswa;
use App\AnggotaKelas;
use Illuminate\Support\Facades\DB;

echo "=== RESTORE DATA SISWA ===\n\n";

// Ambil siswa yang kelas_id-nya null
$siswa_without_kelas = Siswa::where('status', 1)
    ->where('kelas_id', null)
    ->get();

echo "Ditemukan {$siswa_without_kelas->count()} siswa tanpa kelas_id\n";

if ($siswa_without_kelas->count() == 0) {
    echo "✅ Tidak ada data yang perlu di-restore!\n";
    exit;
}

echo "Mulai restore...\n\n";

$restored_count = 0;
$failed_count = 0;

foreach ($siswa_without_kelas as $siswa) {
    // Cari anggota_kelas terakhir untuk siswa ini
    $latest_anggota = AnggotaKelas::where('siswa_id', $siswa->id)
        ->orderBy('id', 'DESC')
        ->first();

    if ($latest_anggota) {
        $siswa->kelas_id = $latest_anggota->kelas_id;
        $siswa->save();

        echo "✅ Restore siswa #{$siswa->id} ({$siswa->nama_lengkap}) → kelas_id: {$latest_anggota->kelas_id}\n";
        $restored_count++;
    } else {
        echo "⚠️  Siswa #{$siswa->id} ({$siswa->nama_lengkap}) tidak ada histori di anggota_kelas\n";
        $failed_count++;
    }
}

echo "\n=== HASIL RESTORE ===\n";
echo "✅ Berhasil restore: {$restored_count} siswa\n";
echo "⚠️  Gagal restore: {$failed_count} siswa (tidak ada histori)\n";

if ($failed_count > 0) {
    echo "\n💡 Untuk siswa yang gagal, kemungkinan mereka belum pernah masuk kelas.\n";
    echo "   Silakan tambahkan mereka ke kelas secara manual di menu Data Kelas.\n";
}

