<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TalentsMapping extends Model
{
    // Migrasi rename belum dijalankan - tabel aktual di DB masih 'prestasi_siswa'
    protected $table = 'prestasi_siswa';

    // Kolom yang bisa diisi - terima KEDUANYA: nama lama (DB) dan nama baru (accessor)
    protected $fillable = [
        'anggota_kelas_id',
        'jenis_prestasi',
        'deskripsi',
        'nama_talents',
        'deskripsi_talents',
    ];


    // Accessor: ambil 'nama_talents' dari kolom 'jenis_prestasi'
    public function getNamaTalentsAttribute()
    {
        return $this->attributes['jenis_prestasi'] ?? null;
    }

    // Mutator: set 'nama_talents' ke kolom 'jenis_prestasi'
    public function setNamaTalentsAttribute($value)
    {
        $this->attributes['jenis_prestasi'] = $value;
    }

    // Accessor: ambil 'deskripsi_talents' dari kolom 'deskripsi'
    public function getDeskripsiTalentsAttribute()
    {
        return $this->attributes['deskripsi'] ?? null;
    }

    // Mutator: set 'deskripsi_talents' ke kolom 'deskripsi'
    public function setDeskripsiTalentsAttribute($value)
    {
        $this->attributes['deskripsi'] = $value;
    }

    public function anggota_kelas()
    {
        return $this->belongsTo('App\AnggotaKelas');
    }
}
