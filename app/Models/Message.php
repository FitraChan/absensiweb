<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'receiver_id',
        'message',
        'is_read',
        'jenis_id',
        'is_read_user'
    ];

    // Relasi ke User (Pengirim)
    public function sender()
    {
        return $this->belongsTo(Karyawan::class, 'user_id');
    }

    // Relasi ke User (Penerima)
    public function receiver()
    {
        return $this->belongsTo(Karyawan::class, 'receiver_id');
    }
}
