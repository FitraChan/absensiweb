<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lembur extends Model
{
    use HasFactory;

    public $timestamps = true;
    protected $table = "lemburs";

    protected $fillable=[
      'id','durasi', 'karyawan_id', 'mulai_ot', 'akhir_ot', 'status','tgl_pengajuan', 'sts_pengajuan', 'total_ot_disetujui','is_check','keterangan'

      ];


      public function Karyawan()
      {
          return $this->hasOne(Karyawan::class,'id','karyawan_id');
      }
}
