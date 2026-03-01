<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class K13NilaiAkhirRaport extends Model
{
    protected $table = 'k13_nilai_akhir_raport';
    protected $fillable = [
        'pembelajaran_id',
        'anggota_kelas_id',
        'kkm',
        'nilai_akhir',
        'predikat_akhir',
    ];

    public function pembelajaran()
    {
        return $this->belongsTo('App\Pembelajaran');
    }

    public function anggota_kelas()
    {
        return $this->belongsTo('App\AnggotaKelas');
    }
}
