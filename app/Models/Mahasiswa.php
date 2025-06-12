<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    // Bagus: Menunjukkan nama tabel singular (mahasiswa)
    protected $table = 'mahasiswa';

    // Bagus: Menunjukkan 'nim' sebagai primary key dan bukan auto-incrementing, serta tipe int
    protected $primaryKey = 'nim';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'nim',
        'user_id', // <-- PASTIKAN INI ADA. Ini penting agar user_id bisa di-mass assign
        'jadwal_id',
        'tanggal', // Kolom 'tanggal' ini, apakah maksudnya 'tanggal_lahir'? Jika ya, sesuaikan nama
        'nama',
        'email',
        'password', // <-- PERHATIAN: Baca penjelasan di bawah
        // 'major', // <-- PERHATIKAN: Jika ada kolom 'major' di tabel 'mahasiswa', masukkan di sini
    ];

    protected $hidden = [
        'password', // <-- PERHATIAN: Baca penjelasan di bawah
    ];

    // --- RELASI PENTING YANG HARUS ADA ---

    /**
     * Relasi: Mahasiswa dimiliki oleh satu User.
     * Digunakan untuk $mahasiswa->user
     * 'user_id' adalah foreign key di tabel 'mahasiswa'
     * 'id' adalah local key di tabel 'users'
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Relasi: Mahasiswa terkait ke satu Jadwal (misal, jadwal konselor yang ditugaskan).
     * Digunakan untuk $mahasiswa->jadwal
     */
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id', 'id');
    }

    /**
     * Relasi: Mahasiswa memiliki banyak Booking.
     * Digunakan untuk $mahasiswa->bookings
     * 'nim' adalah foreign key di tabel 'booking' yang merujuk ke 'nim' di tabel 'mahasiswa'
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'nim', 'nim');
    }
}