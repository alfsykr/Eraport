<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TalentsMapping extends Model
{
    protected $table = 'talents_mapping';
    protected $fillable = [
        'anggota_kelas_id',
        'nama_talents',
        'deskripsi_talents'
    ];

    public function anggota_kelas()
    {
        return $this->belongsTo('App\AnggotaKelas');
    }
}
