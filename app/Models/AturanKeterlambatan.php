<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AturanKeterlambatan extends Model
{
    use HasFactory;
    protected $table = 'aturan_keterlambatan';

    protected $fillable = [
        'konfig_id',
        'menit_mulai',
        'menit_selesai',
        'persen_potongan',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'persen_potongan' => 'float',
        'is_active' => 'boolean',
    ];
}
