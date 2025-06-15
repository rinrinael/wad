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
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

// ========== MAHASISWA ROUTES ==========
Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/mahasiswa/register', [MahasiswaController::class, 'create'])->name('mahasiswa.register.form');
    Route::get('/home', [BookingController::class, 'home'])->name('home');
    Route::get('/schedule', [BookingController::class, 'lihatJadwal'])->name('schedule');
    Route::get('/appointment', [BookingController::class, 'showBookingForm'])->name('appointment');
    Route::post('/appointment/submit', [BookingController::class, 'bookingJadwal'])->name('booking.submit');
    Route::put('/booking/{id}/reschedule', [BookingController::class, 'reschedule'])->name('booking.reschedule');
    Route::put('/booking/{id}/cancel', [BookingController::class, 'cancelBooking'])->name('booking.cancel');
    Route::get('/feedback', [FeedbackController::class, 'showForm'])->name('feedback');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
});

// ========== KONSELOR ROUTES ==========
Route::middleware(['auth', 'role:konselor'])->prefix('konselor')->group(function () {
    Route::get('/dashboard', [KonselorController::class, 'dashboard'])->name('konselor.dashboard');

    Route::get('/my-schedules', [JadwalController::class, 'index'])->name('konselor.my_schedules');
    Route::get('/my-schedules/create', [JadwalController::class, 'create'])->name('konselor.my_schedules.create');
    Route::post('/my-schedules', [JadwalController::class, 'store'])->name('konselor.my_schedules.store');
    Route::get('/my-schedules/{id}', [JadwalController::class, 'show'])->name('konselor.my_schedules.show');
    Route::get('/my-schedules/{id}/edit', [JadwalController::class, 'edit'])->name('konselor.my_schedules.edit');
    Route::put('/my-schedules/{id}', [JadwalController::class, 'update'])->name('konselor.my_schedules.update');
    Route::delete('/my-schedules/{id}', [JadwalController::class, 'destroy'])->name('konselor.my_schedules.destroy');
    Route::get('/feedback', [FeedbackController::class, 'showFeedback'])->name('konselor.feedback');
});

// ========== ADMIN ==========
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/students/create', [MahasiswaController::class, 'createAdmin'])->name('manage.student.create'); // Anda perlu membuat createAdmin() method di controller
    Route::post('/students', [MahasiswaController::class, 'storeAdmin'])->name('manage.student.store'); // Anda perlu membuat storeAdmin() method di controller
    // ADMIN: MANAGEMENT BOOKING / APPOINTMENTS
    Route::get('/appointments', [BookingController::class, 'indexAdminAppointments'])->name('manage.booking');
    Route::get('/appointments/{id}/edit', [BookingController::class, 'getAppointmentAdmin'])->name('appointments.edit');
    Route::put('/appointments/{id}', [BookingController::class, 'updateAppointmentAdmin'])->name('appointments.update');
    Route::delete('/appointments/{id}', [BookingController::class, 'deleteAppointmentAdmin'])->name('appointments.destroy');

    // Konselor Management
    Route::get('/counselors', [KonselorController::class, 'index'])->name('manage.counselor');
    Route::get('/counselors/create', [KonselorController::class, 'create'])->name('counselor.create');
    Route::post('/counselors', [KonselorController::class, 'store'])->name('counselor.store');
    Route::get('/counselors/{konselor}/edit', [KonselorController::class, 'edit'])->name('counselor.edit');
    Route::put('/counselors/{konselor}', [KonselorController::class, 'update'])->name('counselor.update');
    Route::delete('/counselors/{konselor}', [KonselorController::class, 'destroy'])->name('counselor.destroy');

    // Jadwal Management
    Route::get('/schedules', [JadwalController::class, 'indexAdmin'])->name('manage.schedule');
    Route::get('/schedules/{id}/edit', [JadwalController::class, 'editAdmin'])->name('manage.schedule.edit');
    Route::put('/schedules/{id}', [JadwalController::class, 'updateAdmin'])->name('manage.schedule.update');
    Route::delete('/schedules/{id}', [JadwalController::class, 'destroyAdmin'])->name('manage.schedule.destroy');

    // Mahasiswa Management
    Route::get('/students', [MahasiswaController::class, 'index'])->name('manage.student');
    Route::get('/students/{nim}/edit', [MahasiswaController::class, 'editAdmin'])->name('manage.student.edit');
    Route::put('/students/{nim}', [MahasiswaController::class, 'updateAdmin'])->name('manage.student.update');
    Route::delete('/students/{nim}', [MahasiswaController::class, 'destroyAdmin'])->name('manage.student.destroy');

    // Feedback Management
    Route::get('/feedbacks', [FeedbackController::class, 'index'])->name('manage.feedback');
    Route::get('/feedbacks', [FeedbackController::class, 'manageFeedback'])->name('manage.feedback');
    Route::get('/feedbacks/{id}/edit', [FeedbackController::class, 'edit'])->name('feedback.edit');
    Route::delete('/feedbacks/{id}', [FeedbackController::class, 'destroy'])->name('feedback.destroy');
});