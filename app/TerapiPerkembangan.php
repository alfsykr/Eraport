<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TerapiPerkembangan extends Model
{
    protected $table = 'terapi_perkembangan';

    protected $fillable = [
        'anggota_kelas_id',
        'minggu_tanggal',
        'motorik_kasar',
        'sosialisasi',
        'rentang_akademis',
        'evaluasi_sosialisasi',
        'evaluasi_rentang_akademis',
    ];

    public function anggota_kelas()
    {
        return $this->belongsTo('App\AnggotaKelas');
    }
}







