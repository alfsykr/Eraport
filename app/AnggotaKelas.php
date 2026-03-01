<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnggotaKelas extends Model
{
    protected $table = 'anggota_kelas';
    protected $fillable = [
        'siswa_id',
        'kelas_id',
        'pendaftaran',
    ];

    public function siswa()
    {
        return $this->belongsTo('App\Siswa');
    }

    public function kelas()
    {
        return $this->belongsTo('App\Kelas');
    }


    public function kehadiran_siswa()
    {
        return $this->hasOne('App\KehadiranSiswa');
    }

    public function prestasi_siswa()
    {
        return $this->hasMany('App\PrestasiSiswa');
    }

    public function catatan_wali_kelas()
    {
        return $this->hasOne('App\CatatanWaliKelas');
    }

    public function kenaikan_kelas()
    {
        return $this->hasOne('App\KenaikanKelas');
    }


    // Relasi K13
    public function k13_nilai_akhir_raport()
    {
        return $this->hasMany('App\K13NilaiAkhirRaport');
    }
}
