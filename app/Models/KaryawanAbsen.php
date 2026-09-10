<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KaryawanAbsen extends Model
{
    use HasFactory;

    protected $table ='tb_karyawan_absen';

    protected $fillable = [
       'karyawan_id',
       'tanggal',
       'tanggal_mulai',
       'durasi',
       'status',
       'jenis_absen',
       'gambar',
       'keperluan',
       'tgl_persetujuan',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function cuti()
     {
         return $this->hasOne(Cuti::class, 'karyawan_id', 'karyawan_id');
     }
}
