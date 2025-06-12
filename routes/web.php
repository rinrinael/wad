<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\FeedbackController;
use App\Http\Controllers\API\JadwalController;
use App\Http\Controllers\API\KonselorController;
use App\Http\Controllers\API\MahasiswaController;


Route::get('/', function () {
    return redirect()->route('login');
});

// ========== AUTH ==========

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
// Rute untuk memproses pendaftaran
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

// ========== MAHASISWA ROUTES (Fokus di sini) ==========
// Rute-rute ini membutuhkan autentikasi dan peran 'mahasiswa'.
Route::middleware(['auth', 'role:mahasiswa'])->group(function () { // <-- Hapus 'role:mahasiswa' dari sini
    Route::get('/home', [BookingController::class, 'home'])->name('home');
    Route::get('/schedule', [BookingController::class, 'lihatJadwal'])->name('schedule');
    Route::get('/appointment', [BookingController::class, 'showBookingForm'])->name('appointment');
    Route::post('/appointment/submit', [BookingController::class, 'bookingJadwal'])->name('booking.submit');
    Route::put('/booking/{id}/reschedule', [BookingController::class, 'reschedule'])->name('booking.reschedule');
    Route::put('/booking/{id}/cancel', [BookingController::class, 'cancelBooking'])->name('booking.cancel');
});
// ========== KONSELOR ROUTES (Fokus di sini) ==========
Route::middleware(['auth', 'role:konselor'])->prefix('konselor')->group(function () {
    // Dashboard Konselor
    Route::get('/dashboard', [KonselorController::class, 'dashboard'])->name('konselor.dashboard');

    // Manajemen Jadwal Konselor (dihandle oleh JadwalController)
    Route::get('/my-schedules', [JadwalController::class, 'index'])->name('konselor.my_schedules');
    Route::get('/my-schedules/create', [JadwalController::class, 'create'])->name('konselor.my_schedules.create');
    Route::post('/my-schedules', [JadwalController::class, 'store'])->name('konselor.my_schedules.store');
    Route::get('/my-schedules/{id}', [JadwalController::class, 'show'])->name('konselor.my_schedules.show');

    // --- TAMBAHKAN BARIS INI ---
    // Rute untuk MENAMPILKAN FORM EDIT JADWAL
    Route::get('/my-schedules/{id}/edit', [JadwalController::class, 'edit'])->name('konselor.my_schedules.edit'); // <-- BARIS YANG HILANG
    // --- AKHIR BARIS YANG DITAMBAHKAN ---

    Route::put('/my-schedules/{id}', [JadwalController::class, 'update'])->name('konselor.my_schedules.update');
    Route::delete('/my-schedules/{id}', [JadwalController::class, 'destroy'])->name('konselor.my_schedules.destroy');

    // Anda bisa menambahkan rute lain untuk konselor di sini jika diperlukan (misal: edit profil)
    // Route::get('/profile', [KonselorController::class, 'showProfile'])->name('konselor.profile');
    // Route::put('/profile', [KonselorController::class, 'updateProfile'])->name('konselor.profile.update');
});

// ========== ADMIN ==========
Route::middleware(['auth', 'role:admin'])->group(function () { // <-- Pastikan 'role:mahasiswa' ada di sini
    Route::get('/dashboard', [BookingController::class, 'dashboardAdmin'])->name('manage.booking'); // Mengubah 'dashboard' menjadi 'dashboardAdmin'

    // Booking management
    Route::get('/appointments', [BookingController::class, 'getAppointmentsAdmin'])->name('appointments.index'); // Mengubah 'index' menjadi 'getAppointmentsAdmin'
    Route::get('/appointments/{id}/edit', [BookingController::class, 'getAppointmentAdmin'])->name('appointments.edit'); // Mengubah 'edit' menjadi 'getAppointmentAdmin'
    Route::put('/appointments/{id}', [BookingController::class, 'updateAppointmentAdmin'])->name('appointments.update'); // Mengubah 'update' menjadi 'updateAppointmentAdmin'
    Route::delete('/appointments/{id}', [BookingController::class, 'deleteAppointmentAdmin'])->name('appointments.destroy'); // Mengubah 'destroy' menjadi 'deleteAppointmentAdmin'

    // Konselor
     Route::get('/counselors', [KonselorController::class, 'index'])->name('manage.counselor');
    Route::get('/counselors/create', [KonselorController::class, 'create'])->name('manage.counselor.create'); // <-- Rute baru untuk form create
    Route::post('/counselors', [KonselorController::class, 'store'])->name('manage.counselor.store');        // <-- Rute baru untuk store
    Route::get('/counselors/{konselor}/edit', [KonselorController::class, 'edit'])->name('manage.counselor.edit'); // <-- Rute baru untuk form edit
    Route::put('/counselors/{konselor}', [KonselorController::class, 'update'])->name('manage.counselor.update');
    Route::delete('/counselors/{konselor}', [KonselorController::class, 'destroy'])->name('manage.counselor.destroy');
; // Pastikan metode ini ada di KonselorController

    // Jadwal
    Route::get('/schedules', [JadwalController::class, 'index'])->name('manage.schedule'); // Pastikan metode ini ada di JadwalController

    // Mahasiswa
    Route::get('/students', [MahasiswaController::class, 'index'])->name('manage.student'); // Pastikan metode ini ada di MahasiswaController

    // Feedback
    Route::get('/feedbacks', [FeedbackController::class, 'index'])->name('manage.feedback'); // Pastikan metode ini ada di FeedbackController
});