<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;


class TransGaji extends Model
{
    use HasFactory;
    use HasApiTokens;
    public $timestamps = true;
    protected $table = "trans_gajies";

    protected $fillable=[
      'gaji_id',
     'item_gaji_id',
     'karyawan_id',
     'nominal',
     'qty',
      ];

      public function itemGaji()
      {
          return $this->belongsTo(ItemGaji::class)->orderBy('kategori_item_id','asc');
      }


      public function setPayroll()
      {
          return $this->belongsTo(SetPayroll::class,'item_gaji_id', 'item_gaji_id');
      }
}
