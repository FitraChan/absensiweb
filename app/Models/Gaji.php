<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Gaji extends Model
{
    use HasFactory;
    use HasApiTokens;
    public $timestamps = true;
    protected $table = "gajies";

    protected $fillable=[ 'karyawan_id',
        'periode_gaji_id',
        'tot_tunjangan',
        'tot_potongan',

      ];


    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class,'karyawan_id','id');
    }


    public function transGaji()
    {
        return $this->hasMany(TransGaji::class);
    }

    public function periodeGaji()
    {
        return $this->belongsTo(PeriodeGaji::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class,'karyawan_id','karyawan_id');
    }
}
