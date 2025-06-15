<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Konselor;
use App\Models\Mahasiswa;
use App\Models\Booking;
use App\Models\Jadwal;
use App\Models\Feedback;
use Carbon\Carbon;

class ApiController extends Controller
{
    // ================================================================
    // AUTENTIKASI (AUTH)
    // ================================================================

    /**
     * Memproses registrasi user baru (mahasiswa/konselor) dan membuat record terkait.
     * Sesuai dengan rute: POST /register
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => ['required', 'confirmed', Password::min(8)],
                'role' => ['required', Rule::in(['mahasiswa', 'konselor'])],
                'spesialisasi' => [
                    'sometimes',
                    Rule::requiredIf($request->role === 'konselor'),
                    'string',
                    'max:255',
                ],
                'nim' => [
                    'sometimes',
                    Rule::requiredIf($request->role === 'mahasiswa'),
                    'integer',
                    'unique:mahasiswa,nim',
                ],
            ]);

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'role' => $validatedData['role'],
            ]);

            if ($user->role === 'konselor') {
                Konselor::create([
                    'user_id' => $user->id,
                    'nama' => $validatedData['name'],
                    'email' => $validatedData['email'],
                    'spesialisasi' => $validatedData['spesialisasi'] ?? 'Umum',
                    'password' => Hash::make($validatedData['password']), // Kolom password di Konselor/Mahasiswa mungkin tidak diperlukan
                ]);
            } elseif ($user->role === 'mahasiswa') {
                Mahasiswa::create([
                    'user_id' => $user->id,
                    'nama' => $validatedData['name'],
                    'email' => $validatedData['email'],
                    'nim' => $validatedData['nim'],
                    'tanggal' => now()->toDateString(),
                    'password' => Hash::make($validatedData['password']), // Kolom password di Konselor/Mahasiswa mungkin tidak diperlukan
                ]);
            }

            return response()->json([
                'message' => 'Registrasi berhasil!',
                'user' => $user->load('mahasiswa', 'konselor'),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal melakukan registrasi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Memproses login user.
     * Sesuai dengan rute: POST /login
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized.',
                'errors' => ['email' => ['Email atau password salah.']],
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil!',
            'user' => $user->load('mahasiswa', 'konselor'),
            'token' => $token,
        ], 200);
    }

    /**
     * Melakukan logout user.
     * Sesuai dengan rute: POST /logout
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Berhasil logout.'], 200);
    }

    /**
     * Menampilkan profil user yang terautentikasi.
     * Sesuai dengan rute: GET /profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request)
    {
        $user = $request->user()->load('mahasiswa', 'konselor');
        return response()->json([
            'message' => 'Profil user berhasil diambil.',
            'user' => $user,
        ], 200);
    }

    // ================================================================
    // BOOKING (UNTUK MAHASISWA)
    // ================================================================

    /**
     * Mengambil daftar jadwal yang tersedia.
     * Sesuai dengan rute: GET /jadwal-tersedia
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableJadwal(Request $request)
    {
        $jadwals = Jadwal::where('status', 'available')
                         ->whereDate('hari', '>=', Carbon::today())
                         ->with('konselor.user')
                         ->orderBy('hari')
                         ->orderBy('waktu')
                         ->get();

        return response()->json([
            'message' => 'Jadwal yang tersedia berhasil diambil.',
            'jadwals' => $jadwals,
        ], 200);
    }

    /**
     * Membuat booking baru.
     * Sesuai dengan rute: POST /booking
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createBooking(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nim' => 'required|exists:mahasiswa,nim',
                'jadwal_id' => 'required|exists:jadwal,id',
            ]);

            $jadwal = Jadwal::find($validatedData['jadwal_id']);

            if (!$jadwal) {
                return response()->json(['message' => 'Jadwal tidak ditemukan.'], 404);
            }

            if ($jadwal->status !== 'available' || Carbon::parse($jadwal->hari)->isPast()) {
                return response()->json(['message' => 'Jadwal ini tidak tersedia atau sudah berlalu untuk dibooking.'], 409);
            }

            $existingBooking = Booking::where('user_id', Auth::id())
                                      ->whereIn('status', ['booked', 'rescheduled'])
                                      ->first();
            if ($existingBooking) {
                return response()->json(['message' => 'Anda sudah memiliki booking aktif untuk jadwal ini.'], 409);
            }

            $user = $request->user();
            if ($user->role !== 'mahasiswa') {
                return response()->json(['message' => 'Hanya mahasiswa yang bisa membuat booking.'], 403);
            }
            $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
            if (!$mahasiswa || $mahasiswa->nim !== $validatedData['nim']) {
                return response()->json(['message' => 'NIM yang dimasukkan tidak sesuai dengan akun Anda.'], 403);
            }

            $booking = Booking::create([
                'user_id' => Auth::id(),
                'nim' => $validatedData['nim'],
                'tanggal' => $jadwal->hari,
                'status' => 'booked',
                'jadwal_id' => $validatedData['jadwal_id'],
            ]);

            $jadwal->status = 'booked';
            $jadwal->save();

            return response()->json([
                'message' => 'Booking berhasil dibuat!',
                'booking' => $booking->load('jadwal.konselor.user'),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal membuat booking.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Membatalkan booking yang sudah ada.
     * Sesuai dengan rute: POST /booking/{id}/cancel
     *
     * @param int $id ID booking yang akan dibatalkan.
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelBooking($id)
    {
        try {
            $booking = Booking::find($id);

            if (!$booking) {
                return response()->json(['message' => 'Booking tidak ditemukan.'], 404);
            }

            if ($booking->user_id !== Auth::id()) {
                return response()->json(['message' => 'Anda tidak berhak membatalkan booking ini.'], 403);
            }

            if (!in_array($booking->status, ['booked', 'rescheduled'])) {
                return response()->json(['message' => 'Booking ini tidak bisa dibatalkan karena statusnya adalah "' . $booking->status . '".'], 400);
            }

            $jadwal = Jadwal::find($booking->jadwal_id);

            $booking->status = 'cancelled';
            $booking->save();

            if ($jadwal) {
                $jadwal->status = 'available';
                $jadwal->save();
            }

            return response()->json([
                'message' => 'Booking berhasil dibatalkan.',
                'booking' => $booking,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal membatalkan booking.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menampilkan daftar booking yang sudah ada untuk user yang terautentikasi.
     * Endpoint ini cocok untuk menampilkan "My Appointments" di sisi klien.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
   public function myBookings(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'User tidak terautentikasi.'], 401);
        }

        try {
            // Pastikan Anda memuat relasi 'mahasiswa' pada user jika belum dimuat
            // Ini penting untuk pemeriksaan NIM di createBooking dan lainnya
            $user->loadMissing('mahasiswa');

            // Ambil nama tabel dari model Booking secara dinamis
            $bookingTableName = (new Booking())->getTable(); // Ini akan mengembalikan 'booking' (tunggal)

            // Bergabung dengan tabel 'jadwal' untuk dapat mengurutkan berdasarkan 'waktu' dari jadwal
            $appointments = Booking::where("{$bookingTableName}.user_id", $user->id) // Gunakan nama tabel yang benar
                                    ->join('jadwal', "{$bookingTableName}.jadwal_id", '=', 'jadwal.id') // Lakukan JOIN dengan nama tabel yang benar
                                    ->with('jadwal.konselor.user') // Eager load konselor dan user terkait untuk detail
                                    ->select("{$bookingTableName}.*", 'jadwal.waktu as jadwal_waktu') // Pilih semua kolom bookings, dan alias 'jadwal.waktu'
                                    ->orderBy("{$bookingTableName}.tanggal", 'desc') // Urutkan berdasarkan tanggal booking
                                    ->orderBy('jadwal_waktu', 'desc') // Urutkan berdasarkan waktu jadwal menggunakan alias
                                    ->get();

            if ($appointments->isEmpty()) {
                return response()->json([
                    'message' => 'Tidak ada booking yang ditemukan untuk user ini.',
                    'bookings' => [],
                ], 200);
            }

            return response()->json([
                'message' => 'Daftar booking berhasil diambil.',
                'bookings' => $appointments,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil daftar booking.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ================================================================
    // JADWAL (UNTUK KONSELOR)
    // ================================================================

    /**
     * Menampilkan daftar semua jadwal untuk konselor yang login.
     * Sesuai dengan rute: GET /jadwal
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexJadwal()
    {
        $user = Auth::user();
        if ($user->role !== 'konselor') {
            return response()->json(['message' => 'Akses ditolak. Hanya konselor yang bisa melihat jadwalnya.'], 403);
        }

        $konselor = $user->konselor;
        if (!$konselor) {
            return response()->json(['message' => 'Data konselor tidak ditemukan.'], 404);
        }

        $jadwals = Jadwal::where('konselor_id', $konselor->id)
                         ->with('konselor.user')
                         ->orderBy('hari')
                         ->orderBy('waktu')
                         ->get();

        return response()->json([
            'message' => 'Daftar jadwal konselor berhasil diambil.',
            'jadwals' => $jadwals,
        ], 200);
    }

    /**
     * Menyimpan jadwal baru yang dibuat oleh konselor.
     * Sesuai dengan rute: POST /jadwal
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeJadwal(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'konselor') {
            return response()->json(['message' => 'Akses ditolak. Hanya konselor yang bisa membuat jadwal.'], 403);
        }

        try {
            $validatedData = $request->validate([
                'hari' => 'required|date|after_or_equal:today',
                'waktu' => 'required|date_format:H:i',
                'status' => 'required|string|in:available,booked',
            ]);

            $konselor = $user->konselor;

            $existingJadwal = Jadwal::where('konselor_id', $konselor->id)
                                    ->where('hari', $validatedData['hari'])
                                    ->where('waktu', $validatedData['waktu'])
                                    ->first();
            if ($existingJadwal) {
                return response()->json(['message' => 'Anda sudah memiliki jadwal pada hari dan waktu tersebut.'], 409);
            }

            $jadwal = Jadwal::create([
                'konselor_id' => $konselor->id,
                'hari' => $validatedData['hari'],
                'waktu' => $validatedData['wore]'],
                'status' => $validatedData['status'],
            ]);

            return response()->json([
                'message' => 'Jadwal berhasil dibuat.',
                'jadwal' => $jadwal->load('konselor.user'),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal membuat jadwal.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menampilkan detail jadwal tertentu.
     * Sesuai dengan rute: GET /jadwal/{jadwal}
     *
     * @param  \App\Models\Jadwal  $jadwal
     * @return \Illuminate\Http\JsonResponse
     */
    public function showJadwal(Jadwal $jadwal)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $jadwal->konselor->user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak. Anda tidak berhak melihat jadwal ini.'], 403);
        }

        return response()->json([
            'message' => 'Detail jadwal berhasil diambil.',
            'jadwal' => $jadwal->load('konselor.user'),
        ], 200);
    }

    /**
     * Memperbarui jadwal yang sudah ada.
     * Sesuai dengan rute: PUT /jadwal/{jadwal}
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Jadwal  $jadwal
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateJadwal(Request $request, Jadwal $jadwal)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $jadwal->konselor->user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak. Anda tidak berhak memperbarui jadwal ini.'], 403);
        }

        try {
            $validatedData = $request->validate([
                'hari' => 'sometimes|required|date|after_or_equal:today',
                'waktu' => 'sometimes|required|date_format:H:i',
                'status' => 'sometimes|required|string|in:available,booked',
            ]);

            if (isset($validatedData['hari']) || isset($validatedData['waktu'])) {
                $newHari = $validatedData['hari'] ?? $jadwal->hari;
                $newWaktu = $validatedData['waktu'] ?? $jadwal->waktu;

                $existingJadwal = Jadwal::where('konselor_id', $jadwal->konselor_id)
                                        ->where('hari', $newHari)
                                        ->where('waktu', $newWaktu)
                                        ->where('id', '!=', $jadwal->id)
                                        ->first();
                if ($existingJadwal) {
                    return response()->json(['message' => 'Jadwal yang diperbarui tumpang tindih dengan jadwal lain pada hari dan waktu tersebut.'], 409);
                }
            }

            if ($jadwal->status === 'booked' && (isset($validatedData['status']) && $validatedData['status'] === 'available')) {
                $activeBooking = Booking::where('jadwal_id', $jadwal->id)
                                        ->whereIn('status', ['booked', 'rescheduled'])
                                        ->first();
                if ($activeBooking) {
                    return response()->json(['message' => 'Tidak bisa mengubah status jadwal ke "available" karena masih ada booking aktif.'], 409);
                }
            }

            $jadwal->update($validatedData);

            return response()->json([
                'message' => 'Jadwal berhasil diperbarui.',
                'jadwal' => $jadwal->load('konselor.user'),
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memperbarui jadwal.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menghapus jadwal tertentu.
     * Sesuai dengan rute: DELETE /jadwal/{jadwal}
     *
     * @param  \App\Models\Jadwal  $jadwal
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyJadwal(Jadwal $jadwal)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $jadwal->konselor->user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak. Anda tidak berhak menghapus jadwal ini.'], 403);
        }

        $activeBooking = Booking::where('jadwal_id', $jadwal->id)
                                ->whereIn('status', ['booked', 'rescheduled'])
                                ->first();
        if ($activeBooking) {
            return response()->json(['message' => 'Tidak bisa menghapus jadwal karena masih ada booking aktif terkait.'], 409);
        }

        try {
            $jadwal->delete();

            return response()->json(['message' => 'Jadwal berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus jadwal.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ================================================================
    // FEEDBACK
    // ================================================================

    /**
     * Menampilkan daftar semua feedback.
     * Sesuai dengan rute: GET /feedback
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexFeedback()
    {
        $user = Auth::user();
        $feedback = collect();

        if ($user) {
            if ($user->role === 'admin') {
                $feedback = Feedback::with(['booking.user', 'booking.jadwal.konselor.user'])
                                    ->get();
            } elseif ($user->role === 'konselor') {
                $konselor = $user->konselor;
                if ($konselor) {
                    $feedback = Feedback::where('konselor_id', $konselor->id)
                                        ->with(['booking.user', 'booking.jadwal.konselor.user'])
                                        ->get();
                }
            } elseif ($user->role === 'mahasiswa') {
                $feedback = Feedback::where('user_id', $user->id)
                                    ->with(['booking.user', 'booking.jadwal.konselor.user'])
                                    ->get();
            } else {
                return response()->json(['message' => 'Akses ditolak.'], 403);
            }
        } else {
            return response()->json(['message' => 'User tidak terautentikasi.'], 401);
        }

        return response()->json([
            'message' => 'Daftar feedback berhasil diambil.',
            'feedback' => $feedback,
        ], 200);
    }

    // ================================================================
    // KONSELOR (PUBLIK & AKSI ADMIN)
    // ================================================================

    /**
     * Menampilkan daftar semua konselor.
     * Sesuai dengan rute: GET /konselor (Publik)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexKonselor()
    {
        $konselors = Konselor::with('user')->get();

        return response()->json([
            'message' => 'Daftar konselor berhasil diambil.',
            'konselors' => $konselors,
        ], 200);
    }

    /**
     * Menampilkan detail konselor tertentu.
     * Sesuai dengan rute: GET /konselor/{konselor} (Publik)
     *
     * @param  \App\Models\Konselor  $konselor
     * @return \Illuminate\Http\JsonResponse
     */
    public function showKonselor(Konselor $konselor)
    {
        return response()->json([
            'message' => 'Detail konselor berhasil diambil.',
            'konselor' => $konselor->load('user'),
        ], 200);
    }

    /**
     * Menyimpan konselor baru (Aksi oleh Admin).
     * Sesuai dengan rute: POST /konselor
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeKonselor(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak. Hanya admin yang bisa menambah konselor.'], 403);
        }

        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => ['required', Password::min(8)],
                'spesialisasi' => 'required|string|max:255',
            ]);

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'role' => 'konselor',
            ]);

            $konselor = Konselor::create([
                'user_id' => $user->id,
                'nama' => $validatedData['name'],
                'email' => $validatedData['email'],
                'spesialisasi' => $validatedData['spesialisasi'],
                'password' => Hash::make($validatedData['password']), // Mungkin tidak diperlukan
            ]);

            return response()->json([
                'message' => 'Konselor berhasil ditambahkan.',
                'konselor' => $konselor->load('user'),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menambahkan konselor.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Memperbarui informasi konselor (Aksi oleh Admin).
     * Sesuai dengan rute: PUT /konselor/{konselor}
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Konselor  $konselor
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateKonselor(Request $request, Konselor $konselor)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak. Hanya admin yang bisa memperbarui konselor.'], 403);
        }

        try {
            $validatedData = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($konselor->user->id)],
                'password' => ['sometimes', 'nullable', Password::min(8)],
                'spesialisasi' => 'sometimes|required|string|max:255',
            ]);

            $user = $konselor->user;
            if ($user) {
                if (isset($validatedData['name'])) { $user->name = $validatedData['name']; }
                if (isset($validatedData['email'])) { $user->email = $validatedData['email']; }
                if (isset($validatedData['password']) && !is_null($validatedData['password'])) { $user->password = Hash::make($validatedData['password']); }
                $user->save();
            }

            if (isset($validatedData['name'])) { $konselor->nama = $validatedData['name']; }
            if (isset($validatedData['email'])) { $konselor->email = $validatedData['email']; }
            if (isset($validatedData['spesialisasi'])) { $konselor->spesialisasi = $validatedData['spesialisasi']; }
            if (isset($validatedData['password']) && !is_null($validatedData['password'])) { $konselor->password = Hash::make($validatedData['password']); } // Mungkin tidak diperlukan
            $konselor->save();

            return response()->json([
                'message' => 'Konselor berhasil diperbarui.',
                'konselor' => $konselor->load('user'),
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memperbarui konselor.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menghapus konselor (Aksi oleh Admin).
     * Sesuai dengan rute: DELETE /konselor/{konselor}
     *
     * @param  \App\Models\Konselor  $konselor
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyKonselor(Konselor $konselor)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak. Hanya admin yang bisa menghapus konselor.'], 403);
        }

        try {
            $user = $konselor->user;
            if ($user) {
                $user->delete();
            }
            $konselor->delete();

            return response()->json(['message' => 'Konselor berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus konselor.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
