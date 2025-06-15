<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Mahasiswa;
use App\Models\Konselor;
use App\Models\User;
use App\Models\Booking; // Pastikan ini di-use
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Pastikan ini di-use jika belum ada

class FeedbackController extends Controller
{
    /**
     * Fungsi pembantu untuk mendapatkan ID konselor yang login.
     * @return int|null
     */
    protected function getLoggedInKonselorId()
    {
        $user = Auth::user();
        if ($user && !$user->relationLoaded('konselor')) {
            $user->load('konselor');
        }

        if ($user && $user->role === 'konselor' && $user->konselor) {
            return $user->konselor->id;
        }
        // Mengubah abort 403 menjadi return null atau melempar exception yang lebih spesifik
        // jika fungsi ini hanya dipanggil di konteks yang menjamin konselor ada.
        // Untuk saat ini, asumsikan ini digunakan di tempat yang memerlukan konselor.
        abort(403, 'Unauthorized. Not a linked konselor or missing konselor data.');
    }

    /**
     * Display a listing of the resource (API endpoint).
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $feedback = Feedback::with(['mahasiswa.user', 'konselor.user'])->get();
        return response()->json($feedback, 200);
    }

    /**
     * Menampilkan daftar feedback untuk konselor (WEB View).
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showFeedback()
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'konselor') {
            return redirect()->route('login')->withErrors(['message' => 'Anda tidak memiliki akses sebagai konselor.']);
        }

        if (!$user->relationLoaded('konselor')) {
            $user->load('konselor');
        }
        $konselor = $user->konselor;

        if (!$konselor) {
            abort(403, 'Unauthorized. Konselor data not linked for this user.');
        }

        $feedbacks = Feedback::where('konselor_id', $konselor->id)
                             ->with('mahasiswa.user')
                             ->orderBy('created_at', 'desc')
                             ->get();

        return view('konselor/feedback-konselor', compact('feedbacks', 'user', 'konselor'));
    }

    /**
     * Menampilkan form feedback untuk mahasiswa (WEB View).
     * Akan menampilkan riwayat booking yang sudah completed.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showForm()
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'mahasiswa') {
            return redirect()->route('login')->withErrors(['message' => 'Anda harus login sebagai mahasiswa.']);
        }

        if (!$user->relationLoaded('mahasiswa')) {
            $user->load('mahasiswa');
        }
        $mahasiswa = $user->mahasiswa; // Pastikan $mahasiswa terdefinisi jika dibutuhkan di view

        // --- INI BAGIAN PENTING YANG HARUS ADA DAN BENAR ---
        // Ambil riwayat booking mahasiswa yang statusnya 'completed'
        // dan belum ada feedback yang diberikan untuk booking tersebut
        $completedBookings = Booking::where('user_id', $user->id) // Filter berdasarkan user yang login
                                     ->where('status', 'completed')
                                     ->whereDoesntHave('feedback') // Hanya booking yang belum diberi feedback
                                     ->with(['jadwal.konselor.user']) // Eager load jadwal, konselor, dan user konselor
                                     ->orderBy('tanggal', 'desc')
                                     ->get();

        // Pastikan variabel 'completedBookings' dikirim ke view
        return view('mahasiswa/feedback-mahasiswa', compact('user', 'completedBookings'));
    }

    /**
     * Store a newly created resource in storage (untuk WEB dan API).
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $isWebRequest = $request->acceptsHtml();

        $rules = [
            'booking_id'    => 'required|exists:booking,id',
            'comment'       => 'nullable|string|max:1000',
            'rating'        => 'nullable|integer|min:1|max:5',
        ];

        $request->validate($rules);

        $user = Auth::user();
        if ($user && !$user->relationLoaded('mahasiswa')) {
            $user->load('mahasiswa');
        }

        if (!$user || !($mahasiswa = $user->mahasiswa) || $user->role !== 'mahasiswa') {
            if ($isWebRequest) {
                return redirect()->back()->withErrors(['message' => 'Anda harus login sebagai mahasiswa untuk memberikan feedback.'])->withInput();
            }
            return response()->json(['message' => 'Unauthorized or Mahasiswa data not found.'], 403);
        }

        $booking = Booking::findOrFail($request->booking_id);

        if ($booking->user_id !== $user->id || $booking->status !== 'completed') {
            if ($isWebRequest) {
                return redirect()->back()->withErrors(['message' => 'Booking tidak valid atau belum selesai.'])->withInput();
            }
            return response()->json(['message' => 'Invalid booking or not completed.'], 403);
        }

        if ($booking->feedback()->exists()) {
             if ($isWebRequest) {
                return redirect()->back()->withErrors(['message' => 'Anda sudah memberikan feedback untuk booking ini.'])->withInput();
            }
            return response()->json(['message' => 'Feedback already exists for this booking.'], 409);
        }

        $konselorId = $booking->jadwal->konselor_id;
        // $jamPertemuan = Carbon::parse($booking->jadwal->waktu)->format('H:i'); // Tidak diperlukan jika tidak disimpan di DB

        Feedback::create([
            'nim'           => $mahasiswa->nim, // Menggunakan 'nim' sebagai foreign key jika itu yang ada di tabel feedback
            'konselor_id'   => $konselorId,
            'komentar'      => $request->comment,
            'rating'        => $request->rating,
            // 'jam_pertemuan' => $jamPertemuan, // Jangan uncomment jika kolom tidak ada
            'booking_id'    => $booking->id,
        ]);

        if ($isWebRequest) {
            return redirect()->route('feedback.showForm')->with('success', 'Feedback telah berhasil dikirim. Terima kasih!');
        }

        return response()->json(['message' => 'Feedback created successfully.'], 201);
    }

    /**
     * Menampilkan daftar feedback untuk Admin (WEB View).
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function manageFeedback()
    {
        $user = Auth::user();

        // Pastikan hanya admin yang bisa mengakses halaman ini
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('login')->withErrors(['message' => 'Anda tidak memiliki akses sebagai admin.']);
        }

        // Ambil semua feedback dengan eager loading relasi yang relevan
        $feedbacks = Feedback::with(['mahasiswa.user', 'konselor.user', 'booking.jadwal'])
                             ->orderBy('created_at', 'desc')
                             ->get();

        return view('admin.kelola-feedback', compact('feedbacks'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $feedback = Feedback::with(['mahasiswa.user', 'konselor.user', 'booking.jadwal'])->findOrFail($id);
        // Anda mungkin ingin memuat daftar mahasiswa dan konselor jika form edit memungkinkan perubahan ini
        $mahasiswas = Mahasiswa::with('user')->get();
        $konselors = Konselor::with('user')->get();
        return view('admin.edit-feedback', compact('feedback', 'mahasiswas', 'konselors'));
    }

    /**
     * Update the specified resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Unauthorized. Only admin can update feedback.');
        }

        $feedback = Feedback::findOrFail($id);
        $request->validate([
            'komentar'      => 'required|string|max:1000', // Sesuaikan dengan panjang kolom di DB
            'rating'        => 'required|integer|min:1|max:5',
            // Jika Anda ingin mengizinkan perubahan mahasiswa/konselor/booking_id, tambahkan validasi di sini
            // 'nim'           => 'required|exists:mahasiswas,nim', // Perhatikan jika Anda menggunakan 'nim' sebagai FK
            // 'konselor_id'   => 'required|exists:konselors,id',
            // 'booking_id'    => 'required|exists:booking,id',
        ]);

        $feedback->update($request->only(['komentar', 'rating'])); // Hanya update komentar dan rating
        // Jika ada kolom lain yang ingin diupdate oleh admin (misalnya nim, konselor_id, booking_id)
        // Pastikan validasi dan logika di atas sudah disesuaikan.
        // feedback->update($request->only(['nim', 'konselor_id', 'komentar', 'rating', 'booking_id']));

        return redirect()->route('manage.feedback')->with('success', 'Feedback berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Unauthorized. Only admin can delete feedback.');
        }

        $feedback = Feedback::findOrFail($id);
        $feedback->delete();
        return redirect()->route('manage.feedback')->with('success', 'Feedback berhasil dihapus.');
    }
}