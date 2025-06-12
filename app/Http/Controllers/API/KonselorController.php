<?php

namespace App\Http\Controllers\API; // Namespace tetap API

use App\Http\Controllers\Controller;
use App\Models\Konselor;
use App\Models\Booking; // <-- Pastikan ini di-import untuk mengambil data booking
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Tambahkan ini jika menggunakan Rule::in

class KonselorController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user()->load('konselor');

        if (!$user || $user->role !== 'konselor' || !$user->konselor) {
            abort(403, 'Anda tidak diizinkan mengakses dashboard konselor.');
        }

        $konselor = $user->konselor;
        $today = Carbon::today()->toDateString(); // Menggunakan Carbon untuk tanggal hari ini

        // Mengambil janji temu hari ini untuk konselor yang sedang login
        $todaysBookings = Booking::whereHas('jadwal', function ($query) use ($konselor) {
                                $query->where('konselor_id', $konselor->id);
                            })
                            ->whereDate('tanggal', $today)
                            ->with(['jadwal.konselor', 'mahasiswa'])
                            ->get();

        // --- BARU: Mengambil SEMUA booking untuk konselor ini ---
        // Anda mungkin ingin membatasi rentang waktu (misal: 6 bulan ke depan)
        $allKonselorBookings = Booking::whereHas('jadwal', function ($query) use ($konselor) {
                                    $query->where('konselor_id', $konselor->id);
                                })
                                ->with(['jadwal']) // Hanya perlu jadwal untuk informasi tanggal/waktu
                                ->get()
                                ->map(function($booking) {
                                    return [
                                        'date' => Carbon::parse($booking->tanggal)->toDateString(),
                                        'time' => Carbon::parse($booking->jadwal->waktu)->format('H:i'),
                                        'mahasiswa_name' => $booking->mahasiswa->nama ?? 'N/A',
                                        'booking_id' => $booking->id
                                    ];
                                });


        // Data untuk membangun kalender dinamis di Blade
        $currentMonth = Carbon::now()->format('F Y'); // Contoh: June 2025
        $firstDayOfMonth = Carbon::now()->startOfMonth(); // Tanggal 1 bulan ini
        $daysInMonth = $firstDayOfMonth->daysInMonth; // Jumlah hari dalam bulan ini

        return view('konselor/home-konselor', compact('user', 'konselor', 'todaysBookings', 'allKonselorBookings', 'currentMonth', 'firstDayOfMonth', 'daysInMonth'));
    }
    // Tampilkan daftar konselor (untuk admin)
    public function index()
    {
        // Biasanya admin akan melihat daftar konselor
        // Jika ini untuk API, response()->json sudah benar
        // Jika untuk view, return view('admin.konselor.index', ['konselors' => Konselor::paginate(10)]);
        $konselor = Konselor::paginate(10);
        return response()->json($konselor);
    }

    /**
     * Menampilkan form untuk membuat konselor baru.
     * Metode ini akan mengembalikan view, bukan JSON.
     * Biasanya diakses oleh admin.
     */
    public function create() // <-- BARU: Metode untuk menampilkan form tambah konselor
    {
        // Asumsi admin sudah login
        if (!Gate::allows('manage-konselor', Auth::user())) { // Menggunakan Auth::user() untuk Gate
            abort(403, 'Anda tidak diizinkan mengakses halaman ini.');
        }
        return view('admin.konselor.create'); // Anda perlu membuat file Blade ini
    }

    /**
     * Store a newly created resource in storage.
     * Ini akan memproses data dari form 'create' atau dari API lain.
     */
    public function store(Request $request)
    {
        if (!Gate::allows('manage-konselor', Auth::user())) { // Menggunakan Auth::user() untuk Gate
            return response()->json(['message' => 'Anda tidak diizinkan melakukan aksi ini.'], 403);
        }

        $validatedData = $request->validate([
            'nama' => 'required|string|max:255', // Sesuaikan dengan nama kolom di tabel 'konselor'
            'spesialisasi' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:konselor,email',
            'password' => 'required|string|min:8|confirmed', // Hash password sebelum disimpan
            'user_id' => [ // Jika Anda ingin mengaitkan konselor dengan user saat dibuat
                'nullable', 'integer',
                Rule::unique('konselor', 'user_id'), // Pastikan user_id unik di tabel konselor
                Rule::exists('users', 'id') // Pastikan user_id ada di tabel users
            ],
        ]);

        // Jika Anda memiliki kolom password di tabel konselor dan ingin menyimpannya di sana
        $validatedData['password'] = Hash::make($validatedData['password']);

        $konselor = Konselor::create($validatedData);

        // Jika Anda ingin mengarahkan kembali ke halaman daftar konselor setelah berhasil
        // return redirect()->route('manage.counselor.index')->with('success', 'Konselor berhasil ditambahkan.');
        return response()->json(['message' => 'Konselor berhasil ditambahkan.', 'data' => $konselor], 201);
    }

    /**
     * Display the specified resource.
     * Akan mengembalikan JSON detail konselor.
     */
    public function show(Konselor $konselor)
    {
        return response()->json($konselor);
    }

    /**
     * Menampilkan form untuk mengedit konselor.
     * Metode ini akan mengembalikan view, bukan JSON.
     * Biasanya diakses oleh admin.
     */
    public function edit(Konselor $konselor) // <-- BARU: Metode untuk menampilkan form edit konselor
    {
        // Otorisasi sebaiknya memeriksa terhadap model yang spesifik, bukan user secara umum.
        if (!Gate::allows('update-konselor', $konselor)) {
            abort(403, 'Anda tidak diizinkan mengakses halaman ini.');
        }
        return view('admin.konselor.edit', compact('konselor')); // Anda perlu membuat file Blade ini
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Konselor $konselor)
    {
        // Otorisasi sebaiknya memeriksa terhadap model yang spesifik, bukan user secara umum.
        if (!Gate::allows('update-konselor', $konselor)) {
            return response()->json(['message' => 'Anda tidak diizinkan melakukan aksi ini.'], 403);
        }

        $validatedData = $request->validate([
            'nama' => 'sometimes|required|string|max:255',
            'spesialisasi' => 'sometimes|required|string|max:255',
            // 'email' unik kecuali untuk konselor yang sedang diupdate
            'email' => 'sometimes|required|string|email|max:255|unique:konselor,email,' . $konselor->id,
            // Password hanya jika diisi
            'password' => 'sometimes|nullable|string|min:8|confirmed', // nullable untuk password
        ]);

        // Hash password jika ada di request
        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']); // Jangan update password jika kosong
        }

        $konselor->update($validatedData);

        // Jika Anda ingin mengarahkan kembali ke halaman daftar konselor setelah berhasil
        // return redirect()->route('manage.counselor.index')->with('success', 'Konselor berhasil diperbarui.');
        return response()->json(['message' => 'Konselor berhasil diperbarui.', 'data' => $konselor]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Konselor $konselor)
    {
        if (!Gate::allows('manage-konselor', Auth::user())) { // Menggunakan Auth::user() untuk Gate
            return response()->json(['message' => 'Anda tidak diizinkan melakukan aksi ini.'], 403);
        }

        $konselor->delete();
        // Jika Anda ingin mengarahkan kembali ke halaman daftar konselor setelah berhasil
        // return redirect()->route('manage.counselor.index')->with('success', 'Konselor berhasil dihapus.');
        return response()->json(['message' => 'Konselor berhasil dihapus.']);
    }
}