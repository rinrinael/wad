<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'booking';

    protected $fillable = [
        'nim',
        'tanggal',
        'status',
        'jadwal_id',
        'user_id' // Pastikan ini ada di fillable Anda
    ];

    // Relasi : Satu Booking dimiliki oleh satu Mahasiswa
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }

    // Relasi : Satu Booking memiliki satu Jadwal
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    // Relasi : Satu Booking dimiliki oleh satu User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}