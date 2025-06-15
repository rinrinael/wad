<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Jadwal;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\Konselor;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function home(Request $request)
    {
        $user = $request->user()->load('mahasiswa');
        if (!$user) {
            return redirect()->route('login');
        }

        $today = now()->toDateString();
        $appointments = Booking::where('user_id', $user->id)
                               ->where('booking.status', '!=', 'cancelled')
                               ->whereHas('jadwal', function($query) use ($today) {
                                   $query->whereDate('hari', '>=', $today);
                               })
                               ->join('jadwal', 'booking.jadwal_id', '=', 'jadwal.id')
                               ->with(['jadwal.konselor', 'mahasiswa'])
                               ->select('booking.*')
                               ->orderBy('booking.tanggal', 'asc')
                               ->orderBy('jadwal.waktu', 'asc')
                               ->get();

        return view('mahasiswa/home-mahasiswa', compact('user', 'appointments'));
    }

    public function lihatJadwal(Request $request)
    {
        $jadwals = Jadwal::where('status', 'available')
                         ->where('hari', '>=', now()->toDateString())
                         ->where(function($query) {
                             $query->whereDoesntHave('booking')
                                   ->orWhereHas('booking', function($q) {
                                       $q->whereIn('status', ['cancelled', 'completed']);
                                   });
                         })
                         ->with('konselor')
                         ->get();

        if ($request->expectsJson()) {
            return response()->json($jadwals, 200);
        }

        return view('mahasiswa/jadwal-mahasiswa', compact('jadwals'));
    }

    public function showBookingForm()
    {
        $jadwalsTersedia = Jadwal::where('status', 'available')
                                 ->where('hari', '>=', now()->toDateString())
                                 ->where(function($query) {
                                     $query->whereDoesntHave('booking')
                                           ->orWhereHas('booking', function($q) {
                                               $q->whereIn('status', ['cancelled', 'completed']);
                                           });
                                 })
                                 ->with('konselor')
                                 ->get();

        $user = Auth::user()->load('mahasiswa');
        $mahasiswa = $user->mahasiswa;

        return view('mahasiswa/booking-mahasiswa', compact('jadwalsTersedia', 'user', 'mahasiswa'));
    }

    public function bookingJadwal(Request $request)
    {
        $request->validate([
            'nim'       => 'required|exists:mahasiswa,nim',
            'jadwal_id' => 'required|exists:jadwal,id',
        ]);

        if (Booking::where('jadwal_id', $request->jadwal_id)
                    ->whereNotIn('status', ['cancelled', 'completed'])
                    ->exists()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Jadwal ini tidak tersedia untuk dibooking.'], 409);
            }
            return back()->with('error', 'Jadwal ini sudah dibooking atau tidak tersedia.');
        }

        $jadwalToBook = Jadwal::findOrFail($request->jadwal_id);

        $booking = Booking::create([
            'user_id'   => Auth::id(),
            'nim'       => $request->nim,
            'tanggal'   => $jadwalToBook->hari,
            'status'    => 'booked',
            'jadwal_id' => $request->jadwal_id,
        ]);

        $jadwalToBook->status = 'booked';
        $jadwalToBook->save();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Booking berhasil dibuat!', 'booking' => $booking], 201);
        }

        return redirect()->route('home')->with('success', 'Booking berhasil dibuat!');
    }

    public function reschedule(Request $request, $id)
    {
        $request->validate([
            'jadwal_id_baru' => 'required|exists:jadwal,id',
        ]);

        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if (Booking::where('jadwal_id', $request->jadwal_id_baru)
                    ->whereNotIn('status', ['cancelled', 'completed'])
                    ->where('id', '!=', $booking->id)
                    ->exists()) {
            return response()->json(['message' => 'Jadwal baru sudah dibooking atau tidak tersedia.'], 409);
        }

        $oldJadwal = Jadwal::find($booking->jadwal_id);
        if ($oldJadwal) {
            $oldJadwal->status = 'available';
            $oldJadwal->save();
        }
        
        $newJadwal = Jadwal::findOrFail($request->jadwal_id_baru);

        if ($newJadwal->status !== 'available') {
            return response()->json(['message' => 'Jadwal baru tidak tersedia.'], 409);
        }

        $booking->jadwal_id = $request->jadwal_id_baru;
        $booking->tanggal = $newJadwal->hari;
        $booking->save();

        $newJadwal->status = 'booked';
        $newJadwal->save();

        return response()->json(['message' => 'Reschedule berhasil.', 'booking' => $booking]);
    }

    public function cancelBooking($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak diizinkan membatalkan booking ini.');
        }

        $booking->status = 'cancelled';
        $booking->save();

        $jadwal = Jadwal::find($booking->jadwal_id);

        if ($jadwal) {
            $jadwal->status = 'available';
            $jadwal->save();
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Booking dibatalkan.', 'booking' => $booking], 200);
        }
        
        return redirect()->route('home')->with('success', 'Booking berhasil dibatalkan dan slot jadwal sudah tersedia kembali!');
    }

    // Metode Admin
    public function dashboardAdmin(Request $request)
    {
        $appointments = Booking::with(['user', 'jadwal.konselor'])->get();
        return view('admin.kelola-booking', compact('appointments'));
    }

    public function indexAdminAppointments(Request $request)
    {
        // --- PERBAIKAN DI SINI: Filter bookings agar hanya menampilkan yang berstatus 'booked' ---
        $appointments = Booking::where('status', 'booked') // <-- TAMBAHKAN FILTER INI
                               ->with(['user', 'jadwal.konselor', 'mahasiswa'])
                               ->get();
        // --- AKHIR PERBAIKAN ---
        return view('admin.appointments.index', compact('appointments'));
    }

    public function getAppointmentAdmin($id)
    {
        $appointment = Booking::with(['user', 'jadwal.konselor'])->findOrFail($id);

        $availableJadwals = Jadwal::where('status', 'available')
                                    ->where('hari', '>=', Carbon::today()->toDateString())
                                    ->where(function($query) use ($id) {
                                        $query->whereDoesntHave('booking')
                                              ->orWhereHas('booking', function($q) use ($id) {
                                                  $q->whereIn('status', ['cancelled', 'completed'])
                                                    ->orWhere('booking.id', $id);
                                              });
                                    })
                                    ->with('konselor')
                                    ->get();

        return view('admin.appointments.edit', compact('appointment', 'availableJadwals'));
    }

    public function updateAppointmentAdmin(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $validatedData = $request->validate([
            'status' => 'required|string|in:booked,cancelled,completed',
            'jadwal_id' => 'required|exists:jadwal,id',
        ]);

        if ($booking->jadwal_id != $validatedData['jadwal_id']) {
            $oldJadwal = Jadwal::find($booking->jadwal_id);
            if ($oldJadwal) {
                $oldJadwal->status = 'available';
                $oldJadwal->save();
            }
        }

        $booking->jadwal_id = $validatedData['jadwal_id'];
        $booking->status = $validatedData['status'];
        
        $newJadwal = Jadwal::findOrFail($validatedData['jadwal_id']);
        $booking->tanggal = $newJadwal->hari;

        $booking->save();

        if ($booking->status === 'booked') {
            $newJadwal->status = 'booked';
            $newJadwal->save();
        } else {
             $newJadwal->status = 'available';
             $newJadwal->save();
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Appointment berhasil diperbarui!', 'booking' => $booking], 200);
        }

        return redirect()->route('manage.booking')->with('success', 'Appointment berhasil diperbarui!');
    }

    public function deleteAppointmentAdmin($id)
    {
        $booking = Booking::findOrFail($id);
        
        $jadwal = Jadwal::find($booking->jadwal_id);
        if ($jadwal) {
            $jadwal->status = 'available';
            $jadwal->save();
        }

        $booking->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Booking berhasil dihapus.'], 200);
        }
        
        return redirect()->route('manage.booking')->with('success', 'Booking berhasil dihapus.');
    }
}