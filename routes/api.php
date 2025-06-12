<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\FeedbackController;
use App\Http\Controllers\API\JadwalController;
use App\Http\Controllers\API\KonselorController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

//======================================================================
// RUTE PUBLIK (Tidak Perlu Login)
//======================================================================

// --- Auth ---
Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// --- Konselor ---
// Rute untuk melihat daftar konselor dan detailnya oleh publik
Route::get('/konselor', [KonselorController::class, 'index'])->name('api.konselor.index');
Route::get('/konselor/{konselor}', [KonselorController::class, 'show'])->name('api.konselor.show');


//======================================================================
// RUTE TERLINDUNGI (Wajib Login dengan Token Sanctum)
//======================================================================
Route::middleware('auth:sanctum')->group(function () {
    
    // --- Auth ---
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/profile', [AuthController::class, 'profile'])->name('api.profile');

    // --- Booking (Untuk Mahasiswa) ---
    Route::get('/jadwal-tersedia', [BookingController::class, 'lihatJadwal'])->name('api.jadwal.available');
    Route::post('/booking', [BookingController::class, 'bookingJadwal'])->name('api.booking.store');
    Route::post('/booking/{id}/reschedule', [BookingController::class, 'reschedule'])->name('api.booking.reschedule');
    Route::post('/booking/{id}/cancel', [BookingController::class, 'cancelBooking'])->name('api.booking.cancel');

    // --- Jadwal (Untuk Konselor) ---
    // Mengelola jadwal milik konselor yang sedang login
    Route::apiResource('jadwal', JadwalController::class)->names('api.jadwal');

    // --- Feedback ---
    // Hanya ada satu endpoint API untuk melihat semua feedback
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('api.feedback.index');

    // --- Konselor (Aksi oleh Admin) ---
    // Rute untuk menambah, mengubah, dan menghapus konselor (membutuhkan otorisasi Gate)
    Route::post('/konselor', [KonselorController::class, 'store'])->name('api.konselor.store');
    Route::put('/konselor/{konselor}', [KonselorController::class, 'update'])->name('api.konselor.update');
    Route::delete('/konselor/{konselor}', [KonselorController::class, 'destroy'])->name('api.konselor.destroy');
});