<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa';
    protected $primaryKey = 'nim';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nim',
        'user_id',
        'jadwal_id',
        'tanggal',
        'nama',
        'email',
        'password', // <--- TAMBAHKAN KEMBALI BARIS INI KE FILLABLE
    ];

    protected $hidden = [
        'password', // Biarkan ini tetap ada agar password tidak muncul di JSON/Array
    ];

    protected $appends = ['email_user', 'name_user'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id', 'id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'nim', 'nim');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'nim', 'nim');
    }

    public function getEmailUserAttribute()
    {
        return $this->user ? $this->user->email : ($this->attributes['email'] ?? null);
    }

    public function getNameUserAttribute()
    {
        return $this->user ? $this->user->name : ($this->attributes['nama'] ?? null);
    }
}