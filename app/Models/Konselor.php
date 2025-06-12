<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute; // Not currently used, but keeping it
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Konselor extends Model
{
    // Menggunakan trait HasFactory hanya sekali.
    use HasFactory;

    // Menentukan nama tabel secara eksplisit jika singular (konselor).
    // Laravel akan secara otomatis mencari 'konselors' jika ini tidak ada.
    protected $table = 'konselor';

    protected $fillable = [
        'nama',
        'spesialisasi',
        'email',
        'password',
        'user_id', // <-- BARIS BARU: Tambahkan ini untuk mengizinkan mass assignment 'user_id'.
                   // Ini penting setelah Anda menambahkan kolom user_id ke tabel konselor
                   // melalui migrasi 'add_user_id_to_konselor_table'.
    ];

    // Jika Anda memutuskan untuk menghapus kolom 'password' dari tabel 'konselor'
    // (karena autentikasi utama ada di tabel 'users'),
    // maka hapus juga 'password' dari $fillable di atas dan dari $hidden di bawah.
    protected $hidden = [
        'password', // Sembunyikan password saat diubah ke array/JSON.
    ];


    // --- RELASI (RELATIONSHIPS) ---

    /**
     * Relasi: Satu Konselor bisa memiliki banyak data Jadwal.
     * Digunakan untuk $konselor->jadwals (menggunakan nama metode plural lebih baik untuk hasMany).
     */
    public function jadwals() // Mengubah nama metode menjadi plural (jadwals) untuk konsistensi Laravel.
    {
        return $this->hasMany(Jadwal::class);
    }

    /**
     * Relasi: Satu Konselor bisa memiliki banyak data Feedback.
     * Digunakan untuk $konselor->feedbacks (menggunakan nama metode plural lebih baik untuk hasMany).
     */
    public function feedbacks() // Mengubah nama metode menjadi plural (feedbacks) untuk konsistensi Laravel.
    {
        return $this->hasMany(Feedback::class);
    }

    /**
     * Relasi: Setiap Konselor dimiliki oleh satu User (akun login).
     * Digunakan untuk $konselor->user
     * 'user_id' adalah foreign key di tabel 'konselor'
     * 'id' adalah local key di tabel 'users'
     * Ini penting setelah Anda menambahkan kolom user_id ke tabel konselor.
     */
    public function user() // <-- BARIS BARU: Tambahkan relasi ini.
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


}