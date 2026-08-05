<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class TransAbsen extends Model
{
    use HasFactory;
    use HasApiTokens;
    public $timestamps = true;
    protected $table = "trans_absen";

    protected $fillable=[
        'karyawan_id',
        'periode_gaji_id',

      ];


    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }



    public function periodeGaji()
    {
        return $this->belongsTo(PeriodeGaji::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class,'trans_absen_id','id');
    }
}
