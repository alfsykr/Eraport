<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PersonalProgram extends Model
{
    protected $table = 'personal_program';

    protected $fillable = [
        'siswa_id',
        'semester',
        'motorik_kasar',
        'sosialisasi',
        'rentang_akademis',
        'evaluasi_motorik_kasar',
        'evaluasi_sosialisasi',
        'evaluasi_rentang_akademis',
        'guru_id',
    ];

    public function siswa()
    {
        return $this->belongsTo('App\Siswa', 'siswa_id');
    }

    public function guru()
    {
        return $this->belongsTo('App\Guru', 'guru_id');
    }
}


