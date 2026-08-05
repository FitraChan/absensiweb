<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;


class ItemGaji extends Model
{
    use HasFactory;
    use HasApiTokens;
    public $timestamps = true;
    protected $table = "item_gajies";

    protected $fillable=[
        'nama_item_gaji',
        'kategori_item_id',
        'no_urut',

      ];

      public function transGaji()
      {
          return $this->hasMany(TransGaji::class);
      }

      public function setPayroll()
      {
        // code...
        return $this->hasMany(SetPayroll::class, 'item_gaji_id');
      }

     public function Gaji()
     {
         return $this->belongsTo(Gaji::class);
     }

     public function kategoriItems()
     {
       // code...
         return $this->hasOne(KategoriItem::class,'id','kategori_item_id');
     }


     public function itemGaji()
     {
         return $this->belongsTo(ItemGaji::class);
     }
}
