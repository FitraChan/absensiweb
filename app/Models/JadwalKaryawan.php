<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalKaryawan extends Model
{
    use HasFactory;
    protected $table = 'jadwal_karyawan';

    protected $fillable =[
        'karyawan_id','jam_kerja_id','tanggal'
    ];

    // Relasi ke Karyawan
  public function Karyawan()
  {
      return $this->belongsTo(Karyawan::class);
  }

  // Relasi ke Shift
  public function JamKerja()
  {
      return $this->belongsTo(JamKerja::class);
  }
}
