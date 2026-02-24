<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class K13NilaiKisi extends Model
{
    protected $table = 'k13_nilai_kisi';
    protected $fillable = [
        'k13_rencana_kisi_id',
        'anggota_kelas_id',
        'nilai',
    ];

    public function rencana_kisi()
    {
        return $this->belongsTo('App\K13RencanaKisi', 'k13_rencana_kisi_id');
    }

    public function anggota_kelas()
    {
        return $this->belongsTo('App\AnggotaKelas');
    }
}
