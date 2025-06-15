<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import ApiController tunggal
use App\Http\Controllers\API\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

//======================================================================
// RUTE PUBLIK (Tidak Perlu Login)
//======================================================================

// --- Auth ---
Route::post('/register', [ApiController::class, 'register'])->name('api.register');
Route::post('/login', [ApiController::class, 'login'])->name('api.login');

// --- Konselor ---
// Rute untuk melihat daftar konselor dan detailnya oleh publik
Route::get('/konselor', [ApiController::class, 'indexKonselor'])->name('api.konselor.index');
Route::get('/konselor/{konselor}', [ApiController::class, 'showKonselor'])->name('api.konselor.show');


//======================================================================
// RUTE TERLINDUNGI (Wajib Login dengan Token Sanctum)
//======================================================================
Route::middleware('auth:sanctum')->group(function () {
    
    // --- Auth ---
    Route::post('/logout', [ApiController::class, 'logout'])->name('api.logout');
    Route::get('/profile', [ApiController::class, 'profile'])->name('api.profile');

    // --- Booking (Untuk Mahasiswa) ---
    Route::get('/jadwal-tersedia', [ApiController::class, 'getAvailableJadwal'])->name('api.jadwal.available');
    Route::post('/booking', [ApiController::class, 'createBooking'])->name('api.booking.store');
    Route::post('/booking/{id}/cancel', [ApiController::class, 'cancelBooking'])->name('api.booking.cancel');

    // --- Jadwal (Untuk Konselor) ---
    // Mengelola jadwal milik konselor yang sedang login
    // Metode resource: index, store, show, update, destroy
    Route::get('/jadwal', [ApiController::class, 'indexJadwal'])->name('api.jadwal.index');
    Route::post('/jadwal', [ApiController::class, 'storeJadwal'])->name('api.jadwal.store');
    Route::get('/jadwal/{jadwal}', [ApiController::class, 'showJadwal'])->name('api.jadwal.show');
    Route::put('/jadwal/{jadwal}', [ApiController::class, 'updateJadwal'])->name('api.jadwal.update');
    Route::delete('/jadwal/{jadwal}', [ApiController::class, 'destroyJadwal'])->name('api.jadwal.destroy');


    // --- Feedback ---
    // Hanya ada satu endpoint API untuk melihat semua feedback
    Route::get('/feedback', [ApiController::class, 'indexFeedback'])->name('api.feedback.index');

    // --- Konselor (Aksi oleh Admin) ---
    // Rute untuk menambah, mengubah, dan menghapus konselor (membutuhkan otorisasi Gate)
    Route::get('/konselor', [ApiController::class, 'indexKonselor'])->name('api.konselor.index');
    Route::post('/konselor', [ApiController::class, 'storeKonselor'])->name('api.konselor.store');
    Route::put('/konselor/{konselor}', [ApiController::class, 'updateKonselor'])->name('api.konselor.update');
    Route::delete('/konselor/{konselor}', [ApiController::class, 'destroyKonselor'])->name('api.konselor.destroy');
});
