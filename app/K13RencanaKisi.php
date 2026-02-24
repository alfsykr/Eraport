<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class K13RencanaKisi extends Model
{
    protected $table = 'k13_rencana_kisi';
    protected $fillable = [
        'pembelajaran_id',
        'k13_kd_mapel_id',
        'deskripsi_penilaian',
        'urutan',
    ];

    public function pembelajaran()
    {
        return $this->belongsTo('App\Pembelajaran');
    }

    public function k13_kd_mapel()
    {
        return $this->belongsTo('App\K13KdMapel', 'k13_kd_mapel_id');
    }

    public function nilai_kisi()
    {
        return $this->hasMany('App\K13NilaiKisi', 'k13_rencana_kisi_id');
    }
}
