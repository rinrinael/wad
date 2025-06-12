<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Jadwal;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\Konselor; // Pastikan ini sudah ada

class BookingController extends Controller
{
    public function home(Request $request)
    {
        // Eager load relasi 'mahasiswa' jika ingin data profil di sidebar
        $user = $request->user()->load('mahasiswa'); // <-- Tambahkan .load('mahasiswa')
        if (!$user) {
            return redirect()->route('login');
        }

        $today = now()->toDateString();
        $appointments = Booking::where('user_id', $user->id)
                                ->whereDate('tanggal', $today)
                                ->with('jadwal')
                                ->get();

        return view('mahasiswa/home-mahasiswa', compact('user', 'appointments'));
    }

    public function lihatJadwal()
    {
        $jadwals = Jadwal::whereDoesntHave('booking')->with('konselor')->get();
        return view('mahasiswa/jadwal-mahasiswa', compact('jadwals'));
    }

    // --- TAMBAHKAN METODE INI UNTUK MENAMPILKAN FORM BOOKING ---
    public function showBookingForm()
    {
        $jadwalsTersedia = Jadwal::whereDoesntHave('booking')->with('konselor')->get();

        // Eager load relasi 'mahasiswa'
        $user = Auth::user()->load('mahasiswa'); // <-- Tambahkan .load('mahasiswa')
        // Dapatkan objek Mahasiswa dari relasi User
        $mahasiswa = $user->mahasiswa;

        return view('mahasiswa/booking-mahasiswa', compact('jadwalsTersedia', 'user', 'mahasiswa'));
    }
    // --- AKHIR METODE BARU ---


    // Metode bookingJadwal ini sudah benar untuk memproses POST request
    public function bookingJadwal(Request $request)
    {
        $request->validate([
            'nim' => 'required|exists:mahasiswa,nim',
            'jadwal_id' => 'required|exists:jadwal,id',
        ]);

        if (Booking::where('jadwal_id', $request->jadwal_id)->exists()) {
            return back()->with('error', 'Jadwal ini sudah dibooking oleh orang lain.');
        }

        $booking = Booking::create([
            'user_id' => Auth::id(), // Pastikan user_id disimpan
            'nim' => $request->nim,
            'tanggal' => now(), // Atau sesuai input dari form jika ada
            'status' => 'booked',
            'jadwal_id' => $request->jadwal_id,
        ]);

        return redirect()->route('home')->with('success', 'Booking berhasil dibuat!');
    }

    // Metode reschedule dan cancelBooking juga sudah benar
    public function reschedule(Request $request, $id)
    {
        $request->validate([
            'jadwal_id_baru' => 'required|exists:jadwal,id',
        ]);

        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if (Booking::where('jadwal_id', $request->jadwal_id_baru)->exists()) {
            return response()->json(['message' => 'Jadwal baru sudah dibooking.'], 409);
        }

        $booking->jadwal_id = $request->jadwal_id_baru;
        $booking->tanggal = now();
        $booking->save();

        return response()->json(['message' => 'Reschedule berhasil.', 'booking' => $booking]);
    }

    public function cancelBooking($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $booking->status = 'cancelled';
        $booking->save();

        return response()->json(['message' => 'Booking dibatalkan.', 'booking' => $booking]);
    }

    // Metode Admin tetap di sini sesuai permintaan, tidak diubah
    public function dashboardAdmin(Request $request)
    {
        $allBookings = Booking::with(['user', 'jadwal.konselor'])->get();
        return view('admin/dashboard', compact('allBookings'));
    }

    public function getAppointmentsAdmin(Request $request)
    {
        $appointments = Booking::with(['user', 'jadwal.konselor'])->get();
        return view('admin/appointments/index', compact('appointments'));
    }

    public function getAppointmentAdmin($id)
    {
        $appointment = Booking::with(['user', 'jadwal.konselor'])->findOrFail($id);
        return view('admin/appointments/edit', compact('appointment'));
    }

    public function updateAppointmentAdmin(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update($request->validate([
            'jadwal_id' => 'required|exists:jadwal,id',
            'status' => 'required|string|in:booked,cancelled,completed',
        ]));
        return response()->json(['message' => 'Booking berhasil diperbarui.', 'booking' => $booking]);
    }

    public function deleteAppointmentAdmin($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();
        return response()->json(['message' => 'Booking berhasil dihapus.']);
    }
}