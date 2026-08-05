<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriItem extends Model
{
    use HasFactory;

    public $timestamps = true;
    protected $table = "kategori_items";

    protected $fillable=[
        'id',
        'nama_kategori',
        'type',

      ];

      public function itemGaji()
      {

          return $this->hasMany(ItemGaji::class);

      }

    



}
