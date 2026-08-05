<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JamKerja extends Model
{
    use HasFactory;

    protected $table = 'tb_jam_kerja';

    protected $fillable =[
        'nama_shift',
        'waktu_mulai',
        'waktu_akhir',
    ];

    public function JadwalKaryawan()
   {
       return $this->hasMany(JadwalKaryawan::class, 'jam_kerja_id');
   }
}
