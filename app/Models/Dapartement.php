<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dapartement extends Model
{
    use HasFactory;

    protected $table = 'tb_departement';

    protected $fillable =[
        'nama_departement',
        'sub_departement',
        'nama_pimpinan',
        'jabatan',
    ];
}
