<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    //
    protected $fillable = [
        'komentar',
        'rating',
        'nim',
        'konselor_id'
    ];

    // Relasi: Satu Mahasiswa bisa membuat banyak Feedback
    public function Mahasiswa()
    {
        return $this->hasMany(Mahasiswa::class);
    }

    // Relasi: Satu Konselor bisa melihat banyak Feedbcak
    public function Konselor()
    {
        return $this->hasMany(Konselor::class);
    }
}

