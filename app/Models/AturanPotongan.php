<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AturanPotongan extends Model
{
    use HasFactory;
    protected $table = 'aturan_potongan';

    protected $fillable = [
        'konfig_id',
        'jenis_potongan',
        'nama_aturan',
        'menit_mulai',
        'menit_selesai',
        'qty_mulai',
        'qty_selesai',
        'tipe_nilai',
        'nilai_potongan',
        'sumber_potongan',
        'is_active',
    ];

    protected $casts = [
        'menit_mulai'     => 'integer',
        'menit_selesai'   => 'integer',
        'qty_mulai'       => 'integer',
        'qty_selesai'     => 'integer',
        'nilai_potongan'  => 'float',
        'is_active'       => 'boolean',
    ];
}
