<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SetPayroll extends Model
{
    use HasFactory;

    protected $table = 'departemen_item_gaji';

    protected $fillable = [
        'departemen_id', 'item_gaji_id', 'nominal'
    ];

    public function departement()
    {
        return $this->belongsTo(Dapartement::class,'departemen_id','id');
    }

    public function itemGaji()
    {
        return $this->belongsTo(ItemGaji::class);
    }


}
